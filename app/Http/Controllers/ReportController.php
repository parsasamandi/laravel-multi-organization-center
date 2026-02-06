<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreReportRequest;
use App\DataTables\ReportDataTable;
use App\Providers\SuccessMessages;
use App\Providers\Action;
use App\Models\Report;
use App\Models\GeneralInfo;
use App\Models\Status;
use App\Models\Center;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected $action;

    // Constructor to inject the Action service into the controller
    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    // Method to display the list view for reports using DataTables
    public function list()
    {
        // Create a new instance of the ReportDataTable
        $reportTable = new ReportDataTable();
        // Render the view with the DataTable's HTML content
        return view('report.list', ['reportTable' => $reportTable->html()]);
    }

    // Method to render the Report DataTable
    public function reportTable(ReportDataTable $reportTable)
    {
        // Render the DataTable view
        return $reportTable->render('report.list');
    }

    // Method to store or update a report
    public function store(StoreReportRequest $request)
    {
        // Get the center ID from the authenticated user
        $centerId = Auth::id();
        $report = null;

        // If the request contains an ID, decrypt it to find the existing report
        if ($request->filled('id')) {
            $reportId = $this->decryptId($request->get('id'));
            $report = Report::find($reportId);
            // If a report is found, use its center ID, otherwise, use the authenticated user's ID
            $centerId = $report->center_id ?? $centerId;
        }

        // Extract relevant data from the request
        $data = $request->only(['expenses', 'range', 'type', 'description']);
        $data['center_id'] = $centerId;

        // Retrieve the corresponding GeneralInfo record based on the center ID, Jalali month, and year
        $generalInfo = GeneralInfo::where([
            'center_id' => $centerId,
            'jalaliMonth' => $request->get('jalaliMonth'),
            'jalaliYear' => $request->get('jalaliYear')
        ])->first();

        // Associate the report with the found GeneralInfo record
        $data['general_info_id'] = $generalInfo->id;

        // Handle the receipt file upload if provided
        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $fileName = $this->getReceiptFileName(
                $centerId, 
                $request->get('jalaliMonth'), 
                $request->get('jalaliYear'), 
                $receipt->getClientOriginalName()
            );
            
            // If updating an existing report, delete the old receipt file from S3
            if ($report) {
                Storage::disk('s3')->delete('receipt/' . $report->receipt);
            }

            // Store the new receipt file in S3 and update the data array with the file name
            if ($receipt->storeAs('receipt', $fileName, 's3')) {
                // If the file is successfully uploaded, update the data array with the file name
                $data['receipt'] = $fileName;
            } else {
                // Return a JSON error response if the upload fails
                return $this->getErrorMessage("رسید فایل به درستی بارگزاری نشد، لطفا دوباره امتحان کنید.");
            }
        }

        // Create a new report or update the existing one
        $report = Report::updateOrCreate(['id' => $reportId ?? null], $data);

        // By default, set the status of the report to NOT CONFIRMED
        $report->statuses()->create(['status' => Status::NOTCONFIRMED]);

        // Return the appropriate response based on the button action
        return $this->getAction($request->get('button_action'));
    }

    // Helper method to generate a file name for the receipt based on center ID, month, year, and original file name
    private function getReceiptFileName($centerId, $month, $year, $originalName)
    {
        // Find the center by ID
        $center = Center::find($centerId);
        // Determine the prefix for the file name based on the center type
        $prefix = $center->type === Center::CENTER ? "GOL{$center->code}" : "GOLTEAM{$center->code}";
        // Construct and return the full file name
        return "{$prefix}/Y{$year}/M{$month}_{$originalName}";
    }

    // Method to retrieve a report's information for editing
    public function edit(Request $request)
    {
        // Decrypt the ID from the request to find the report
        $id = $this->decryptId($request->get('id'));

        // Retrieve the report along with its associated general info
        $report = Report::with('generalInfo:id,jalaliMonth,jalaliYear')
            ->select('id', 'center_id', 'general_info_id', 'expenses', 'range', 'receipt', 'description', 'type')
            ->find($id);

        // Return the report data as a JSON response
        return response()->json($report);
    }

    // Method to update the status of a report (e.g., confirming or not confirming)
    public function confirmStatus(Request $request)
    {
        // Find the report by ID
        $id = $request->get('id');
        $report = Report::findOrFail($id);

        // Determine the new status based on the request input
        $statusValue = (int) $request->get('status');
        $status = match ($statusValue) {
            Status::SUCCESSFUL => Status::SUCCESSFUL,
            Status::UNSUCCESSFUL => Status::UNSUCCESSFUL,
            default => Status::NOTCONFIRMED,
        };
        
        // Update the report's status
        $report->statuses()->update(['status' => $status]);

        // Return a success response
        return response()->json(['success' => true], Response::HTTP_CREATED);
    }

    // Method to display the details of a specific report
    public function details(Request $request)
    {
        // Decrypt the ID from the request to find the report
        $id = $this->decryptId($request->get('id'));
        // Retrieve the report along with its associated general info
        $report = Report::with('generalInfo')->findOrFail($id);

        // Render the details view with the report data
        return view('report.details', ['report' => $report]);
    }

    // Method to delete a report
    public function delete(Request $request)
    {
        // Decrypt the ID from the request to find the report
        $id = $this->decryptId($request->get('id'));
        // Find the report by ID
        $report = Report::findOrFail($id);

        // Delete the associated receipt file from S3
        if(Storage::disk('s3')->delete('receipt/' . $report->receipt)) {
            //  Use the Action service to handle the deletion process
            return $this->action->delete(Report::class, $id);
        }

        return $this->getErrorMessage("رسید فایل قبلا به درستی بارگزاری نشده است و حذف نمی‌شود.");
    }

    // Helper method to decrypt an encrypted ID, returning null if no ID is provided
    private function decryptId($encryptedId)
    {
        return $encryptedId ? Crypt::decryptString($encryptedId) : null;
    }
}