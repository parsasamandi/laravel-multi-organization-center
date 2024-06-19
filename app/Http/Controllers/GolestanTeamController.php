<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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

        $id = $request->get('id') ? 
            Crypt::decryptString($request->get('id')) : null; // Decrypt the ID

        $data = [
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'phone_number' => $request->get('phone_number'), 
            'email' => $request->get('email'), 
            'type' => Center::GOLESTANTEAM
        ];

        if($request->get('password'))
            $data['password'] = Hash::make($request->get('password'));


        // Insert or update
        Center::updateOrCreate(['id' => $id], $data);

        return $this->getAction($request->get('button_action'));
    }
    
    // Edit
    public function edit(Request $request) {

        $id = Crypt::decryptString($request->get('id')); // Decrypt the ID

        return $this->action->edit(Center::class, $id);
    }

    // Delete
    public function delete(Request $request) {

        $id = Crypt::decryptString($request->get('id')); // Decrypt the ID

        return $this->action->delete(Center::class, $id);
    }
}
