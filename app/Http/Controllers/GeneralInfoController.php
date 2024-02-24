<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreGeneralInfoRequest;
use App\Providers\SuccessMessages;
use App\Datatables\GeneralInfoDataTable;
use App\Models\GeneralInfo;
use App\Providers\Action;
use File;
use Auth;

class GeneralInfoController extends Controller
{
    public $action;

    public function __construct() {
        $this->action = new Action();
    }

    // DataTable to blade
    public function list() {
        // General Info Table
        $GeneralInfoTable = new GeneralInfoDataTable;

        $vars['generalInfoTable'] = $GeneralInfoTable->html();

        return view('generalInfoList', $vars);
    }

    // Rendering General Info Table
    public function generalInfoTable(GeneralInfoDataTable $generalInfoTable) {
        return $generalInfoTable->render('generalInfoList'); 
    }

    // Insert
    public function store(StoreGeneralInfoRequest $request) {

        if($request->hasFile('bank_statement_receipt')) {

            $generalInfo = GeneralInfo::where('jalaliMonth', $request->get('jalaliMonth'))
                                ->where('jalaliYear', $request->get('jalaliYear'))->first();
            if(!$generalInfo) {

                $receipt = $request->file('bank_statement_receipt');
                $file = $receipt->getClientOriginalName();
                $receipt->move(public_path('receipts'), $file);

                GeneralInfo::updateOrCreate(
                    ['id' => $request->get('id')],
                    ['jalaliMonth' => $request->get('jalaliMonth'),
                    'jalaliYear' => $request->get('jalaliYear'), 
                    'bank_balance' => $request->get('bank_balance'),
                    'center_id' => Auth::id(),
                    'bank_statement_receipt' => $file
                ]);

            } else {
                return response()->json(['success' => true, 'message' => '<div class="alert alert-danger">برای این تاریخ قبلا اطلاعات وارد شده است.</div>']); 
            }



            return $this->getAction($request->get('button_action'));

        }

    }

    // Delete
    public function delete($id) {
        return $this->action->deleteWithFile(GeneralInfo::class, $request->get('id'), 'bank_statement_receipt');
    }

    // Edit
    public function edit(Request $request) {
        return $this->action->edit(GeneralInfo::class, $request->get('id'));
    }
}
