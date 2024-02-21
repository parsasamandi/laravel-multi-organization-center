<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\DataTables\CenterDataTable;
// use App\Http\Requests\StoreAdminRequest;
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
        return view('admin.home');
    }


    // DataTable to blade
    public function list() {
        // dataTable
        $dataTable = new CenterDataTable();

        // Center table
        $vars['centerTable'] = $dataTable->html();

        return view('centerList', $vars);
    }

    // Get 
    public function centerTable(CenterDataTable $dataTable) {
        return $dataTable->render('centerList');
    }

    // Store
    public function store(Request $request) {

        // Insert or update
        $password = Hash::make($request->get('password'));

        User::updateOrCreate(
            ['id' => $request->get('id')],
            ['name' => $request->get('name'), 'email' => $request->get('email'), 'password' => $password]
        );

        return $this->getAction($request->get('button_action'));
    }
    
    // Edit 
    public function edit(Request $request) {
        return $this->action->edit(User::class, $request->get('id'));
    }

    // Delete
    public function delete($id) {
        return $this->action->delete(User::class, $id);
    }
}
