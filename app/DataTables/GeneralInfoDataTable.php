<?php

namespace App\DataTables;

use App\Models\GeneralInfo;
use App\DataTables\GeneralDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use App\Providers\Convertor;
use App\Models\Center;
use Storage;
use DB;

class GeneralInfoDataTable extends DataTable
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
            ->rawColumns(['action', 'bank_statement_receipt', 'data', 'status', 'center_name'])
            ->addColumn('center_name', function(GeneralInfo $generalInfo) {
                $center = Center::find($generalInfo->center_id);
                if(!$center) {
                    return 'مرکز وجود ندارد';
                }
                return $center->name;
            })
            // Fix this error | It does not workd
            ->filterColumn('center_name', function ($query, $keyword) {
                // Use whereHas to filter based on related Center model's name attribute
                $query->whereHas('center', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%$keyword%");
                });
            })
            ->addColumn('date', function(GeneralInfo $generalInfo) {
                return $this->dataTable->jalaliMonth($generalInfo->jalaliMonth) . ' ' . $this->dataTable->englishToPersianNumbers($generalInfo->jalaliYear);
                return $generalInfo->jalaliMonth . ' ' .
                    $this->dataTable->englishToPersianNumbers($generalInfo->jalaliYear);
            })
            ->filterColumn('date', function ($query, $keyword) {
                // Ensure Convertor class is available
                $convertor = new Convertor();
                // Convert Persian numbers to English numbers
                $jalaliYear = $convertor->persianToEnglishDecimal($keyword);
                // Map Jalali month name to its corresponding number
                $jalaliMonth = $convertor->numberTojalaliMonth($keyword);

                $query->join('general_infos', 'reports.general_info_id', '=', 'general_infos.id')
                    ->where(function ($query) use ($jalaliMonth, $jalaliYear) {
                        if (!empty($jalaliMonth)) {
                            $query->where('general_infos.jalaliMonth', 'LIKE', "%{$jalaliMonth}%");
                        }
                        if (is_numeric($jalaliYear)) {
                            $query->where('general_infos.jalaliYear', 'LIKE', "%$jalaliYear%");
                        }
                });
            })            
            ->orderColumn('date', function ($query, $direction) {
                $query->orderBy('jalaliYear', $direction)
                      ->orderBy('jalaliMonth', $direction);
            })
            ->editColumn('bank_balance', function(GeneralInfo $generalInfo) {
                return $this->dataTable->englishToPersianNumbers($generalInfo->bank_balance);
            })
            ->editColumn('bank_statement_receipt', function(GeneralInfo $generalInfo) {
                // Get the URL for the file from S3 storage
                $presignedUrl = Storage::disk('s3')->temporaryUrl('receipts/' . $generalInfo->bank_statement_receipt, now()->addHours(1));
                
                // Return a link to the file
                return '<a href="' . $presignedUrl . '" target="_blank">دانلود</a>';

            })->addColumn('status', function(GeneralInfo $generalInfo) {
                switch($generalInfo->statuses->status) {
                    case 0:
                        return 'تایید نشده';
                        break;
                    case 1:
                        return 'تایید شده';
                        break;
                }
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
                ->title('موجودی حساب'),
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
