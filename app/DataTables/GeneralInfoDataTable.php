<?php

namespace App\DataTables;

use App\Models\GeneralInfo;
use App\Datatables\GeneralDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

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
            ->rawColumns(['action', 'bank_statement_receipt', 'data', 'status']) 
            ->addColumn('date', function(GeneralInfo $generalInfo) {
                return $generalInfo->jalaliMonth . ' ' . $generalInfo->jalaliYear;
            })
            ->filterColumn('date', function ($query, $keyword) {

                return $query->where('jalaliYear', 'LIKE', "%{$keyword}%")
                    ->orWhere('jalaliMonth', 'LIKE', "%{$keyword}%");
  
            })
            ->editColumn('bank_statement_receipt', function(GeneralInfo $generalInfo) {

                $fileUrl = asset("receipts/{$generalInfo->bank_statement_receipt}");

                return "<a href=\"$fileUrl\" download>دانلود رسید بانک</a>";

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
                return <<<HTML
                    <a onclick="showConfirmationModal('{$generalInfo->id}')">
                        <i class="fa fa-trash text-primary" aria-hidden="true"></i>
                    </a>
                    &nbsp;
                    <a href="/generalInfo/details/{$generalInfo->id}">
                        <i class='fa fa-info-circle text-primary' aria-hidden="true"></i>
                    </a>
                HTML;
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
        return $model->where('center_id', Auth::id());
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
            Column::make('bank_statement_receipt')
                ->title('صورتحساب بانکی'),
            Column::make('bank_balance')
                ->title('موجودی حساب')
                ->orderable(false),
            Column::computed('date')
                ->title('تاریخ')
                ->searchable(true),
            Column::computed('status') 
                ->title('وضعیت'),
            $this->dataTable->setActionCol()
        ];
    }
}
