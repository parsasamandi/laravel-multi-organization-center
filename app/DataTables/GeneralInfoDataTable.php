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
            ->rawColumns(['action', 'bank_statement_receipt']) 
            ->editColumn('jalaliMonth', function(GeneralInfo $generalInfo) {
                return $this->dataTable->jalaliMonth($generalInfo->jalaliMonth);
            })
            ->editColumn('jalaliYear', function(GeneralInfo $generalInfo) {
                return $generalInfo->jalaliYear;
            })
            ->editColumn('bank_statement_receipt', function(GeneralInfo $generalInfo) {
                return "<object data='/receipts/{$generalInfo->bank_statement_receipt}' type='application/pdf' class='dataTablePDF' width='100%' height='auto'>
                            <p>Your browser does not support PDFs. <a href='/receipts/{$generalInfo->bank_stateemnt_receipt}'>Download the PDF</a> instead.</p>
                        </object>";
            })
            ->addColumn('action', function(GeneralInfo $generalInfo) {
                return $this->dataTable->setAction($generalInfo->id); 
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
                ->title('پرینت حساب بانکی'),
            Column::make('bank_balance')
                ->title('موجودی بانکی')
                ->orderable(false),
            Column::make('jalaliMonth')
                ->title('ماه'),
            Column::make('jalaliYear')
                ->title('سال'),
            $this->dataTable->setActionCol()
        ];
    }
}
