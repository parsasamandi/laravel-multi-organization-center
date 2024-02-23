<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreReportRequest;
use App\Datatables\ReportDataTable;
use App\Providers\SuccessMessages;
use App\Providers\Action;
use App\Models\Report;
use App\Models\GeneralInfo;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class ReportController extends Controller
{
    public $action;

    public function __construct() {
        $this->action = new Action();
    }

    // DataTable to blade
    public function list() {
        // Report Table
        $ReportTable = new ReportDataTable;

        $vars['reportTable'] = $ReportTable->html();

        return view('reportList', $vars);
    }

    // Rendering Report Table
    public function reportTable(ReportDataTable $reportTable) {
        return $reportTable->render('report'); 
    }


    // Insert
    public function store(StoreReportRequest $request) {

        if($request->hasFile('receipt')) {

            $generalInfo = GeneralInfo::where('jalaliMonth', $request->get('jalaliMonth'))
                                ->where('jalaliYear', $request->get('jalaliYear'))->first();
            if($generalInfo) {

                $receipt = $request->file('receipt');
                $file = $receipt->getClientOriginalName();
                $receipt->move(public_path('receipts'), $file);

                Report::updateOrCreate(
                    ['id' => $request->get('id')],
                    ['expenses' => $request->get('expenses'), 'range' => $request->get('range'), 
                    'receipt' => $file, 'description' => $request->get('description'), 
                    'type' => $request->get('type'), 'center_id' => Auth::id(), 
                    'general_info_id' => $generalInfo->id
                ]);

            } else {
                return response()->json(['success' => true, 'message' => '<div class="alert alert-danger">برای تاریخ انتخاب شده اطلاعات کلی وارد نشده است</div>'], Response::HTTP_CREATED); 
            }

        }
        return $this->getAction($request->get('button_action'));

    }

    // Delete
    public function delete($id) {
        return $this->action->deleteWithFile(Report::class, $request->get('id'), 'receipt');
    }

    // Edit
    public function edit(Request $request) {
        $report = Report::find($request->get('id'));

        if ($report) {
            $generalInfo = GeneralInfo::find($report->general_info_id);
            $values = $report->toArray();
            if ($generalInfo) {
                $values['jalaliMonth'] = $generalInfo->jalaliMonth;
                $values['jalaliYear'] = $generalInfo->jalaliYear;
            }

            return response()->json($values);
        } else {
            return $this->failedResponse();
        }
    }    
}