<?php

namespace App\Http\Controllers;

use App\DTOs\TaxRateDTO;
use App\Services\TaxRateService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class TaxRateController extends Controller
{
    public function __construct(
        private TaxRateService $taxRateService
    ) {
        $this->middleware(['auth', 'permission:manage-invoices']);
    }

    public function index()
    {
        $taxRates = $this->taxRateService->getAll();
        
        $title = 'Tax Rates';
        $content = '';
        ob_start();
        ?>
        <h1>Tax Rates</h1>
        <a href="<?= route('tax-rates.create') ?>">Create New Tax Rate</a>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Percentage</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($taxRates as $taxRate): ?>
                <tr>
                    <td><?= htmlspecialchars($taxRate->name) ?></td>
                    <td><?= htmlspecialchars($taxRate->percent) ?>%</td>
                    <td><?= $taxRate->is_active ? 'Yes' : 'No' ?></td>
                    <td>
                        <a href="<?= route('tax-rates.edit', $taxRate->id) ?>">Edit</a>
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
        $title = 'Create Tax Rate';
        $content = '';
        ob_start();
        ?>
        <h1>Create Tax Rate</h1>
        <form method="POST" action="<?= route('tax-rates.store') ?>">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            
            <label>Name:</label>
            <input type="text" name="name" required>
            
            <label>Percentage:</label>
            <input type="number" step="0.01" name="percent" required>
            
            <label>Active:</label>
            <input type="checkbox" name="is_active" value="1" checked>
            
            <button type="submit">Create Tax Rate</button>
        </form>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../../resources/views/layout.php';
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'percent' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $dto = new TaxRateDTO(...$validated);
        $this->taxRateService->create($dto);

        return redirect()->route('tax-rates.index')
            ->with('success', 'Tax rate created successfully');
    }

    public function edit(int $id)
    {
        $taxRate = $this->taxRateService->findById($id);
        
        $title = 'Edit Tax Rate';
        $content = '';
        ob_start();
        ?>
        <h1>Edit Tax Rate</h1>
        <form method="POST" action="<?= route('tax-rates.update', $taxRate->id) ?>">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="_method" value="PUT">
            
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($taxRate->name) ?>" required>
            
            <label>Percentage:</label>
            <input type="number" step="0.01" name="percent" value="<?= htmlspecialchars($taxRate->percent) ?>" required>
            
            <label>Active:</label>
            <input type="checkbox" name="is_active" value="1" <?= $taxRate->is_active ? 'checked' : '' ?>>
            
            <button type="submit">Update Tax Rate</button>
        </form>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../../resources/views/layout.php';
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'percent' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['id'] = $id;
        $validated['is_active'] = $request->has('is_active');
        $dto = new TaxRateDTO(...$validated);
        $this->taxRateService->update($dto);

        return redirect()->route('tax-rates.index')
            ->with('success', 'Tax rate updated successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->taxRateService->delete($id);

        return redirect()->route('tax-rates.index')
            ->with('success', 'Tax rate deleted successfully');
    }
}
