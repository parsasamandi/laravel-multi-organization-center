<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreReportRequest;
use App\DataTables\ReportDataTable;
use App\Providers\SuccessMessages;
use App\Providers\Action;
use App\Models\Report;
use App\Models\GeneralInfo;
use App\Models\Status;
use App\Models\Center;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function list()
    {
        $reportTable = new ReportDataTable();
        return view('report.list', ['reportTable' => $reportTable->html()]);
    }

    public function reportTable(ReportDataTable $reportTable)
    {
        return $reportTable->render('report.list');
    }

    public function store(StoreReportRequest $request)
    {
        $centerId = Auth::id();
        $report = null;

        if ($request->filled('id')) {
            $reportId = $this->decryptId($request->get('id'));
            $report = Report::find($reportId);
            $centerId = $report->center_id ?? $centerId;
        }

        $data = $request->only(['expenses', 'range', 'type', 'description']);
        $data['center_id'] = $centerId;

        $generalInfo = GeneralInfo::where([
            'center_id' => $centerId,
            'jalaliMonth' => $request->get('jalaliMonth'),
            'jalaliYear' => $request->get('jalaliYear')
        ])->first();

        $data['general_info_id'] = $generalInfo->id;

        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $fileName = $this->getReceiptFileName($centerId, 
                $request->get('jalaliMonth'), $request->get('jalaliYear'), $receipt->getClientOriginalName());
            
            if ($report) {
                Storage::disk('s3')->delete('receipt/' . $report->receipt);
            }

            $receipt->storeAs('receipt', $fileName, 's3');
            $data['receipt'] = $fileName;
        }

        $report = Report::updateOrCreate(['id' => $reportId ?? null], $data);

        // The status of the user's expense reports is NOT CONFIRMED by default
        $report->statuses()->create(['status' => Status::NOTCONFIRMED]);

        return $this->getAction($request->get('button_action'));
    }

    private function getReceiptFileName($centerId, $month, $year, $originalName)
    {
        $center = Center::find($centerId);
        $prefix = $center->type === Center::CENTER ? "GOL{$center->code}" : "GOLTEAM{$center->code}";
        return "{$prefix}/Y{$year}/M{$month}_{$originalName}";
    }

    public function edit(Request $request)
    {
        $id = $this->decryptId($request->get('id'));

        $report = Report::with('generalInfo:id,jalaliMonth,jalaliYear')
            ->select('id', 'center_id', 'general_info_id', 'expenses', 'range', 'receipt', 'description', 'type')
            ->find($id);

        return response()->json($report);
    }

    public function confirmStatus(Request $request)
    {
        $id = $request->get('id');
        $report = Report::findOrFail($id);

        $status = $request->get('status') == Status::CONFIRMED ? Status::CONFIRMED : Status::NOTCONFIRMED;
        $report->statuses()->update(['status' => $status]);

        return response()->json(['success' => true], Response::HTTP_CREATED);
    }

    public function details(Request $request)
    {
        $id = $this->decryptId($request->get('id'));
        $report = Report::with('generalInfo')->findOrFail($id);

        return view('report.details', ['report' => $report]);
    }

    public function delete(Request $request)
    {
        $id = $this->decryptId($request->get('id'));
        $report = Report::findOrFail($id);

        Storage::disk('s3')->delete('receipt/' . $report->receipt);

        return $this->action->delete(Report::class, $id);
    }

    private function decryptId($encryptedId)
    {
        return $encryptedId ? Crypt::decryptString($encryptedId) : null;
    }
}
