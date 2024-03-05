<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\StoreGeneralInfoRequest;
use App\Http\Requests\UpdateGeneralInfoRequest;
use App\Providers\SuccessMessages;
use App\Datatables\GeneralInfoDataTable;
use App\Models\GeneralInfo;
use App\Providers\Action;
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

        GeneralInfo::create([
            'jalaliMonth' => $request->get('jalaliMonth'),
            'jalaliYear' => $request->get('jalaliYear'),
            'bank_balance' => $request->get('bank_balance'),
            'bank_statement_receipt' => $file,
            'center_id' => Auth::id()
        ]);

        return $this->getAction($request->get('button_action'));
    }

    // Update
    public function update(UpdateGeneralInfoRequest $request) {

        // Inside your method where you have access to the validator instance
        $validator = Validator::make([], []); // Create an empty validator instance
        $validator->errors()->add('general_info', 'مقدمات گزارش برای تاریخ مورد نظر وجود ندارد.');

        // After adding errors, you can check if there are any errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toJson(),
                'message' => 'Validation failed' // Add your message here if needed
            ], 422); // Use appropriate status code for validation errors
        }

        $generalInfo = GeneralInfo::findOrFail($request->get('id'));

        // Initialize $updateData array
        $updateData = [
            'jalaliMonth' => $request->get('jalaliMonth'),
            'jalaliYear' => $request->get('jalaliYear'),
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

        return $this->getAction($request->get('button_action'));
    }

    // Delete
    public function delete($id) {

        $generalInfo = GeneralInfo::findOrFail($id);

        return $this->action->deleteWithFile(GeneralInfo::class, 
          $id, $generalInfo->bank_statement_receipt);
    }

    // Edit
    public function edit($id) {
        // Fetch the data for the specified ID from the database
        $generalInfo = GeneralInfo::findOrFail($id); // Replace with your actual model name

        // Return the view with the data
        return view('generalInfo.edit')->with('generalInfo', $generalInfo); 
    }

    // Details
    public function details($id) {
        // Fetch the data for the specified ID from the database
        $generalInfo = GeneralInfo::where('id', $id)->first(); 

        // Return the view with the data
        return view('generalInfo.details')->with('generalInfo', $generalInfo); 
    }

}
