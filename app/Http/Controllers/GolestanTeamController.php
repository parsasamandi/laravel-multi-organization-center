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
    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function list()
    {
        $dataTable = new GolestanTeamDataTable();
        return view('golestanTeamList', ['golestanTeamTable' => $dataTable->html()]);
    }

    public function golestanTeamTable(GolestanTeamDataTable $golestanTeamTable)
    {
        return $golestanTeamTable->render('golestanTeamList');
    }

    public function store(StoreCenterRequest $request)
    {
        $id = $this->decryptId($request->get('id'));

        $data = $request->only(['name', 'phone_number', 'email']);
        $data['type'] = Center::GOLESTANTEAM;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->get('password'));
        }

        Center::updateOrCreate(['id' => $id], $data);

        return $this->getAction($request->get('button_action'));
    }

    public function edit(Request $request)
    {
        $id = $this->decryptId($request->get('id'));
        return $this->action->edit(Center::class, $id);
    }

    public function delete(Request $request)
    {
        $id = $this->decryptId($request->get('id'));
        return $this->action->delete(Center::class, $id);
    }

    private function decryptId($encryptedId)
    {
        return $encryptedId ? Crypt::decryptString($encryptedId) : null;
    }
}
