<?php

namespace App\DataTables;

use App\Models\GeneralInfo;
use App\DataTables\GeneralDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use App\Providers\Action;
use App\Models\Center;
use Storage;


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
                return $center->name;
            })
            ->filterColumn('center_name', function ($query, $keyword) {
                $query->whereHas('center', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->addColumn('date', function(GeneralInfo $generalInfo) {
                return $this->dataTable->jalaliMonth($generalInfo->jalaliMonth) . ' ' . $this->dataTable->englishToPersianNumbers($generalInfo->jalaliYear);
                return $generalInfo->jalaliMonth . ' ' .
                    $this->dataTable->englishToPersianNumbers($generalInfo->jalaliYear);
            })
            ->filterColumn('date', function ($query, $keyword) {
                // Split the keyword into year and month
                $parts = explode(' ', $keyword);
            
                // Check if $parts has at least two elements
                if (count($parts) >= 2) {
                    $jalaliYear = $parts[0];
                    $jalaliMonthString = $parts[1]; // Assuming the month is provided as a string like "فروردین", "اردیبهشت", etc.
            
                    $action = new Action();
                    // Map Jalali month name to its corresponding number
                    $jalaliMonth = $action->numberTojalaliMonth($jalaliMonthString);
                    
                    // Query for records matching the provided year and month
                    $query->whereHas('generalInfo', function ($query) use ($jalaliYear, $jalaliMonth) {
                        $query->where('jalaliYear', $jalaliYear)
                              ->where('jalaliMonth', $jalaliMonth);
                    });
                } 
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

        if ($center && $center->type === Center::GOLESTANTEAM) {
            return $model->newQuery();
        }

        return $model->where('center_id', $center->id);
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
        return [
            $this->dataTable->getIndexCol(),
            Column::computed('center_name')
                ->title('نام مرکز')
                ->searchable(true)
                ->orderable(false),
            Column::make('bank_statement_receipt')
                ->title('صورتحساب بانکی'),
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
    }
}
