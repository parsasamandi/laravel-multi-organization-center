<?php

namespace App\DataTables;

use App\Models\Report;
use App\Models\GeneralInfo;
use App\DataTables\GeneralDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
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
            ->rawColumns(['action', 'receipt', 'date'])
            ->addColumn('date', function (Report $report) {
                $generalInfo = GeneralInfo::where('id', $report->general_info_id)->first();
                if ($generalInfo) {
                    return $generalInfo->jalaliMonth . ' ' .
                        $this->dataTable->englishToPersianNumbers($generalInfo->jalaliYear);
                }
            })->filterColumn('date', function ($query, $keyword) {
                $query->whereHas('generalInfo', function ($query) use ($keyword) {
                    $query->where('jalaliYear', 'LIKE', "%{$keyword}%")
                          ->orWhere('jalaliMonth', 'LIKE', "%{$keyword}%");
                });
            })
            ->editColumn('expenses', function(Report $report) {
                return $this->dataTable->englishToPersianNumbers($report->expenses);
            })
            ->editColumn('range', function(Report $report) {
                return $this->dataTable->englishToPersianNumbers($report->range);
            })
            ->editColumn('receipt', function(Report $report) {
                // Get the URL for the file from S3 storage
                $presignedUrl = Storage::disk('s3')->temporaryUrl($report->receipt, now()->addHours(1));

                // Return a link to the file
                return '<a href="' . $presignedUrl . '" target="_blank">بارگیری</a>';
            })
            ->editColumn('type', function (Report $report) {
                switch ($report->type) {
                    case 0:
                        return 'هزینه حقوق کارمندان';
                    case 1:
                        return 'هزینه آموزش';
                    case 2:
                        return 'هزینه های سلامت';
                }
            })->addColumn('status', function(Report $report) {
                switch($report->statuses->status) {
                    case 0:
                        return 'تایید نشده';
                    case 1:
                        return 'تایید شده';
                }
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

        if ($user && $user->type === 1) {
            return $model->newQuery();
        }

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
                ->title('ردیف هزینه در صورتحساب'),
            Column::make('receipt')
                ->title('رسید'),
            Column::make('type')
                ->title('نوع'),
            Column::computed('date')
                ->title('تاریخ')
                ->searchable(true),
            Column::computed('status')
                ->title('وضعیت'),
            $this->dataTable->setActionCol()
        ];
    }
}
