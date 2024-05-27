<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\DataTables\ReportDataTable;
use App\Providers\SuccessMessages;
use App\Providers\Action;
use App\Models\Report;
use App\Models\GeneralInfo;
use App\Models\Status;
use App\Models\Center;
use Yajra\DataTables\Html\Builder;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use Storage;

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

        // Dates
        if(Auth::user()->type == Center::GOLESTANTEAM) {
            $dates = GeneralInfo::select('id', 'jalaliMonth', 'jalaliYear')->get();
        } else {
            $dates = GeneralInfo::where('center_id', Auth::id())
                ->select('id', 'jalaliMonth', 'jalaliYear')
                ->get();
        }

        // Convert jalaliYear to Persian numbers
        $dates->transform(function ($date) {
            $date->jalaliYear = $this->action->englishToPersianNumbers($date->jalaliYear);
            return $date;
        });

        // Prepare other variables
        $vars = [
            'reportTable' => $ReportTable->html(),
            'dates' => $dates,
        ];

        return view('report.list', $vars);
    }

    // Rendering Report Table
    public function reportTable(ReportDataTable $reportTable) {
        return $reportTable->render('report.list');
    }


    // Insert or Update
    public function store(StoreReportRequest $request) {

        $id = $request->get('id');

        if($request->hasFile('receipt')) {
            // Getting the file
            $receipt = $request->file('receipt');
            // File name
            $center = Auth::user();

            if($center->type = Center::CENTER) 
                $file_name = 'receipts/' . $center->code . $receipt->getClientOriginalName();
            else
                $file_name = 'receipts/' . $receipt->getClientOriginalName();
            // Storing file to S3
            $receipt->storeAs('receipts', $file_name, 's3');
        }

        $report = Report::createorUpdate([
            'id' => $id,
            'expenses' => $this->action->persianToEnglishNumbers($request->get('expenses')),
            'range' => $this->action->persianToEnglishNumbers($request->get('range')),
            'receipt' => $file_name, 'description' => $request->get('description'),
            'type' => $request->get('type'), 'center_id' => Auth::id(),
            'general_info_id' => $request->get('general_info_id')
        ]);

        // Storing Report's status
        $report->statuses()->updateOrCreate(
            ['status_id' => $id, 'status' => Status::NOTCONFIRMED, 'status_type' => Report::class]
        );

        return $this->getAction($request->get('button_action'));
    }


    // Edit
    public function edit($id) {

        // $vars['report'] = Report::find($id);

        // if ($vars) {

        //     // Dates
        //     if(Auth::user()->type == Center::GOLESTANTEAM)
        //         $vars['dates'] = GeneralInfo::select('id', 'jalaliMonth', 'jalaliYear')->get();
        //     else
        //         $vars['dates'] = GeneralInfo::where('center_id', Auth::id())->select('id', 'jalaliMonth', 'jalaliYear')->get();

        //     // Pass the report data and dates to the view
        //     return view('report.edit', $vars);
        // }

        return $this->action->edit(Report::class, $request->get('id'));
        
    }

    public function confirmStatus(Request $request) {

        $report = Report::findOrFail($request->get('id'));

        // Checking if it was confirmed
        if($request->get('status') == Status::CONFIRMED) {

            // Updating General info's status into "Confirmed"
            $report->statuses()->update(
                ['status' => Status::CONFIRMED]
            );
        } else {
             // Updating General info's status into "Not confirmed"
             $report->statuses()->update(
                ['status' => Status::NOTCONFIRMED]
            );
        }

        return response()->json(['success' => true], Response::HTTP_CREATED);
    }

    // Update
    public function update(Request $request) {

        // Report table
        $report = Report::findOrFail($request->get('id'));

        $updateData = [
            'expenses' => $request->get('expenses'),
            'range' => $request->get('range'),
            'description' => $request->get('description'),
            'type' => $request->get('type'),
            'center_id' => Auth::id(),
            'general_info_id' => $request->get('general_info_id')
        ];

        // Check if a receipt file is uploaded
        if ($request->hasFile('receipt')) {

            // Deleting from storage
            Storage::disk('s3')->delete($report->receipt);

            // Getting the file
            $receipt = $request->file('receipt');
            // File name
            $file_name = 'receipts/' . $receipt->getClientOriginalName();
            // Storing file to S3
            $receipt->storeAs('receipts', $file_name, 's3');
            
            $updateData['receipt'] = $file_name; // Include file in update data
        }

        $report->update($updateData);


        return $this->getAction("update");
    }

    // Details
    public function details($id) {
        return view('report.details', ['report' => Report::with('generalInfo')->findOrFail($id)]);
    }

    // Delete
    public function delete($id) {
        
        $report = Report::find($id);
        
        // Deleting from storage
        Storage::disk('s3')->delete($report->receipt);
    
        return $this->action->delete(Report::class, $id);
    }

}
