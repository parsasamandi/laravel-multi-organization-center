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
use Illuminate\Support\Facades\Crypt;
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

        $decryptedId = null;
        $report = null;

        if ($request->get('id')) {
            $decryptedId = Crypt::decryptString($request->get('id'));
            $report = Report::find($decryptedId);
            $centerId = $report->center_id;
            $center = Center::find($centerId);
         } else {
            // Get the authenticated user's center ID
            $centerId = Auth::user()->id;
            $center = Center::find($centerId);
        }

        $data = [
            'expenses' => $request->get('expenses'),
            'range' => $request->get('range'),
            'type' => $request->get('type'),
            'description' => $request->get('description'),
            'center_id' => $centerId
        ];
    
        $jalaliYear = $request->get('jalaliYear');
        $jalaliMonth = $request->get('jalaliMonth');
    
        // Getting general_info_id
        $generalInfo = GeneralInfo::where(function ($query) use ($centerId, $jalaliYear, $jalaliMonth) {
            $query->where('center_id', $centerId)
                ->where('jalaliMonth', $jalaliMonth)
                ->where('jalaliYear', $jalaliYear);
        })->first();
    
        $data['general_info_id'] = $generalInfo->id;
    
        // Handle receipt upload if present
        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');

            $fileName = $center->type === Center::CENTER ?
                "GOL{$center->code}/{$request->get('jalaliMonth')}_{$request->get('jalaliYear')}/{$receipt->getClientOriginalName()}" :
                "GOLTEAM{$center->code}/{$request->get('jalaliMonth')}_{$request->get('jalaliYear')}/{$receipt->getClientOriginalName()}";

            // Delete existing receipt if updating            
            $report != null ? Storage::disk('s3')->delete('receipts/' . $report->receipt) : null; 
    
            $receipt->storeAs('receipts', $fileName, 's3');
            $data['receipt'] = $fileName;
        }
    
        $report = Report::updateOrCreate(['id' => $decryptedId], $data);
    
        // Update report status
        $report->statuses()->updateOrCreate(
            ['status_id' => $report->id, 'status' => Status::NOTCONFIRMED, 'status_type' => Report::class]
        );
    
        return $this->getAction($request->get('button_action'));
    }

    // Edit
    public function edit(Request $request) {

        $id = Crypt::decryptString($request->get('id')); // Decrypt the ID
        
        // Fetch the report along with only specific columns from the related generalInfo
        $report = Report::with(['generalInfo' => function($query) {
            $query->select('id', 'jalaliMonth', 'jalaliYear');
        }])->select('id', 'center_id', 'general_info_id', 'expenses', 'range', 'receipt', 'description', 'type')
        ->find($id);
    
        // Return the response as JSON
        return response()->json($report);
    }    

    public function confirmStatus(Request $request) {

        $id = $request->get('id'); // Decrypt the ID

        $report = Report::findOrFail($id);

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
    public function details(Request $request) {

        $id = Crypt::decryptString($request->get('id')); // Decrypt the ID

        return view('report.details', ['report' => Report::with('generalInfo')->findOrFail($id)]);
    }

    // Delete
    public function delete(Request $request) {

        $id = Crypt::decryptString($request->get('id')); // Decrypt the ID
        
        $report = Report::find($id);
        
        // Deleting from storage
        Storage::disk('s3')->delete('receipts/' . $report->receipt);
    
        return $this->action->delete(Report::class, $id);
    }

}
