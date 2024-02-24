<?php

namespace App\DataTables;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class GeneralDataTable 
{
    /**
     * html builder | dataTable builder / table columns / table name
     * 
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html($builder, $columns, $table)
    {
        return $builder
            ->setTableId("{$table}Table")
            ->minifiedAjax(route("{$table}.list.table"))
            ->columns($columns)
            ->columnDefs(
                [
                    ["className" => 'dt-center text-center', "target" => '_all'],
                ]
            )
            ->searching(true)
            ->lengthMenu([10,25,40])
            ->info(false)
            ->ordering(true)
            ->responsive(true)
            ->pageLength(10)
            ->dom('Bfrtip')
            ->orderBy(1)
            ->language(asset('js/persian.json'));
    }

    // Computed column in datatables for delete,update,insertion
    public function setAction($id) {
        return <<<ATAG
                    <a onclick="showConfirmationModal('{$id}')">
                        <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                    </a>
                    &nbsp;
                    <a onclick="showEditModal('{$id}')">
                        <i class="fa fa-edit text-danger" aria-hidden="true"></i>
                    </a>
                ATAG;
    }

    /**
     * Get index column(0 | 1 | 2 .....).
     */
    public function getIndexCol()
    {
        return Column::make('DT_RowIndex')
                ->title('#')
                ->searchable(false)
                ->orderable(false);
    } 

    /**
     * Get action column.
     */
    public function setActionCol()
    {
        return Column::computed('action') // This Column is not in database
                ->exportable(false)
                ->searchable(false)
                ->printable(false)
                ->orderable(false)
                ->title("حذف | ویرایش");
    }  


    // Set status column
    public function setStatusCol($status) {

        if($status == Status::ACTIVE) return "موجود";
        else if($status == Status::INACTIVE) return 'ناموجود';
    }

    // Filter status column 
    public function filterStatusCol($query, $rkeyword) {

        switch($keyword) {
            case 'موجود': $keyword = 0; 
            break;
            case 'ناموجود': $keyword = 1;
        }

        return $this->filterColumn($query, 'id in (select status_id from 
                        status where status like ?)', $keyword);
    }

    // Filter category column
    public function filterCategoryCol($query, $keyword) {

        return $this->filterColumn($query, 'category_id in 
                (select id from categories where name like ?)', $keyword);
    } 

    // Filter product column
    public function filterProductCol($query, $keyword) {

        return $this->filterColumn($query, 'product_id in 
                    (select id from product where name like ?)', $keyword);

    }

    // Filter column
    public function filterColumn($query, $sql, $keyword) {
        return $query->whereRaw($sql, ["%{$keyword}%"]);
    }
}
