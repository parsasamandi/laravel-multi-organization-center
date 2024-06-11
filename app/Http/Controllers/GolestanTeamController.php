<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\DataTables\GolestanTeamDataTable;
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
    public function list() {
        // dataTable
        $dataTable = new GolestanTeamDataTable();

        // GolestanTeam table
        $vars['golestanTeamTable'] = $dataTable->html();

        return view('golestanTeamList', $vars);
    }

    // Get Table
    public function golestanTeamTable(GolestanTeamDataTable $golestanTeamTable) {
        return $golestanTeamTable->render('golestanTeamList');
    }

    // Store
    public function store(StoreCenterRequest $request) {

        $data = [
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'phone_number' => $request->get('phone_number'), 
            'email' => $request->get('email'), 
            'password' => Hash::make($request->get('password')),
            'type' => Center::GOLESTANTEAM
        ];

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
