<?php

namespace App\DataTables;

use App\Models\Center;
use App\DataTables\GeneralDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Auth;

class CenterDataTable extends DataTable
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
            ->rawColumns(['action'])
            ->addColumn('action', function (Center $center){
                return $this->dataTable->setAction($center->id);
            })->editColumn('phone_number', function (Center $center) {
                return $this->dataTable->englishToPersianNumbers($center->phone_number);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Center $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Center $model)
    {
        $center = Auth::user();

        if ($center && $center->type === 1) {
            return $model->newQuery();
        }

        return $model->where('id', $center->id);

    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->dataTable->html($this->builder(),
                $this->getColumns(), 'center');
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
            Column::make('code')
                ->title('کد مرکز'),
            Column::make('name')
                ->title('نام مرکز'),
            Column::make('email')
                ->title('ایمیل'),
            Column::make('phone_number')
                ->title('شماره تلفن'),
            $this->dataTable->setActionCol()
        ];
    }
}
