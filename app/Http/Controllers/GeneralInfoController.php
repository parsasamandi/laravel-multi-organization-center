<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreGeneralInfoRequest;
use App\Http\Requests\UpdateGeneralInfoRequest;
use App\Providers\SuccessMessages;
use App\Datatables\GeneralInfoDataTable;
use App\Models\GeneralInfo;
use App\Providers\Action;
use App\Models\Status;
use File;
use Auth;

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

    // Insert
    public function store(StoreGeneralInfoRequest $request) {


        $receipt = $request->file('receipt');
        $file = $receipt->getClientOriginalName();
        $receipt->move(public_path('receipts'), $file);

        // Storing General info
        $generalInfo = GeneralInfo::create([
            'jalaliMonth' => $request->get('jalaliMonth'),
            'jalaliYear' => $request->get('jalaliYear'),
            'bank_balance' => $request->get('bank_balance'),
            'bank_statement_receipt' => $file,
            'center_id' => Auth::id()
        ]);

        // Storing General info's status
        $generalInfo->statuses()->create(
            ['status' => Status::NOTCONFIRMED, 'status_type' => GeneralInfo::class]
        );

        return $this->getAction($request->get('button_action'));

    }

    // Edit
    public function edit($id) {
        // Fetch the data for the specified ID from the database
        $generalInfo = GeneralInfo::findOrFail($id); // Replace with your actual model name

        // Return the view with the data
        return view('generalInfo.edit')->with('generalInfo', $generalInfo); 
    }

    // Update
    public function update(Request $request) {

        $generalInfo = GeneralInfo::findOrFail($request->get('id'));

        // Initialize $updateData array
        $updateData = [
            'bank_balance' => $request->get('bank_balance'),
            'center_id' => Auth::id(),
        ];

        // Check if a receipt file is uploaded
        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $file = $receipt->getClientOriginalName();
            $receipt->move(public_path('receipts'), $file);
            $updateData['bank_statement_receipt'] = $file; // Include file in update data
        }

        // Update the GeneralInfo record
        $generalInfo->update($updateData);
    }

    // Confirming the General Info status
    public function confrimStatus(Request $request) {

        $generalInfo = GeneralInfo::findOrFail($request->get('id'));

        // Checking if it was confirmed
        if($request->get('status') == Status::CONFIRMED) {

            // Storing General info's status
            $generalInfo->statuses()->update(
                ['status' => Status::CONFIRMED]
            );
        } else {
             // Storing General info's status
             $generalInfo->statuses()->update(
                ['status' => Status::NOTCONFIRMED]
            );
        }

        return response()->json(['success' => true], Response::HTTP_CREATED); 
    }

    // Delete
    public function delete($id) {

        $generalInfo = GeneralInfo::find($id);

        return $this->action->deleteWithFile(GeneralInfo::class, 
            $id, $generalInfo->bank_statement_receipt);
    }


    // Details
    public function details($id) {
        // Fetch the data for the specified ID from the database
        $generalInfo = GeneralInfo::where('id', $id)->with('statuses')->first(); 

        // Return the view with the data
        return view('generalInfo.details')->with('generalInfo', $generalInfo); 
    }

}
