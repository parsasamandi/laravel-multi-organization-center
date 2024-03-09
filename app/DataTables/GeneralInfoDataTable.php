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
            ->rawColumns(['action', 'bank_statement_receipt', 'data']) 
            ->addColumn('date', function(GeneralInfo $generalInfo) {
                return $this->dataTable->jalaliMonth($generalInfo->jalaliMonth) . ' ' . $generalInfo->jalaliYear;
            })
            ->filterColumn('date', function ($query, $keyword) {

                $monthMap = [
                    'فروردین' => 1,
                    'اردیبهشت' => 2,
                    'خرداد' => 3,
                    'تیر' => 4,
                    'مرداد' => 5,
                    'شهریور' => 6,
                    'مهر' => 7,
                    'آبان' => 8,
                    'آذر' => 9,
                    'دی' => 10,
                    'بهمن' => 11,
                    'اسفند' => 12,
                ];
    
                return $query->where('jalaliYear', 'LIKE', "%{$keyword}%")
                    ->orWhere(function ($query) use ($keyword, $monthMap) {
                        $monthNumeric = $monthMap[$keyword] ?? null;
                        if ($monthNumeric !== null) {
                            $query->where('jalaliMonth', $monthNumeric);
                        }
                });
            })
            ->editColumn('bank_statement_receipt', function(GeneralInfo $generalInfo) {

                $fileUrl = asset("receipts/{$generalInfo->bank_statement_receipt}");

                return "<a href=\"$fileUrl\" download>دانلود رسید بانک</a>";
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
                ->title('چاپ حساب بانکی'),
            Column::make('bank_balance')
                ->title('موجودی بانکی')
                ->orderable(false),
            Column::computed('date')
                ->title('تاریخ')
                ->searchable(true),
            $this->dataTable->setActionCol()
        ];
    }
}
