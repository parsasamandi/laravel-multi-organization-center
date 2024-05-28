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

        // Prepare other variables
        $vars['reportTable'] = $ReportTable->html();

        return view('report.list', $vars);
    }

    // Rendering Report Table
    public function reportTable(ReportDataTable $reportTable) {
        return $reportTable->render('report.list');
    }


    // Insert or Update
    public function store(StoreReportRequest $request) {

        $data = [
            'expenses' => $request->get('expenses'),
            'range' => $request->get('range'),
            'type' => $request->get('type'),
            'description' => $request->get('description'),
            'center_id' => Auth::user()->id
        ];
    
        // Get center information and Jalali year/month
        $center = Auth::user();
        $jalaliYear = $request->get('jalaliYear');
        $jalaliMonth = $request->get('jalaliMonth');
    
        // Check general info existence based on center type
        $generalInfo = GeneralInfo::where(function ($query) use ($center, $jalaliYear, $jalaliMonth) {
            if ($center->type == Center::CENTER) {
                $query->where('center_id', $center->id);
            }
            $query->where('jalaliYear', $jalaliYear)->where('jalaliMonth', $jalaliMonth);
        })->first();
    
        if (!$generalInfo) {
            return response()->json(['success' => false, 'message' => '<div class="alert alert-danger">برای سال و ماه انتخاب شده، گزارش صورتحساب قبلا وارد نشده است. لطفا ابتدا گزارش صورتحساب را برای این تاریخ وارد نمایید.</div>']);
        }
    
        $data['general_info_id'] = $generalInfo->id;
    
        // Handle receipt upload if present
        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $fileName = ($center->type == Center::CENTER ? $center->code . '_' : '') . $receipt->getClientOriginalName();
    
            Storage::disk('s3')->delete($report->receipt ?? null); // Delete existing receipt if updating
    
            $receipt->storeAs('receipts', $fileName, 's3');

            $data['receipt'] = $fileName;
        }
    
        $id = $request->get('id');
    
        $report = Report::updateOrCreate(['id' => $id], $data);
    
        // Update report status
        $report->statuses()->updateOrCreate(
            ['status_id' => $id, 'status' => Status::NOTCONFIRMED, 'status_type' => Report::class]
        );
    
        return $this->getAction($request->get('button_action'));
    }
    


    // Edit
    public function edit(Request $request) {
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
