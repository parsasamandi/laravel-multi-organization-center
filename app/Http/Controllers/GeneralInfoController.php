<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreGeneralInfoRequest;
use App\Providers\SuccessMessages;
use App\DataTables\GeneralInfoDataTable;
use App\Models\GeneralInfo;
use App\Providers\Action;
use App\Models\Status;
use App\Models\Center;

class GeneralInfoController extends Controller
{
    protected $action;

    public function __construct(Action $action) {
        $this->action = $action;
    }

    public function list() {
        $generalInfoTable = new GeneralInfoDataTable();
        return view('generalInfo.list', ['generalInfoTable' => $generalInfoTable->html()]);
    }

    public function generalInfoTable(GeneralInfoDataTable $generalInfoTable) {
        return $generalInfoTable->render('generalInfo.list');
    }

    public function store(StoreGeneralInfoRequest $request) {

        $centerId = Auth::id();
        $generalInfo = null;

        if ($request->filled('id')) {
            $generalInfoId = $this->decryptId($request->get('id'));
            $generalInfo = GeneralInfo::find($generalInfoId) ?? null;
            $centerId = $generalInfo->center_id ?? $centerId;
        }

        $data = $request->only(['jalaliMonth', 'jalaliYear', 'bank_balance']);
        $data['center_id'] = $centerId;

        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $fileName = $this->getReceiptFileName($centerId, $request->get('jalaliMonth'), 
                $request->get('jalaliYear'), $receipt->getClientOriginalName());

            if ($generalInfo && $generalInfo->bank_statement_receipt) {
                Storage::disk('s3')->delete('bank_statement_receipts/' . $generalInfo->bank_statement_receipt);
            }

            $receipt->storeAs('bank_statement_receipts', $fileName, 's3');
            $data['bank_statement_receipt'] = $fileName;
        }   

        $generalInfo = GeneralInfo::updateOrCreate(['id' => $generalInfoId ?? null], $data);

        // The status of the user's brank receipts is NOT CONFIRMED by default
        $generalInfo->statuses()->create(['status' => Status::NOTCONFIRMED]);

        return $this->getAction($request->get('button_action'));
    }

    private function getReceiptFileName($centerId, $month, $year, $originalName)
    {
        $center = Center::find($centerId);
        $prefix = $center->type === Center::CENTER ? "GOL{$center->code}" : "GOLTEAM{$center->code}";
        return "{$prefix}/Y{$year}/M{$month}_{$originalName}";
    }

    public function edit(Request $request) {
        $id = $this->decryptId($request->get('id'));
        return $this->action->edit(GeneralInfo::class, $id);
    }

    public function confirmStatus(Request $request) {
        $id = $request->get('id');
        $generalInfo = GeneralInfo::findOrFail($id);

        $status = $request->get('status') == Status::CONFIRMED ? Status::CONFIRMED : Status::NOTCONFIRMED;
        $generalInfo->statuses()->update(['status' => $status]);

        return response()->json(['success' => true], Response::HTTP_CREATED);
    }

    public function details(Request $request) {
        $id = $this->decryptId($request->get('id'));
        $generalInfo = GeneralInfo::with('statuses')->findOrFail($id);
        return view('generalInfo.details', ['generalInfo' => $generalInfo]);
    }

    public function delete(Request $request) {
        $id = $this->decryptId($request->get('id'));
        $generalInfo = GeneralInfo::findOrFail($id);

        Storage::disk('s3')->delete('bank_statement_receipts/' . $generalInfo->bank_statement_receipt);
        return $this->action->delete(GeneralInfo::class, $id);
    }

    private function decryptId($encryptedId) {
        return $encryptedId ? Crypt::decryptString($encryptedId) : null;
    }
}