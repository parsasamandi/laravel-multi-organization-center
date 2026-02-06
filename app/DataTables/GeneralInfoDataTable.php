<?php

namespace App\DataTables;

use App\Models\GeneralInfo;
use App\Models\Center;
use App\Models\Status;
use App\DataTables\GeneralDataTable;
use App\Providers\Convertor;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use Storage;
use DB;

class GeneralInfoDataTable extends DataTable
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
            ->rawColumns(['action', 'bank_statement_receipt', 'data', 'status', 'center_name'])
            
            ->addColumn('center_name', function(GeneralInfo $generalInfo) {
                $center = Center::find($generalInfo->center_id);
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
                    if ($jalaliMonth) 
                        $query->where('jalaliMonth', '=', $jalaliMonth);
                    
                    if ($jalaliYear) 
                        $query->orWhere('jalaliYear', '=', $jalaliYear);

                } else {
                    $query->whereHas('center', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    });
                }
            })
            ->addColumn('date', function(GeneralInfo $generalInfo) {
                return $this->convertor->convertJalaliMonth($generalInfo->jalaliMonth) 
                    . ' ' . $this->convertor->englishToPersianDecimal($generalInfo->jalaliYear);
            })                     
            ->orderColumn('date', function ($query, $direction) {
                $query->orderBy('jalaliYear', $direction)
                      ->orderBy('jalaliMonth', $direction);
            })
            ->editColumn('bank_balance', function(GeneralInfo $generalInfo) {
                return $this->convertor->englishToPersianDecimal
                    (number_format($generalInfo->bank_balance, 0, '', ','));
            })
            ->editColumn('bank_statement_receipt', function (GeneralInfo $generalInfo) {
                $filePath = 'bank_statement/' . $generalInfo->bank_statement_receipt;
                $fileName = $generalInfo->bank_statement_receipt;
            
                // Get the presigned URL with Content-Disposition header
                $presignedUrl = $this->dataTable->getPresignedUrlWithContentDisposition($filePath, $fileName);
                // Return a link to the file
                return '<a href="' . $presignedUrl . '" target="_blank">دانلود</a>';
                
            })->addColumn('status', function(GeneralInfo $generalInfo) {
                return match ($generalInfo->statuses->status) {
                    Status::NOTCONFIRMED => 'بررسی نشده',
                    Status::SUCCESSFUL => 'موفق',
                    Status::UNSUCCESSFUL => 'ناموفق',
                    default => 'وضعیت نامشخص',
                };
            })
            ->addColumn('action', function(GeneralInfo $generalInfo) {
                return $this->dataTable->setAction($generalInfo->id, 'generalInfo');
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\GeneralInfoDataTable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(GeneralInfo $model)
    {
        $center = Auth::user();

        if ($center && $center->type == Center::GOLESTANTEAM) {
            return $model->newQuery();
        }

        return $model->newQuery()->where('general_infos.center_id', Auth::id());

    }


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->dataTable->html($this->builder(),
                $this->getColumns(), 'generalInfo');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            Column::make('bank_statement_receipt')
                ->title('صورتحساب بانکی')
                ->orderable(false),
            Column::make('bank_balance')
                ->title('موجودی حساب (ریال)'),
            Column::computed('date')
                ->title('تاریخ')
                ->searchable(true)
                ->orderable(true),
            Column::computed('status')
                ->title('وضعیت'),
            $this->dataTable->setActionCol()
        ];

        if(Auth::user()->type == Center::GOLESTANTEAM)
            // Insert the 'code' column as the firstcolumn
            array_splice($columns, 0, 0, [
                Column::computed('center_name')
                ->title('نام مرکز')
                ->searchable(true)
                ->orderable(false),
            ]);

        return $columns;
    }

}
