<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\DataTables\CenterDataTable;
use App\Http\Requests\StoreCenterRequest;
use App\Providers\Action;
use App\Models\Center;

class CenterController extends Controller
{
    public $action;

    public function __construct() {
        $this->action = new Action();
    }

    // The home page
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
            'type' => Center::CENTER,
            'password' => Hash::make($request->get('password'))
        ];
    
        $id = $request->get('id') ? $request->get('id') : null; // Set id to null if empty
    
        // Insert or update
        Center::updateOrCreate(['id' => $id], $data);
    
        return $this->getAction($request->get('button_action'));
    }    
    

    // Edit
    public function edit(Request $request) {
        return $this->action->edit(Center::class, $request->get('id'));
    }


    // Delete
    public function delete($id) {
        return $this->action->delete(Center::class, $id);
    }
}
