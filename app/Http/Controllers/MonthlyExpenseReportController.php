<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Providers\SuccessMessages;
use App\Http\Requests\StoreAparatRequest;
use App\Datatables\GeneralInfoDataTable;
use App\Datatables\ReportDataTable;

class MonthlyExpenseReportController extends Controller
{
    // DataTable to blade
    public function list() {
        // General Info Table
        $GeneralInfoTable = new GeneralInfoDataTable;
        // Report Table
        $ReportTable = new ReportDataTable;

        $vars['generalInfoTable'] = $GeneralInfoTable->html();

        $vars['reportTable'] = $ReportTable->html();

        return view('monthlyExpenseReport', $vars);
    }

    // Rendering General Info Table
    public function generalInfoTable(GeneralInfoDataTable $generalInfoTable) {
        return $generalInfoTable->render('monthlyReportExpense.generalInfo'); 
    }

    // Rendering Report Table
    public function reportTable(ReportDataTable $reportTable) {
        return $reportTable->render('monthlyReportExpense.report'); 
    }


    // Insert
    public function store(Request $request) {
        return $this->getAction($request->get('button_action'));
    }

    // Delete
    public function delete($id) {
        return $action->delete(Media::class,$id);
    }

    // Edit
    public function edit(Request $request) {
        return $action->edit(Media::class,$request->get('id'));
    }
}
