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
            'phone_number' => $request->get('phone_number'), 
            'email' => $request->get('email'), 
            'password' => Hash::make($request->get('password'))
        ];

        if($request->get('type') == Center::SUPERADMIN) {
            $data['type'] = Center::SUPERADMIN;
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

    // Delete
    public function delete($id) {
        return $this->action->delete(Center::class, $id);
    }
}
