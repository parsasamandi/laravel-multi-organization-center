<?php

namespace App\DataTables;

use App\Models\Center;
use App\DataTables\GeneralDataTable;
use App\Providers\Convertor;
use App\Models\PaymentTransfer;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;
use Auth;
use DB;

class CenterStatusReportDataTable extends DataTable
{
    public $dataTable;
    public $convertor;

    public function __construct()
    {
        $this->dataTable = new GeneralDataTable();
        $this->convertor = new Convertor();
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->rawColumns(['action', 'bank_balance', 'general_info_status', 'report_status', 'total_expenses', 'total_payment_rial', 'remained_differences'])
            ->editColumn('center_name', function ($row) {
                return $this->convertor->englishToPersianDecimal($row->center_name);
            })
            ->editColumn('report_month', function ($row) {
                $numericMonth = $row->month_id; // Ensure you're using the numeric month ID
                $jalaliMonth = $this->convertor->convertJalaliMonth((int)$numericMonth);
                $jalaliYear = $this->convertor->englishToPersianDecimal($row->report_year);
            
                return $jalaliMonth . ' ' . $jalaliYear;
            })
            ->editColumn('bank_balance', function ($row) {
                return $this->convertor->englishToPersianDecimal($row->bank_balance);
            })
            ->editColumn('total_payment_rial', function ($row) {
                if ($row->total_payment_rial == 0)
                    return "<span class='text-danger'>ناموجود</span>";

                return $this->convertor->englishToPersianDecimal($row->total_payment_rial);
            })
            ->editColumn('total_expenses', function ($row) {
                return $this->convertor->englishToPersianDecimal($row->total_expenses);
            })
            ->addColumn('remained_differences', function ($row) {

                if ($row->total_expenses) {

                    $difference = $row->total_payment_rial - $row->total_expenses;
                    $convertDifference = $this->convertor->englishToPersianDecimal($difference);
                
                    // Highlight if difference is above 10M
                    if ($difference > 10_000_000) {
                        return "<span class='text-danger'>" . $convertDifference . "</span>";
                    }

                    return $convertDifference;
                }

                return  "<span class='text-danger'>ناموجود</span>";

            })
            ->editColumn('report_status', function ($row) {
                return $row->report_status == "موفق" ? "<span class='text-success'>{$row->report_status}</span>" :
                    "<span class='text-danger'>{$row->report_status}</span>";
            })
            ->editColumn('general_info_status', function ($row) {
                return $row->general_info_status == "موفق" ? "<span class='text-success'>{$row->general_info_status}</span>" :
                    "<span class='text-danger'>{$row->general_info_status}</span>";
            })
            ->addColumn('action', function (Center $center){
                $detailsUrl = url('centerStatusReport/details');

                return "<a href='$detailsUrl'><i class='fa fa-info-circle text-primary' aria-hidden='true'></i></a>";
            });
    }

