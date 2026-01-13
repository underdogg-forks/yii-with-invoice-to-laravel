<?php

namespace App\Http\Controllers;

use App\Services\UnitPeppolService;
use App\DTOs\UnitPeppolDTO;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UnitPeppolController extends Controller
{
    public function __construct(
        private UnitPeppolService $service
    ) {}

    public function index(): View
    {
        $unitPeppols = $this->service->getAll();
        
        return view('unitpeppol.index', compact('unitPeppols'));
    }

    public function add(int $unit_id): View
    {
        return view('unitpeppol.form', [
            'unitPeppol' => null,
            'unit_id' => $unit_id,
            'actionRoute' => route('unitpeppol.store', ['unit_id' => $unit_id]),
        ]);
    }

    public function store(Request $request, int $unit_id): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|max:3',
            'name' => 'required|max:120',
            'description' => 'nullable',
        ]);

        $validated['unit_id'] = $unit_id;
        
        $dto = new UnitPeppolDTO(...$validated);
        $this->service->create($dto);

        return redirect()->route('unitpeppol.index')
            ->with('success', 'Unit Peppol record created successfully.');
    }

    public function edit(int $id): View
    {
        $unitPeppol = $this->service->getById($id);
        
        if (!$unitPeppol) {
            abort(404, 'Unit Peppol not found');
        }

        return view('unitpeppol.form', [
            'unitPeppol' => $unitPeppol,
            'unit_id' => $unitPeppol->unit_id,
            'actionRoute' => route('unitpeppol.update', ['id' => $id]),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|max:3',
            'name' => 'required|max:120',
            'description' => 'nullable',
        ]);

        $dto = new UnitPeppolDTO(id: $id, ...$validated);
        $this->service->update($id, $dto);

        return redirect()->route('unitpeppol.index')
            ->with('success', 'Unit Peppol record updated successfully.');
    }

    public function delete(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('unitpeppol.index')
            ->with('success', 'Unit Peppol record deleted successfully.');
    }
}
