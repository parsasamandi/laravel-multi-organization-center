<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentTransferRequest;
use App\DataTables\PaymentTransferDataTable;
use App\Providers\SuccessMessages;
use App\Providers\Action;
use App\Models\PaymentTransfer;
use App\Models\Center;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentTransferController extends Controller
{
    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function list()
    {
        $paymentTransferTable = new PaymentTransferDataTable();
        $centers = Center::select('id', 'name_en')
            ->where('type', Center::CENTER)
            ->orderBy('code', 'asc') // Sort by 'code' in ascending order
            ->get();

        return view('paymentTransfer.list', [
            'paymentTransferTable' => $paymentTransferTable->html(),
            'centers' => $centers, 
        ]);
    }

    public function paymentTransferTable(PaymentTransferDataTable $paymentTransferTable)
    {
        return $paymentTransferTable->render('paymentTransfer.list');
    }

    public function store(StorePaymentTransferRequest $request)
    {
        // Decrypt the ID if provided and find the existing payment transfer
        $paymentTransferId = $request->filled('id') 
            ? optional(PaymentTransfer::find($this->decryptId($request->get('id'))))->id 
            : null;

        // Collect validated data and replace empty strings with null
        $data = array_map(
            fn($value) => $value === '' ? null : $value,
            $request->only([
                'cad_to_usd_rate', 'total_rial', 'total_cad', 
                'outfit', 'education', 'salary', 
                'food', 'misc', 'misc_desc', 'date', 'center_id'
            ])
        );

        // Resolve center_id if provided
        if (!empty($data['center_id'])) {
            $data['center_id'] = optional(Center::find($data['center_id']))->id;
        }

        // Default the date to now if not provided
        $data['date'] = $data['date'] ?? now();

        // Update or create the payment transfer
        PaymentTransfer::updateOrCreate(['id' => $paymentTransferId], $data);

        // Handle the action based on button input
        return $this->getAction($request->get('button_action'));
    }


    public function edit(Request $request)
    {
        // Decrypt the ID from the request to find the center
        $id = $this->decryptId($request->get('id'));
        // Use the Action service to handle the edit process
        return $this->action->edit(PaymentTransfer::class, $id);
    }

    public function details(Request $request)
    {
        $id = $this->decryptId($request->get('id'));
        $paymentTransfer = PaymentTransfer::findOrFail($id);

        // Set $english to true or false as needed
        $english = true; // Hardcoded to true for English mode

        return view('paymentTransfer.details', [
            'paymentTransfer' => $paymentTransfer,
            'english' => $english, 
        ]);
    }

    public function delete(Request $request)
    {
        // Decrypt the ID from the request to find the center
        $id = $this->decryptId($request->get('id'));
        // Use the Action service to handle the deletion process
        return $this->action->delete(PaymentTransfer::class, $id);
    }

    // Helper method to decrypt an encrypted ID
    private function decryptId($encryptedId) {
        return $encryptedId ? Crypt::decryptString($encryptedId) : null;
    }
}
