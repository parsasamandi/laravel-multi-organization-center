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
                return "<img src=/receipts/" . $report->receipt . " class='dataTableImage' />";

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
