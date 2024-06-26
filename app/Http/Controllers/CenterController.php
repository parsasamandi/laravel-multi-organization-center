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

    public function __construct(Action $action) {
        $this->action = $action;
    }

    // The home page
    public function center() {
        return view('home');
    }

    // DataTable to blade
    public function list() {
        $dataTable = new CenterDataTable();
        return view('centerList', ['centerTable' => $dataTable->html()]);
    }

    // Get Table
    public function centerTable(CenterDataTable $centerTable) {
        return $centerTable->render('centerList');
    }

    // Store
    public function store(StoreCenterRequest $request) {
        $id = $request->filled('id') ? $this->decryptId($request->get('id')) : null;

        $data = $request->only(['name', 'code', 'phone_number', 'email']) + ['type' => Center::CENTER];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->get('password'));
        }

        Center::updateOrCreate(['id' => $id], $data);

        return $this->getAction($request->get('button_action'));
    }

    // Edit
    public function edit(Request $request) {
        $id = $this->decryptId($request->get('id'));
        return $this->action->edit(Center::class, $id);
    }

    // Delete
    public function delete(Request $request) {
        $id = $this->decryptId($request->get('id'));
        return $this->action->delete(Center::class, $id);
    }

    // Decrypt ID
    private function decryptId($encryptedId) {
        return Crypt::decryptString($encryptedId);
    }
}
