<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\DataTables\CenterDataTable;
use App\Http\Requests\StoreCenterRequest;
use App\Providers\Action;
use App\Models\Center;


class GolestanTeamController extends Controller
{
    public $action;

    public function __construct() {
        $this->action = new Action();
    }

    // DataTable to blade
    public function center() {
        return view('home');
    }

    // DataTable to blade
    public function list() {
        // dataTable
        $dataTable = new CenterDataTable();

        // Center table
        $vars['centerTable'] = $dataTable->html();

        return view('centerList', $vars);
    }

    // Get Table
    public function centerTable(CenterDataTable $centerTable) {
        return $centerTable->render('centerList');
    }

    // Store
    public function store(StoreCenterRequest $request) {

        $data = [
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'phone_number' => $request->get('phone_number'), 
            'email' => $request->get('email'), 
            'password' => Hash::make($request->get('password'))
        ];

        if($request->get('type') == Center::GOLESTANTEAM) {
            $data['type'] = Center::GOLESTANTEAM;
        } else {
            $data['type'] = Center::CENTER;
        }

        // Insert or update
        Center::updateOrCreate(['id' => $request->get('id')], $data);

        return $this->getAction($request->get('button_action'));
    }
    

    // Edit
    public function edit(Request $request) {
        return $this->action->edit(Center::class, $request->get('id'));
    }

    // Update
    public function update(UpdateGeneralInfoRequest $request) {

        $generalInfo = GeneralInfo::findOrFail($request->get('id'));

        // Initialize $updateData array
        $updateData = [
            'bank_balance' => $request->get('bank_balance'),
            'center_id' => Auth::id(),
        ];

        // Check if a receipt file is uploaded
        if ($request->hasFile('receipt')) {
            // Deleting from storage
            Storage::disk('s3')->delete($generalInfo->bank_statement_receipt);

            // Getting the file
            $receipt = $request->file('receipt');
            // File name
            $file_name = 'receipts/' . $receipt->getClientOriginalName();
            // Storing file to S3
            $receipt->storeAs('receipts', $file_name, 's3');
            
            $updateData['bank_statement_receipt'] = $file_name; // Include file in update dataa
        }

        // Update the GeneralInfo record
        $generalInfo->update($updateData);

        return $this->getAction("update");
    }

    // Delete
    public function delete($id) {
        return $this->action->delete(Center::class, $id);
    }
}
