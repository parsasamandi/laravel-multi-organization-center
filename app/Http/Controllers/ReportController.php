<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Datatables\ReportDataTable;
use App\Providers\SuccessMessages;
use App\Providers\Action;
use App\Models\Report;
use App\Models\GeneralInfo;
use App\Models\Status;
use Yajra\DataTables\Html\Builder;
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

        return view('report.list', $vars);
    }

    // Rendering Report Table
    public function reportTable(ReportDataTable $reportTable) {
        return $reportTable->render('report.list'); 
    }


    // Insert
    public function store(StoreReportRequest $request) {

        $generalInfo = GeneralInfo::where('jalaliMonth', $request->get('jalaliMonth'))
                            ->where('jalaliYear', $request->get('jalaliYear'))
                            ->where('center_id', Auth::id())->first();

        if($generalInfo) {

            $receipt = $request->file('receipt');
            $file = $receipt->getClientOriginalName();
            $receipt->move(public_path('receipts'), $file);

            $report = Report::create(
                ['expenses' => $request->get('expenses'), 'range' => $request->get('range'), 
                'receipt' => $file, 'description' => $request->get('description'), 
                'type' => $request->get('type'), 'center_id' => Auth::id(), 
                'general_info_id' => $generalInfo->id
            ]);

            // Storing General info's status
            $report->statuses()->create(
                ['status' => Status::NOTCONFIRMED, 'status_type' => Report::class]
            );

        } else {
            return response()->json(['success' => false, 
                'message' => '<div class="alert alert-danger">برای تاریخ انتخاب شده "گزارش کلی" وارد نشده است</div>']); 

        }

        return $this->getAction($request->get('button_action'));

    }

    // Delete
    public function delete($id) {

        $report = Report::findOrFail($id);

        return $this->action->deleteWithFile(Report::class, $id, $report->receipt);
    }

    // Edit
    public function edit($id) {

        $report = Report::find($id);

        if ($report) {
            $generalInfo = GeneralInfo::find($report->general_info_id);

            // Create an array with report properties
            $values = $report->toArray();
            
            // Add jalaliMonth and jalaliYear from the associated GeneralInfo model
            if ($generalInfo) {

                $values['jalaliMonth'] = $generalInfo->jalaliMonth;
                $values['jalaliYear'] = $generalInfo->jalaliYear;
            }

            return view('report.edit')->with('report', $values); 

        } else {
            return $this->failedResponse();
        }

    }  

    // Update
    public function update(UpdateReportRequest $request) {

        $generalInfo = GeneralInfo::where('jalaliMonth', $request->get('jalaliMonth'))
                            ->where('jalaliYear', $request->get('jalaliYear'))->where('center_id', Auth::id())->first();
        if($generalInfo) {

            // Report table
            $report = Report::findOrFail($request->get('id'));

            $updateData = [
                'expenses' => $request->get('expenses'),
                'range' => $request->get('range'),
                'description' => $request->get('description'), 
                'type' => $request->get('type'), 
                'center_id' => Auth::id(),
                'general_info_id' => $generalInfo->id
            ];

            // Check if a receipt file is uploaded
            if ($request->hasFile('receipt')) {
                $receipt = $request->file('receipt');
                $file = $receipt->getClientOriginalName();
                $receipt->move(public_path('receipts'), $file);
                $updateData['receipt'] = $file; // Include file in update data
            }

            if($request->get('status') == 1) 
                $updateData['status'] = 1; // status


            // Updating the report table
            $report->update($updateData);

        } else {
            return response()->json(['success' => false, 
                'message' => '<div class="alert alert-danger">در گذشته برای تاریخ انتخاب شده "مقدمات گزارش"وارد نشده است</div>']); 
        }
    }
    
    // Details
    public function details($id) {
        return view('report.details', ['report' => Report::with('generalInfo')->findOrFail($id)]);
    }
    
     // Print
     public function printReport() {
        // Retrieve all reports from the database
        $reports = Report::all();
        
        // Pass the reports data to the printable view
        return view('vendor.datatables.print', compact('reports'));
    }
}