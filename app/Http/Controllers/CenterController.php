<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\DataTables\CenterDataTable;
use App\Http\Requests\StoreCenterRequest;
use App\Providers\Action;
use App\Models\Center;

class CenterController extends Controller
{
    protected $action;

    // Constructor to inject the Action service into the controller
    public function __construct(Action $action) {
        $this->action = $action;
    }

    // Method to display the home page
    public function center() {
        return view('home');
    }

    // Method to render the list view for centers using DataTables
    public function list() {
        // Create a new instance of the CenterDataTable
        $dataTable = new CenterDataTable();
        // Render the view with the DataTable's HTML content
        return view('centerList', ['centerTable' => $dataTable->html()]);
    }

    // Method to render the Center DataTable
    public function centerTable(CenterDataTable $centerTable) {
        // Render the DataTable view
        return $centerTable->render('centerList');
    }

    // Method to store or update a center
    public function store(StoreCenterRequest $request) {
        // Decrypt the ID if it is present in the request, otherwise set to null
        $id = $request->filled('id') ? $this->decryptId($request->get('id')) : null;

        // Extract relevant data from the request and set the type to 'CENTER'
        $data = $request->only(['name', 'name_en', 'code', 'phone_number', 'email']) + ['type' => Center::CENTER];

        // If a password is provided, hash it and add it to the data array
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->get('password'));
        }

        // Create a new center or update the existing one
        Center::updateOrCreate(['id' => $id], $data);

        // Return the appropriate response based on the button action
        return $this->getAction($request->get('button_action'));
    }

    // Method to retrieve a center's information for editing
    public function edit(Request $request) {
        // Decrypt the ID from the request to find the center
        $id = $this->decryptId($request->get('id'));
        // Use the Action service to handle the edit process
        return $this->action->edit(Center::class, $id);
    }

    // Method to delete a center
    public function delete(Request $request) {
        // Decrypt the ID from the request to find the center
        $id = $this->decryptId($request->get('id'));
        // Use the Action service to handle the deletion process
        return $this->action->delete(Center::class, $id);
    }

    // Helper method to decrypt an encrypted ID
    private function decryptId($encryptedId) {
        return Crypt::decryptString($encryptedId);
    }
}
