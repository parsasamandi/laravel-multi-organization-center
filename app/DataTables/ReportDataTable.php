<?php

namespace App\DataTables;

use App\Models\Report;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;
use App\Datatables\GeneralDataTable;

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
            ->rawColumns(['action' , 'receipt'])
            ->addColumn('action', function (Report $report){
                return $this->dataTable->setAction($report->id); 
            })
            ->editColumn('receipt', function(Report $report) {
                
                return "<object data='/receipts/{$report->receipt}' type='application/pdf' class='dataTablePDF' width='100%' height='auto'>
                            <p>Your browser does not support PDFs. <a href='/receipts/{$report->receipt}'>Download the PDF</a> instead.</p>
                        </object>";
            })
            ->editColumn('type', function (Report $report){
                switch ($report->type) {
                    case 0:
                        return 'گزارش حقوق کارمند';
                        break;
                    case 1:
                        return 'گزارش هزینه آموزش';
                        break;
                    case 2:
                        return 'گزارش هزینه های سلامت';
                        break;
                }
                
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Report $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Report $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->dataTable->html($this->builder(), 
                $this->getColumns(), 'report');
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
            Column::make('expenses')
                ->title('مبلغ هزینه'),
            Column::make('range')
                ->title('ردیف ها در صورت حساب بانکی'),
            Column::make('receipt')
                ->title('رسید'),
            Column::make('description')
                ->title('توضیحات'),
            Column::make('type')
                ->title('نوع'),
            $this->dataTable->setActionCol()
        ];
    }
}
