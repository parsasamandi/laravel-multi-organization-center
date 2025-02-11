<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\DataTables\CenterStatusReportDataTable;
use App\Http\Requests\StoreCenterRequest;
use App\Providers\Action;
use App\Models\Center;

class CenterStatusReportController extends Controller
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
        $dataTable = new CenterStatusReportDataTable();
        // Render the view with the DataTable's HTML content
        return view('centerStatusReportList', ['centerStatusReportTable' => $dataTable->html()]);
    }

    // Method to render the Center DataTable
    public function centerStatusReportTable(CenterStatusReportDataTable $centerTable) {
        // Render the DataTable view
        return $centerTable->render('centerStatusReportList');
    }

     // Method to show the details of a specific GeneralInfo record
     public function details(Request $request) {
        $id = $this->decryptId($request->get('id'));
        $generalInfo = GeneralInfo::with('statuses')->findOrFail($id);
        return view('generalInfo.details', ['generalInfo' => $generalInfo]);
    }

    // Helper method to decrypt an encrypted ID
    private function decryptId($encryptedId) {
        return Crypt::decryptString($encryptedId);
    }
}
