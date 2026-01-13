<?php

namespace App\Http\Controllers;

use App\DTOs\CustomFieldDTO;
use App\Services\CustomFieldService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CustomFieldController extends Controller
{
    public function __construct(
        private CustomFieldService $customFieldService
    ) {
        $this->middleware(['auth', 'permission:manage-settings']);
    }

    public function index()
    {
        $customFields = $this->customFieldService->getAll();
        
        return view('custom-fields.index', compact('customFields'));
    }

    public function create()
    {
        return view('custom-fields.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'custom_field_table' => 'required|string|max:50',
            'custom_field_label' => 'required|string|max:255',
            'custom_field_type' => 'required|in:text,textarea,checkbox,select,date,number',
            'custom_field_order' => 'nullable|integer',
        ]);
        
        $dto = new CustomFieldDTO(...$validated);
        $this->customFieldService->create($dto);
        
        return redirect()
            ->route('custom-fields.index')
            ->with('success', 'Custom field created successfully');
    }

    public function edit(int $id)
    {
        $customField = $this->customFieldService->getById($id);
        
        if (!$customField) {
            abort(404);
        }
        
        return view('custom-fields.edit', compact('customField'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'custom_field_table' => 'required|string|max:50',
            'custom_field_label' => 'required|string|max:255',
            'custom_field_type' => 'required|in:text,textarea,checkbox,select,date,number',
            'custom_field_order' => 'nullable|integer',
        ]);
        
        $dto = new CustomFieldDTO(custom_field_id: $id, ...$validated);
        $this->customFieldService->update($dto);
        
        return redirect()
            ->route('custom-fields.index')
            ->with('success', 'Custom field updated successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->customFieldService->delete($id);
        
        return redirect()
            ->route('custom-fields.index')
            ->with('success', 'Custom field deleted successfully');
    }
}
