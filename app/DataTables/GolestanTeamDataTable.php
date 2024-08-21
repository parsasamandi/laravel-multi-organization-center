<?php

namespace App\DataTables;

use App\Models\Center;
use App\DataTables\GeneralDataTable;
use App\Providers\Convertor;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Auth;

class GolestanTeamDataTable extends DataTable
{
    public $dataTable;
    public $convertor;

    public function __construct() {
        $this->dataTable = new GeneralDataTable();
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
            ->rawColumns(['action'])
            ->addColumn('action', function (Center $center){
                return $this->dataTable->setAction($center->id);
            })->editColumn('phone_number', function (Center $center) {
                return $this->convertor->englishToPersianDecimal($center->phone_number);
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

        if ($center && $center->type == Center::GOLESTANTEAM) {
            return $model->where('type', Center::GOLESTANTEAM);
        }

        return $model->where('type', Center::CENTER);

    }   

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->dataTable->html($this->builder(),
                $this->getColumns(), 'golestanTeam');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('name')
                ->title('نام شخص')
                ->orderable(false),
            Column::make('email')
                ->title('ایمیل')
                ->orderable(false),
            Column::make('phone_number')
                ->title('شماره تلفن')
                ->orderable(false),
            $this->dataTable->setActionCol()
        ];
    }
}
