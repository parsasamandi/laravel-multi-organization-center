<?php

namespace App\DataTables;

use App\Models\Report;
use App\Models\GeneralInfo;
use App\DataTables\GeneralDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use App\Models\Center;
use App\Providers\Convertor;
use Storage;

class ReportDataTable extends DataTable
{
    public $dataTable;

    public function __construct() {
        $this->dataTable = new GeneralDataTable();
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
            ->addIndexColumn()
            ->rawColumns(['action', 'receipt', 'date', 'center_name'])
            ->addColumn('center_name', function(Report $report) {
                $center = Center::find($report->center_id);
                return $center ? $center->name : 'مرکز وجود ندارد';
            })
            ->addColumn('date', function (Report $report) {
                $generalInfo = GeneralInfo::find($report->general_info_id);
                if ($generalInfo) {
                    return $this->dataTable->jalaliMonth($generalInfo->jalaliMonth) . ' ' .
                        $this->dataTable->englishToPersianNumbers($generalInfo->jalaliYear);
                }
            })
            ->filterColumn('date', function ($query, $keyword) {
                $convertor = new Convertor();
                $query->whereHas('generalInfo', function ($query) use ($keyword, $convertor) {
                    $jalaliMonth = $convertor->numberTojalaliMonth($keyword);
                    $jalaliYear = $convertor->persianToEnglishDecimal($keyword);
                    $query->where('jalaliMonth', 'LIKE', "%{$jalaliMonth}%")
                          ->orWhere('jalaliYear', 'LIKE', "%{$jalaliYear}%");
                });
            })
            ->orderColumn('date', function ($query, $direction) {
                $generalInfosExists = GeneralInfo::exists();
                if ($generalInfosExists) {
                    $query->join('general_infos', 'reports.general_info_id', '=', 'general_infos.id')
                        ->orderBy('general_infos.jalaliYear', $direction)
                        ->orderBy('general_infos.jalaliMonth', $direction)
                        ->select('reports.*')
                        ->where('reports.center_id', Auth::id()); // Explicitly specify the table name
                } else {
                    $query->orderBy('reports.date', $direction)
                        ->where('reports.center_id', Auth::id()); // Explicitly specify the table name
                }
            })
            ->editColumn('expenses', function(Report $report) {
                return $this->dataTable->englishToPersianNumbers($report->expenses);
            })
            ->editColumn('range', function(Report $report) {
                return $this->dataTable->englishToPersianNumbers($report->range);
            })
            ->editColumn('receipt', function(Report $report) {
                $presignedUrl = Storage::disk('s3')->temporaryUrl('receipts/' . $report->receipt, now()->addHours(1));
                return '<a href="' . $presignedUrl . '" target="_blank">دانلود</a>';
            })
            ->editColumn('type', function (Report $report) {
                switch ($report->type) {
                    case 0: return 'هزینه حقوق کارمندان';
                    case 1: return 'هزینه آموزش';
                    case 2: return 'هزینه های سلامت';
                    case 3: return 'هزینه های غذا';
                    case 4: return 'هزینه های پوشاک';
                    case 5: return 'هزینه های دیگر یک';
                    case 6: return 'هزینه های دیگر دو';
                }
            })
            ->orderColumn('type', function ($query, $direction) {
                $query->orderBy('type', $direction);
            })
            ->addColumn('status', function(Report $report) {
                return $report->statuses->status == 1 ? 'تایید شده' : 'تایید نشده';
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
            $this->dataTable->getIndexCol(),
            Column::computed('date')
                ->title('تاریخ')
                ->searchable(true)
                ->orderable(true),
            Column::make('type')
                ->title('نوع هزینه')
                ->orderable(true)
                ->searchable(true),
            Column::make('expenses')
                ->title('مبلغ هزینه')
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
            array_splice($columns, 1, 0, [
                Column::computed('center_name')
                    ->title('نام مرکز')
                    ->searchable(true)
                    ->orderable(false),
            ]);
        }

        return $columns;
    }
}
