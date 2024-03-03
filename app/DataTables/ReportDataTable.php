<?php

namespace App\DataTables;

use App\Models\Report;
use App\Models\GeneralInfo;
use App\Datatables\GeneralDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

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
            ->rawColumns(['action' , 'receipt', 'jalaliMonth'])
            ->addColumn('jalaliMonth', function (Report $report){

                $generalInfo = GeneralInfo::where('id', $report->general_info_id)->first();

                return $this->dataTable->jalaliMonth($generalInfo->jalaliMonth);

            })->filterColumn('jalaliMonth', function ($query, $keyword) {
                // Define a mapping of Persian month names to their corresponding numbers
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
                if (isset($monthMap[$keyword])) {
                    // If it exists, convert the Persian month name to its corresponding number
                    $monthNumber = $monthMap[$keyword];
            
                    // Apply the filter based on the month number
                    return $query->whereHas('generalInfo', function ($query) use ($monthNumber) {
                        $query->where('jalaliMonth', $monthNumber);
                    });
                }
            })
            ->addColumn('jalaliYear', function (Report $report){

                $generalInfo = GeneralInfo::where('id', $report->general_info_id)->first();

                return $generalInfo->jalaliYear;
            })
            ->filterColumn('jalaliYear', function ($query, $keyword) {
                return $this->dataTable->filterColumn($query, 'general_info_id in 
                    (select id from general_infos where jalaliYear like ?)', $keyword);
            })
            ->editColumn('receipt', function(Report $report) {

                $fileUrl = asset("receipts/{$report->receipt}");

                return "<a href=\"$fileUrl\" download>دانلود رسید بانک</a>";;
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
            })->addColumn('action', function (Report $report){
                return $this->dataTable->setAction($report->id, 'report'); 
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
            Column::computed('jalaliMonth')
                ->title('ماه')
                ->searchable('true'),
            Column::computed('jalaliYear')
                ->title('سال')
                ->searchable('true'),
            $this->dataTable->setActionCol()
        ];
    }
}
