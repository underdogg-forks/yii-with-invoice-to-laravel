<?php

namespace App\Http\Controllers;

use App\DTOs\InvoiceDTO;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {
        $this->middleware(['auth', 'permission:manage-invoices']);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $invoices = $this->invoiceService->getAll($status);
        
        $title = 'Invoices';
        $content = '';
        ob_start();
        ?>
        <h1>Invoices</h1>
        <a href="<?= route('invoices.create') ?>">Create New Invoice</a>
        
        <table>
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td><?= htmlspecialchars($invoice->number ?? '') ?></td>
                    <td><?= htmlspecialchars($invoice->client->name ?? '') ?></td>
                    <td><?= htmlspecialchars($invoice->date_invoice ?? '') ?></td>
                    <td><?= htmlspecialchars($invoice->total ?? 0) ?></td>
                    <td><?= htmlspecialchars($invoice->status->name ?? '') ?></td>
                    <td>
                        <a href="<?= route('invoices.show', $invoice->id) ?>">View</a>
                        <a href="<?= route('invoices.edit', $invoice->id) ?>">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../../resources/views/layout.php';
    }

    public function create()
    {
        $title = 'Create Invoice';
        $content = '';
        ob_start();
        ?>
        <h1>Create Invoice</h1>
        <form method="POST" action="<?= route('invoices.store') ?>">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            
            <label>Client ID:</label>
            <input type="number" name="client_id" required>
            
            <label>Date:</label>
            <input type="date" name="date_invoice" value="<?= date('Y-m-d') ?>" required>
            
            <label>Due Date:</label>
            <input type="date" name="date_due" required>
            
            <button type="submit">Create Invoice</button>
        </form>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../../resources/views/layout.php';
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'numbering_id' => 'nullable|exists:invoice_numbering,id',
            'status_id' => 'nullable|exists:invoice_statuses,id',
            'date_invoice' => 'required|date',
            'date_due' => 'required|date|after_or_equal:date_invoice',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        $dto = new InvoiceDTO(...$validated);
        $this->invoiceService->create($dto);

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully');
    }

    public function show(int $id)
    {
        $invoice = $this->invoiceService->findById($id);
        
        $title = 'Invoice #' . $invoice->number;
        $content = '';
        ob_start();
        ?>
        <h1>Invoice #<?= htmlspecialchars($invoice->number) ?></h1>
        
        <p><strong>Client:</strong> <?= htmlspecialchars($invoice->client->name ?? '') ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($invoice->date_invoice) ?></p>
        <p><strong>Due Date:</strong> <?= htmlspecialchars($invoice->date_due) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($invoice->status->name ?? '') ?></p>
        <p><strong>Total:</strong> <?= htmlspecialchars($invoice->total ?? 0) ?></p>
        
        <h2>Line Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoice->items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->name ?? '') ?></td>
                    <td><?= htmlspecialchars($item->quantity) ?></td>
                    <td><?= htmlspecialchars($item->price) ?></td>
                    <td><?= htmlspecialchars($item->subtotal) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="<?= route('invoices.edit', $invoice->id) ?>">Edit</a>
        <a href="<?= route('invoices.index') ?>">Back to List</a>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../../resources/views/layout.php';
    }

    public function edit(int $id)
    {
        $invoice = $this->invoiceService->findById($id);
        
        $title = 'Edit Invoice #' . $invoice->number;
        $content = '';
        ob_start();
        ?>
        <h1>Edit Invoice #<?= htmlspecialchars($invoice->number) ?></h1>
        <form method="POST" action="<?= route('invoices.update', $invoice->id) ?>">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="_method" value="PUT">
            
            <label>Date:</label>
            <input type="date" name="date_invoice" value="<?= htmlspecialchars($invoice->date_invoice) ?>" required>
            
            <label>Due Date:</label>
            <input type="date" name="date_due" value="<?= htmlspecialchars($invoice->date_due) ?>" required>
            
            <label>Terms:</label>
            <textarea name="terms"><?= htmlspecialchars($invoice->terms ?? '') ?></textarea>
            
            <button type="submit">Update Invoice</button>
        </form>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../../resources/views/layout.php';
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'date_invoice' => 'required|date',
            'date_due' => 'required|date|after_or_equal:date_invoice',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        $validated['id'] = $id;
        $dto = new InvoiceDTO(...$validated);
        $this->invoiceService->update($dto);

        return redirect()->route('invoices.show', $id)
            ->with('success', 'Invoice updated successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->invoiceService->delete($id);

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully');
    }

    public function changeStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:invoice_statuses,id',
        ]);

        $this->invoiceService->changeStatus($id, $validated['status_id']);

        return redirect()->route('invoices.show', $id)
            ->with('success', 'Invoice status updated');
    }

    public function createCredit(int $id): RedirectResponse
    {
        $creditInvoice = $this->invoiceService->createCreditInvoice($id);

        return redirect()->route('invoices.show', $creditInvoice->id)
            ->with('success', 'Credit invoice created successfully');
    }
}
