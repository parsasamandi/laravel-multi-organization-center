<?php

namespace App\Http\Controllers;

use Morilog\Jalali\Jalalian;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreGeneralInfoRequest;
use App\Providers\SuccessMessages;
use App\DataTables\GeneralInfoDataTable;
use App\Models\GeneralInfo;
use App\Providers\Action;
use App\Models\Status;
use App\Models\Center;

class GeneralInfoController extends Controller
{
    protected $action;

    // Constructor to inject the Action service
    public function __construct(Action $action) {
        $this->action = $action;
    }

    // Method to display the list view of general information using DataTables
    public function list() {
        $generalInfoTable = new GeneralInfoDataTable();
        return view('generalInfo.list', ['generalInfoTable' => $generalInfoTable->html()]);
    }

    // Method to render the DataTable for general information
    public function generalInfoTable(GeneralInfoDataTable $generalInfoTable) {
        return $generalInfoTable->render('generalInfo.list');
    }

    // Method to handle storing or updating general information
    public function store(StoreGeneralInfoRequest $request) {
        // // Get the current authenticated user's center ID
        $centerId = Auth::id();
        $generalInfo = null;

        // If 'id' is provided, find the existing generalInfo record
        if ($request->filled('id')) {
            $generalInfoId = $this->decryptId($request->get('id'));
            $generalInfo = GeneralInfo::find($generalInfoId) ?? null;
            $centerId = $generalInfo->center_id ?? $centerId;
        }

        // Extract relevant data from the request
        $data = $request->only(['jalaliMonth', 'jalaliYear', 'bank_balance']);
        $data['center_id'] = $centerId;

        // If a receipt file is uploaded, handle the file storage
        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $fileName = $this->getReceiptFileName($centerId, $request->get('jalaliMonth'), 
                $request->get('jalaliYear'), $receipt->getClientOriginalName());

            // Delete the old receipt file from S3 if it exists
            if ($generalInfo && $generalInfo->bank_statement_receipt) {
                Storage::disk('s3')->delete('bank_statement/' . $generalInfo->bank_statement_receipt);
            }

            // Store the new receipt file on S3
            if ($receipt->storeAs('bank_statement', $fileName, 's3')) {
                // If the file is successfully uploaded, update the data array with the file name
                $data['bank_statement_receipt'] = $fileName;
            } else {
                // Return a JSON error response if the upload fails
                return $this->getErrorMessage("رسید فایل به درستی بارگزاری نشد، لطفا دوباره امتحان کنید.");
            }
        }   

        // Create or update the GeneralInfo record
        $generalInfo = GeneralInfo::updateOrCreate(['id' => $generalInfoId ?? null], $data);

        // Set the status to NOTCONFIRMED by default
        $generalInfo->statuses()->create(['status' => Status::NOTCONFIRMED]);

        // Determine the action to take based on the button_action input
        return $this->getAction($request->get('button_action'));
    }

    // Helper method to generate the file name for the receipt
    private function getReceiptFileName($centerId, $month, $year, $originalName)
    {
        $center = Center::find($centerId);
        $prefix = $center->type === Center::CENTER ? "GOL{$center->code}" : "GOLTEAM{$center->code}";
        return "{$prefix}/Y{$year}/M{$month}_{$originalName}";
    }

    // Method to retrieve a GeneralInfo record for editing
    public function edit(Request $request) {
        $id = $this->decryptId($request->get('id'));
        return $this->action->edit(GeneralInfo::class, $id);
    }

    // Review status
    public function confirmStatus(Request $request) {
        $id = $request->get('id');
        $generalInfo = GeneralInfo::findOrFail($id);

        // Determine the new status based on the input and update the record
        $statusValue = (int) $request->get('status');
        $status = match ($statusValue) {
            Status::SUCCESSFUL => Status::SUCCESSFUL,
            Status::UNSUCCESSFUL => Status::UNSUCCESSFUL,
            default => Status::NOTCONFIRMED,
        };
        
        // Update the bank_receipt's info status
        $generalInfo->statuses()->update(['status' => $status]);

        return response()->json(['success' => true], Response::HTTP_CREATED);
    }

    // Method to show the details of a specific GeneralInfo record
    public function details(Request $request) {
        $id = $this->decryptId($request->get('id'));
        $generalInfo = GeneralInfo::with('statuses')->findOrFail($id);
        return view('generalInfo.details', ['generalInfo' => $generalInfo]);
    }

    // Method to delete a specific GeneralInfo record and its associated receipt file
    public function delete(Request $request) {
        $id = $this->decryptId($request->get('id'));
        $generalInfo = GeneralInfo::findOrFail($id);
    
        // Delete the associated receipt file from S3
        if (Storage::disk('s3')->delete('bank_statement/' .  $generalInfo->bank_statement_receipt)) {
            return $this->action->delete(GeneralInfo::class, $id);
        }
    
        return $this->getErrorMessage("رسید فایل قبلا به درستی بارگزاری نشده است و حذف نمی‌شود.");
    }    
    
    // Helper method to decrypt an encrypted ID
    private function decryptId($encryptedId) {
        return $encryptedId ? Crypt::decryptString($encryptedId) : null;
    }
}