    public function query(Center $model)
    {
        $user = Auth::user();
        $driver = DB::getDriverName(); // Get the current database driver
    
        // Get current Jalali year and month
        $currentJalaliDate = Jalalian::now();
        $currentYear = $currentJalaliDate->getYear();
        $currentMonth = $currentJalaliDate->getMonth();
    
        // Determine ordering cast syntax based on driver
        $orderByCast = $driver === 'pgsql'
            ? DB::raw('CAST(months.id AS INTEGER)')
            : DB::raw('CAST(months.id AS UNSIGNED)');
    
        // Set up a driver-specific COALESCE for the report_year column
        if ($driver === 'pgsql') {
            $coalesceYear = DB::raw('COALESCE("gi"."jalaliYear", \'1403\') AS report_year');
        } else {
            $coalesceYear = DB::raw('COALESCE(gi.jalaliYear, 1403) AS report_year');
        }
    
        $query = $model->newQuery()
            ->crossJoin(DB::raw("(
                SELECT 1 AS id, 'فروردین' AS month_name UNION ALL
                SELECT 2, 'اردیبهشت' UNION ALL
                SELECT 3, 'خرداد' UNION ALL
                SELECT 4, 'تیر' UNION ALL
                SELECT 5, 'مرداد' UNION ALL
                SELECT 6, 'شهریور' UNION ALL
                SELECT 7, 'مهر' UNION ALL
                SELECT 8, 'آبان' UNION ALL
                SELECT 9, 'آذر' UNION ALL
                SELECT 10, 'دی' UNION ALL
                SELECT 11, 'بهمن' UNION ALL
                SELECT 12, 'اسفند'
            ) as months"))
            ->leftJoin('general_infos as gi', function ($join) use ($currentYear, $currentMonth, $driver) {
                if ($driver === 'pgsql') {
                    // PostgreSQL: use quoted identifiers for case sensitivity
                    $join->on('centers.id', '=', 'gi.center_id')
                         ->on('months.id', '=', 'gi.jalaliMonth')
                         ->where(function ($query) use ($currentYear, $currentMonth) {
                             $query->where(DB::raw('"gi"."jalaliYear"'), '<', $currentYear)
                                   ->orWhere(function ($query) use ($currentYear, $currentMonth) {
                                       $query->where(DB::raw('"gi"."jalaliYear"'), '=', $currentYear)
                                             ->where(DB::raw('"gi"."jalaliMonth"'), '<=', $currentMonth);
                                   });
                         });
                } else {
                    // MySQL: no extra quoting needed
                    $join->on('centers.id', '=', 'gi.center_id')
                         ->on('months.id', '=', 'gi.jalaliMonth')
                         ->where(function ($query) use ($currentYear, $currentMonth) {
                             $query->where('gi.jalaliYear', '<', $currentYear)
                                   ->orWhere(function ($query) use ($currentYear, $currentMonth) {
                                       $query->where('gi.jalaliYear', '=', $currentYear)
                                             ->where('gi.jalaliMonth', '<=', $currentMonth);
                                   });
                         });
                }
            })
            ->leftJoin('reports as r', 'gi.id', '=', 'r.general_info_id')
            ->leftJoin('payment_transfers as pt', function ($join) use ($driver) {
                if ($driver === 'pgsql') {
                    // PostgreSQL: use EXTRACT for date parts
                    $join->on(DB::raw('"gi"."jalaliYear"'), '=', DB::raw("EXTRACT(YEAR FROM pt.date)"))
                         ->on(DB::raw('"gi"."jalaliMonth"'), '=', DB::raw("EXTRACT(MONTH FROM pt.date)"))
                         ->on('centers.id', '=', 'pt.center_id');
                } else {
                    // MySQL: use YEAR() and MONTH()
                    $join->on('gi.jalaliYear', '=', DB::raw("YEAR(pt.date)"))
                         ->on('gi.jalaliMonth', '=', DB::raw("MONTH(pt.date)"))
                         ->on('centers.id', '=', 'pt.center_id');
                }
            })
            ->select([
                'centers.id',
                'centers.name as center_name',
                'months.month_name as report_month',
                'months.id as month_id',
                DB::raw("CASE WHEN COUNT(r.id) = 0 THEN 'ناموفق' ELSE 'موفق' END AS report_status"),
                DB::raw("CASE WHEN COUNT(gi.id) = 0 THEN 'ناموفق' ELSE 'موفق' END AS general_info_status"),
                $coalesceYear,
                DB::raw('COALESCE(COUNT(r.id), 0) AS total_reports'),
                DB::raw('COALESCE(MAX(gi.bank_balance), 0) AS bank_balance'),
                DB::raw("COALESCE(SUM(pt.total_rial), 0) as total_payment_rial"),
                DB::raw('COALESCE(SUM(r.expenses), 0) AS total_expenses'),
            ])
            ->groupBy('centers.id', 'centers.name', 'months.id', 'report_month', 'report_year')
            ->orderBy('centers.name', 'asc')
            ->orderBy($orderByCast, 'asc');
    
        if ($user && $user->type == Center::GOLESTANTEAM) {
            $query->where('centers.type', Center::CENTER);
        }
    
        return $query;
    }
    

    public function html()
    {
        return $this->dataTable->html(
            $this->builder(),
            $this->getColumns(),
            'centerStatusReport',
        );
    }

    protected function getColumns()
    {
        return [
            Column::make('center_name')->title('نام مرکز')->orderable(true)->searchable(true),
            Column::make('report_month')->title('تاریخ')->orderable(true)->searchable(true),
            Column::make('total_payment_rial')->title('میزان واریزی')->orderable(false),
            Column::make('general_info_status')->title('وضعیت صورتحساب بانکی')->orderable(false),
            Column::make('report_status')->title('وضعیت گزارش هزینه‌ها')->orderable(false),
            Column::make('bank_balance')->title('مبلغ صورتحساب بانکی')->orderable(false),
            Column::make('total_expenses')->title('جمع گزارش هزینه')->orderable(false),
            Column::computed('remained_differences')->title('اختلاف مانده‌ها')->orderable(false),
            $this->dataTable->setActionCol()
        ];
    }
}
