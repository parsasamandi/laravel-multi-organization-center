<?php

namespace App\DataTables;

use App\Models\PaymentTransfer;
use App\Models\Center;
use App\Providers\Convertor;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;
use Storage;
use DB;

class PaymentTransferDataTable extends DataTable {

    public $dataTable;
    public $convertor;

    public function __construct() {
        $this->dataTable = new GeneralDataTable;
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
            ->rawColumns(['action', 'center_name', 'rial_to_cad_rate'])
            ->addColumn('center_name', function ($paymentTransfer) {
                $center = Center::find($paymentTransfer->center_id);
                return $center ? $center->name_en : 'مرکز وجود ندارد';
            })
            ->addColumn('rial_to_cad_rate', function ($paymentTransfer) {
                $rate = $paymentTransfer->total_rial / $paymentTransfer->total_cad;
                return number_format($rate, 4); // Format to 1 decimal point
            })
            ->filterColumn('center_name', function ($query, $keyword) {
                $query->whereHas('center', function ($q) use ($keyword) {
                    $q->where('name_en', 'LIKE', "%$keyword%");
                });
            })
            ->editColumn('total_cad', function ($paymentTransfer) {
                return number_format($paymentTransfer->total_cad, 1); // Format to 1 decimal point
            })
            ->editColumn('total_rial', function ($paymentTransfer) {
                return number_format($paymentTransfer->total_rial, 3); // Format to 3 decimal points if they have any decimal point, do not format to decimal point at all if they have no decimal point
            })
            ->editColumn('date', function ($paymentTransfer) {
                // Convert the Jalali date to Gregorian date
                return Jalalian::fromFormat('Y-m-d', $paymentTransfer->date)->toCarbon()->format('Y-m-d');
            })
            ->addColumn('action', function ($paymentTransfer) {
                return $this->dataTable->setAction($paymentTransfer->id, "paymentTransfer");
            });
    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\PaymentTransfer $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PaymentTransfer $model)   
    {
        if (auth()->user()->type === Center::GOLESTANTEAM) {
            return $model->newQuery();
        }

        return null;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->dataTable->html($this->builder(), $this->getColumns(), 'paymentTransfer', 'english.json');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            Column::computed('date')
                ->title('Transferred Date')
                ->searchable(true)
                ->orderable(true),
            Column::computed('center_name')
                ->searchable(true)
                ->orderable(false),
            Column::make('total_cad')
                ->title('Total Payment (CAD)')
                ->orderable(true)
                ->searchable(true),
            Column::make('total_rial')
                ->title('Total Payment (RIAL)')
                ->orderable(true)
                ->searchable(true),
            Column::make('cad_to_usd_rate')
                ->title('CAD To USD Rate')
                ->orderable(false)
                ->searchable(false),
            Column::computed('rial_to_cad_rate')
                ->title('Rial To CAD Rate')
                ->searchable(true)
                ->orderable(true),
            $this->dataTable->setActionCol(),
        ];

        return $columns;
    }
        

}