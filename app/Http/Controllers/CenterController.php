<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\DataTables\AdminDataTable;
use App\Http\Requests\StoreAdminRequest;
use App\Providers\Action;
use App\Models\Category;
use App\Models\User;


class CenterController extends Controller
{
    public $action;

    public function __construct() {
        $this->action = new Action();
    }

    // Admin home
    public function admin() {
        return view('admin.home');
    }

    // DataTable to blade
    public function list() {
        // dataTable
        $dataTable = new AdminDataTable();

        // Admin table
        $vars['adminTable'] = $dataTable->html();

        return view('admin.list', $vars);
    }

    // Get 
    public function adminTable(AdminDataTable $dataTable) {
        return $dataTable->render('admin.list');
    }

    // Store
    public function store(StoreAdminRequest $request) {

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
