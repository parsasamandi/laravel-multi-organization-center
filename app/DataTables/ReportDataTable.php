<?php

namespace App\DataTables;

use App\Models\Report;
use App\Models\GeneralInfo;
use App\Models\Status;
use App\DataTables\GeneralDataTable;
use App\Models\Center;
use App\Providers\Convertor;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use Storage;
use DB;

class ReportDataTable extends DataTable
{
    public $dataTable;
    public $convertor;

    public function __construct() {
        $this->dataTable = new GeneralDataTable();
        $this->convertor = new Convertor();
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->rawColumns(['action', 'receipt', 'center_name', 'date'])
            ->addColumn('center_name', function (Report $report) {
                $center = Center::find($report->center_id);
                return $center ? $center->name : 'مرکز وجود ندارد';
            })
            ->filterColumn('center_name', function ($query, $keyword) {
                
                // Convert the keyword from Persian to English numbers
                $keyword = $this->convertor->persianToEnglishDecimal(trim($keyword));

                $persianMonths = [
                    "فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور",
                    "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند"
                ];
                
                if (in_array($keyword, $persianMonths) || is_numeric($keyword)) { 
                    // Initialize variables
                    $jalaliMonth = null;
                    $jalaliYear = null;
                
                    // Check if the keyword contains only numbers (indicating it might be a year)
                    if (is_numeric($keyword)) {
                        $jalaliYear = (int) $keyword;
                    } else {
                        // Split the keyword by spaces to determine if it contains both month and year
                        $parts = preg_split('/\s+/', $keyword);
                
                        foreach ($parts as $part) {
                            if (is_numeric($part)) {
                                $jalaliYear = (int) $part;
                            } else {
                                $jalaliMonth = (int) $this->convertor->convertJalaliMonth($part);
                            }
                        }
                    }

                    $query->whereHas('generalInfo', function ($subQuery) use ($jalaliMonth, $jalaliYear) {
                        $subQuery->where(function ($q) use ($jalaliMonth, $jalaliYear) {
                            if (!empty($jalaliMonth)) {
                                $q->where('general_infos.jalaliMonth', '=', "{$jalaliMonth}");
                            }
                            
                            if (!empty($jalaliYear)) {
                                // Use orWhere for jalaliYear to ensure it is applied alongside jalaliMonth
                                $q->orWhere('general_infos.jalaliYear', '=', "{$jalaliYear}");
                            }
                        });
                    });
                } else {
                    $query->whereHas('center', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    });
                }
            })
            ->addColumn('date', function (Report $report) {
                $generalInfo = GeneralInfo::find($report->general_info_id);
                if ($generalInfo) 
                    return $this->convertor->convertJalaliMonth($generalInfo->jalaliMonth) . ' ' .
                        $this->convertor->englishToPersianDecimal($generalInfo->jalaliYear);

                return null;
            })
            ->orderColumn('date', function ($query, $direction) {
                if (GeneralInfo::exists()) {
                    $query->join('general_infos', 'reports.general_info_id', '=', 'general_infos.id')
                        ->orderBy('general_infos.jalaliYear', $direction)
                        ->orderBy('general_infos.jalaliMonth', $direction)
                        ->select('reports.*');
                }
            })
            ->editColumn('expenses', function (Report $report) {
                return $this->convertor->englishToPersianDecimal
                    (number_format($report->expenses, 0, '', ','));
            })
            ->editColumn('range', function (Report $report) {
                return $this->convertor->englishToPersianDecimal($report->range);
            })
            ->editColumn('receipt', function (Report $report) {
                $filePath = 'receipt/' . $report->receipt;
                $fileName = $report->receipt;
            
                // Get the presigned URL with Content-Disposition header
                $presignedUrl = $this->dataTable->getPresignedUrlWithContentDisposition($filePath, $fileName);
                // Return a link to the file
                return '<a href="' . $presignedUrl . '" target="_blank">دانلود</a>';
            })
            ->editColumn('type', function (Report $report) {
                return match ($report->type) {
                    0 => 'هزینه حقوق کارمندان',
                    2 => 'هزینه های سلامت',
                    3 => 'هزینه های غذا',
                    4 => 'هزینه های پوشاک',
                    5 => 'هزینه های دیگر',
                    9 => 'هزینه آموزش - نیمسال اوله',
                    8 => 'هزینه آموزش - نیمسال دوم',
                    7 => 'هزینه آموزش تابستان',
                    6 => 'هزینه آموزش اقلام مهر',
                    1 => 'هزینه آموزش - دیگر',
                    default => 'نوع هزینه نامشخص',
                };
            })
            ->orderColumn('type', function ($query, $direction) {
                $query->orderBy('type', $direction);
            })
            ->addColumn('status', function (Report $report) {
                return match ($report->statuses->status) {
                    Status::NOTCONFIRMED => 'بررسی نشده',
                    Status::SUCCESSFUL => 'موفق',
                    Status::UNSUCCESSFUL => 'ناموفق',
                    default => 'وضعیت نامشخص',
                };
            })
            ->addColumn('action', function (Report $report) {
                return $this->dataTable->setAction($report->id, 'report');
            });
    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Report $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Report $model) {
        $user = Auth::user();

        if ($user && $user->type == Center::GOLESTANTEAM) {
            return $model->newQuery();
        }

        return $model->newQuery()->where('reports.center_id', Auth::id());
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->dataTable->html($this->builder(), $this->getColumns(), 'report');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            Column::computed('date')
                ->title('تاریخ')
                ->searchable(true)
                ->orderable(true),
            Column::make('type')
                ->title('نوع هزینه')
                ->orderable(true)
                ->searchable(true),
            Column::make('expenses')
                ->title('مبلغ هزینه (ریال)')
                ->orderable(true)
                ->searchable(true),
            Column::make('range')
                ->title('ردیف در صورتحساب')
                ->orderable(false)
                ->searchable(false),
            Column::make('receipt')
                ->title('فاکتور')
                ->orderable(false),
            Column::computed('status')
                ->title('وضعیت')
                ->orderable(false)
                ->searchable(true),
            $this->dataTable->setActionCol()
        ];

        if (Auth::user()->type == Center::GOLESTANTEAM) {
            array_splice($columns, 0, 0, [
                Column::computed('center_name')
                    ->title('نام مرکز')
                    ->searchable(true)
                    ->orderable(false),
            ]);
        }

        return $columns;
    }
}
