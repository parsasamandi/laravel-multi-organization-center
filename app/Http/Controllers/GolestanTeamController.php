<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\DataTables\GolestanTeamDataTable;
use App\Http\Requests\StoreCenterRequest;
use App\Http\Requests\StoreGolestanTeamRequest;
use App\Providers\Action;
use App\Models\Center;

class GolestanTeamController extends Controller
{
    protected $action;

    // Constructor to inject the Action service into the controller
    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    // Method to display the list view for Golestan team members using DataTables
    public function list()
    {
        // Create a new instance of the GolestanTeamDataTable
        $dataTable = new GolestanTeamDataTable();
        // Render the view with the DataTable's HTML content
        return view('golestanTeamList', ['golestanTeamTable' => $dataTable->html()]);
    }

    // Method to render the GolestanTeam DataTable
    public function golestanTeamTable(GolestanTeamDataTable $golestanTeamTable)
    {
        // Render the DataTable view
        return $golestanTeamTable->render('golestanTeamList');
    }

    // Method to store or update a Golestan team member's information
    public function store(StoreGolestanTeamRequest $request)
    {
        // Decrypt the ID if it exists in the request, otherwise set it to null
        $id = $this->decryptId($request->get('id'));

        // Extract the name, phone number, and email from the request data
        $data = $request->only(['name', 'phone_number', 'email']);
        // Set the type of the Center to GOLESTANTEAM
        $data['type'] = Center::GOLESTANTEAM;

        // If a password is provided in the request, hash it before saving
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->get('password'));
        }

        // Create a new Center record or update an existing one based on the ID
        Center::updateOrCreate(['id' => $id], $data);

        // Determine the appropriate action to take based on the button action input
        return $this->getAction($request->get('button_action'));
    }

    // Method to retrieve a Golestan team member's information for editing
    public function edit(Request $request)
    {
        // Decrypt the ID from the request to find the Center record
        $id = $this->decryptId($request->get('id'));
        // Use the Action service to handle the editing process
        return $this->action->edit(Center::class, $id);
    }

    // Method to delete a Golestan team member's information
    public function delete(Request $request)
    {
        // Decrypt the ID from the request to find the Center record
        $id = $this->decryptId($request->get('id'));
        // Use the Action service to handle the deletion process
        return $this->action->delete(Center::class, $id);
    }

    // Helper method to decrypt an encrypted ID, returning null if no ID is provided
    private function decryptId($encryptedId)
    {
        return $encryptedId ? Crypt::decryptString($encryptedId) : null;
    }
}
