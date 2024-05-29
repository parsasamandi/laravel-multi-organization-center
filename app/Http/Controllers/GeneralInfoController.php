<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreGeneralInfoRequest;
use App\Http\Requests\UpdateGeneralInfoRequest;
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
    // public function store(StoreGeneralInfoRequest $request) {

    //     // Id
    //     $id = $request->get('id');

    //     $data = [
    //         'jalaliMonth' => $request->get('jalaliMonth'),
    //         'jalaliYear' => $request->get('jalaliYear'),
    //         'bank_balance' => $request->get('bank_balance'),
    //         'center_id' => Auth::id()
    //     ];

    //     $generalInfo = GeneralInfo::updateOrCreate(['id' => $id], $data);

    //     if($request->get('id')) {
    //         if($request->hasFile('receipt')) {

    //             Storage::disk('s3')->delete($generalInfo->bank_statement_receipt);

    //             // Getting the file
    //             $receipt = $request->file('receipt');
    //             // File name
    //             $center = Center::find(Auth::user()->id);
    
    //             if($center->type == Center::CENTER) 
    //                 $file_name = $center->code . '_' .$receipt->getClientOriginalName();
    //             else
    //                 $file_name = $receipt->getClientOriginalName();
    
    //             // Storing file to S3
    //             $receipt->storeAs('receipts', $file_name, 's3');
    
    //             $data['bank_statement_receipt'] = $file_name;
    //         }

    //     }

    //     if($request->hasFile('receipt')) {

    //         // Getting the file
    //         $receipt = $request->file('receipt');
    //         // File name
    //         $center = Center::find(Auth::user()->id);

    //         if($center->type == Center::CENTER) 
    //             $file_name = $center->code . $receipt->getClientOriginalName();
    //         else
    //             $file_name = $receipt->getClientOriginalName();

    //         // Storing file to S3
    //         $receipt->storeAs('receipts', $file_name, 's3');

    //         $data['bank_statement_receipt'] = $file_name;
    //     }

    //     // Storing General info's status
    //     $generalInfo->statuses()->updateOrCreate(
    //         ['status_id' => $id, 'status' => Status::NOTCONFIRMED, 'status_type' => GeneralInfo::class]
    //     );

    //     return $this->getAction($request->get('button_action'));
    // }

    public function store(StoreGeneralInfoRequest $request) {

        $data = [
            'jalaliMonth' => $request->get('jalaliMonth'),
            'jalaliYear' => $request->get('jalaliYear'),
            'bank_balance' => $request->get('bank_balance'),
            'center_id' => Auth::id(),
        ];
    
        // Combine logic for handling receipt upload
        $this->handleReceiptUpload($request, $data);
    
        $generalInfo = GeneralInfo::updateOrCreate(['id' => $request->get('id')], $data);
    
        // Update General info's status (can be optimized further)
        $generalInfo->statuses()->updateOrCreate(
            ['status_id' => $generalInfo->id, 'status' => Status::NOTCONFIRMED, 'status_type' => GeneralInfo::class]
        );
    
        return $this->getAction($request->get('button_action'));
    }
    
    private function handleReceiptUpload(StoreGeneralInfoRequest $request, &$data) {
        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $center = Center::find(Auth::user()->id);
    
            $fileName = $center->type === Center::CENTER ?
                $center->code . '_' . $receipt->getClientOriginalName() :
                $receipt->getClientOriginalName();
    
            if ($request->get('id')) {
                Storage::disk('s3')->delete($generalInfo->bank_statement_receipt);
            }
    
            $receipt->storeAs('receipts', $fileName, 's3');
            $data['bank_statement_receipt'] = $fileName;
        }
    }
    
    
    // Edit
    public function edit(Request $request) {
        return $this->action->edit(GeneralInfo::class, $request->get('id'));
    }

    // Confirming the General Info status
    public function confirmStatus(Request $request) {

        $generalInfo = GeneralInfo::findOrFail($request->get('id'));

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
    public function details($id) {
        // Fetch the data for the specified ID from the database
        $generalInfo = GeneralInfo::where('id', $id)->with('statuses')->first();

        // Return the view with the data
        return view('generalInfo.details')->with('generalInfo', $generalInfo);
    }

    // Delete
    public function delete($id) {

        $generalInfo = GeneralInfo::find($id);
        
        // Deleting from storage
        Storage::disk('s3')->delete($generalInfo->bank_statement_receipt);
    
        return $this->action->delete(GeneralInfo::class, $id);
    }

}
