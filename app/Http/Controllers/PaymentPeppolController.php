<?php

namespace App\Http\Controllers;

use App\Services\PaymentPeppolService;
use App\DTOs\PaymentPeppolDTO;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentPeppolController extends Controller
{
    public function __construct(
        private PaymentPeppolService $service
    ) {}

    public function index(): View
    {
        $paymentPeppols = $this->service->getAll();
        
        return view('paymentpeppol.index', compact('paymentPeppols'));
    }

    public function add(int $inv_id): View
    {
        return view('paymentpeppol.form', [
            'paymentPeppol' => null,
            'inv_id' => $inv_id,
            'actionRoute' => route('paymentpeppol.store', ['inv_id' => $inv_id]),
        ]);
    }

    public function store(Request $request, int $inv_id): RedirectResponse
    {
        $validated = $request->validate([
            'provider' => 'required|max:20',
        ]);

        $validated['inv_id'] = $inv_id;
        $validated['auto_reference'] = time();
        
        $dto = new PaymentPeppolDTO(...$validated);
        $this->service->create($dto);

        return redirect()->route('paymentpeppol.index')
            ->with('success', 'Payment Peppol record created successfully.');
    }

    public function edit(int $id): View
    {
        $paymentPeppol = $this->service->getById($id);
        
        if (!$paymentPeppol) {
            abort(404, 'Payment Peppol not found');
        }

        return view('paymentpeppol.form', [
            'paymentPeppol' => $paymentPeppol,
            'inv_id' => $paymentPeppol->inv_id,
            'actionRoute' => route('paymentpeppol.update', ['id' => $id]),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'provider' => 'required|max:20',
        ]);

        $dto = new PaymentPeppolDTO(...array_merge(['id' => $id], $validated));
        $this->service->update($id, $dto);

        return redirect()->route('paymentpeppol.index')
            ->with('success', 'Payment Peppol record updated successfully.');
    }

    public function delete(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('paymentpeppol.index')
            ->with('success', 'Payment Peppol record deleted successfully.');
    }
}
