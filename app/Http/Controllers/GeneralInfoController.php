<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\StoreGeneralInfoRequest;
use App\Providers\SuccessMessages;
use App\DataTables\GeneralInfoDataTable;
use App\Models\GeneralInfo;
use App\Providers\Action;
use App\Models\Status;
use App\Models\Center;
use File;
use Storage;

class GeneralInfoController extends Controller
{
    public $action;

    public function __construct() {
        $this->action = new Action();
    }

    // DataTable to blade
    public function list() {
        // General Info Table
        $GeneralInfoTable = new GeneralInfoDataTable;

        $vars['generalInfoTable'] = $GeneralInfoTable->html();

        return view('generalInfo.list', $vars);
    }

    // Rendering General Info Table
    public function generalInfoTable(GeneralInfoDataTable $generalInfoTable) {
        return $generalInfoTable->render('generalInfo.list');
    }

    // Insert or Update
    public function store(StoreGeneralInfoRequest $request) {
        $data = [
            'jalaliMonth' => $request->get('jalaliMonth'),
            'jalaliYear' => $request->get('jalaliYear'),
            'bank_balance' => $request->get('bank_balance')
        ];
    
        if (Auth::user()->type == Center::CENTER) {
            $data['center_id'] = Auth::id();
        }
    
        // Handle receipt upload
        $generalInfoId = $request->get('id') ? Crypt::decryptString($request->get('id')) : null;
        if ($generalInfoId) {
            $generalInfo = GeneralInfo::find($generalInfoId);
        } else {
            $generalInfo = new GeneralInfo();
        }
        
        $this->handleReceiptUpload($request, $generalInfo, $data);
    
        // Create or update the GeneralInfo record
        $generalInfo = GeneralInfo::updateOrCreate(['id' => $generalInfoId], $data);
    
        // Update General info's status
        $generalInfo->statuses()->updateOrCreate(
            ['status_id' => $generalInfo->id, 'status' => Status::NOTCONFIRMED, 'status_type' => GeneralInfo::class]
        );
    
        return $this->getAction($request->get('button_action'));
    }

    // Handleing the receipt upload
    public function handleReceiptUpload(StoreGeneralInfoRequest $request, $generalInfo, &$data) {

        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $center = Center::find(Auth::user()->id);
    
            $fileName = $center->type === Center::CENTER ?
                "GOL{$center->code}/{$request->get('jalaliMonth')}_{$request->get('jalaliYear')}/{$receipt->getClientOriginalName()}" :
                "GOLTEAM{$center->code}/{$request->get('jalaliMonth')}_{$request->get('jalaliYear')}/{$receipt->getClientOriginalName()}";
    
            // Delete old receipt if it exists
            if ($generalInfo && $generalInfo->bank_statement_receipt) {
                Storage::disk('s3')->delete('receipts/' . $generalInfo->bank_statement_receipt);
            }
    
            // Store new receipt
            $receipt->storeAs('receipts', $fileName, 's3');
            $data['bank_statement_receipt'] = $fileName; // Ensure this line correctly updates the $data array
        }
    }
    
    
    // Edit
    public function edit(Request $request) {
       // Encrypting the ID
        $id = Crypt::decryptString($request->get('id'));

        return $this->action->edit(GeneralInfo::class, $id);
    }

    // Confirming the General Info status
    public function confirmStatus(Request $request) {

        $id = $request->get('id');

        $generalInfo = GeneralInfo::findOrFail($id);

        // Checking if it was confirmed
        if($request->get('status') == Status::CONFIRMED) {

            // Updating General info's status into "Confirmed"
            $generalInfo->statuses()->update(
                ['status' => Status::CONFIRMED]
            );
        } else {
            // Updating General info's status into "Not confirmed"
            $generalInfo->statuses()->update(
                ['status' => Status::NOTCONFIRMED]
            );
        }

        return response()->json(['success' => true], Response::HTTP_CREATED);
    }

    // Details
    public function details(Request $request) {

        $id = Crypt::decryptString($request->get('id')); // Decrypt the ID

        // Fetch the data for the specified ID from the database
        $generalInfo = GeneralInfo::where('id', $id)->with('statuses')->first();

        // Return the view with the data
        return view('generalInfo.details')->with('generalInfo', $generalInfo);
    }


    // Delete
    public function delete(Request $request) {

        $id = Crypt::decryptString($request->get('id')); // Decrypt the ID

        $generalInfo = GeneralInfo::find($id);
        
        // Deleting from storage
        Storage::disk('s3')->delete('receipts/' . $generalInfo->bank_statement_receipt);
    
        return $this->action->delete(GeneralInfo::class, $id);
    }

}
