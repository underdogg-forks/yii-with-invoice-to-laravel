<?php

namespace App\Http\Controllers;

use App\DTOs\ClientDTO;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function __construct(
        private ClientService $clientService
    ) {
        $this->middleware(['auth', 'permission:manage-clients']);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $group = $request->get('group');
        $active = $request->get('active');
        
        $clients = $this->clientService->search($search, [
            'group' => $group,
            'active' => $active,
        ]);
        
        return view('clients.index', compact('clients', 'search', 'group', 'active'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_surname' => 'nullable|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'nullable|string|max:50',
            'client_mobile' => 'nullable|string|max:50',
            'client_fax' => 'nullable|string|max:50',
            'client_address_1' => 'nullable|string|max:255',
            'client_address_2' => 'nullable|string|max:255',
            'client_building_number' => 'nullable|string|max:50',
            'client_city' => 'nullable|string|max:100',
            'client_state' => 'nullable|string|max:100',
            'client_zip' => 'nullable|string|max:20',
            'client_country' => 'nullable|string|max:100',
            'client_vat_id' => 'nullable|string|max:100',
            'client_tax_code' => 'nullable|string|max:100',
            'client_web' => 'nullable|string|max:255',
            'client_birthdate' => 'nullable|date',
            'client_gender' => 'nullable|in:0,1,2',
            'client_language' => 'nullable|string|max:10',
            'client_active' => 'boolean',
            'client_group' => 'nullable|string|max:100',
        ]);
        
        $dto = new ClientDTO(...$validated);
        $this->clientService->create($dto);
        
        return redirect()
            ->route('clients.index')
            ->with('success', 'Client created successfully');
    }

    public function show(int $id)
    {
        $client = $this->clientService->getById($id);
        
        if (!$client) {
            abort(404);
        }
        
        return view('clients.show', compact('client'));
    }

    public function edit(int $id)
    {
        $client = $this->clientService->getById($id);
        
        if (!$client) {
            abort(404);
        }
        
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_surname' => 'nullable|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'nullable|string|max:50',
            'client_mobile' => 'nullable|string|max:50',
            'client_fax' => 'nullable|string|max:50',
            'client_address_1' => 'nullable|string|max:255',
            'client_address_2' => 'nullable|string|max:255',
            'client_building_number' => 'nullable|string|max:50',
            'client_city' => 'nullable|string|max:100',
            'client_state' => 'nullable|string|max:100',
            'client_zip' => 'nullable|string|max:20',
            'client_country' => 'nullable|string|max:100',
            'client_vat_id' => 'nullable|string|max:100',
            'client_tax_code' => 'nullable|string|max:100',
            'client_web' => 'nullable|string|max:255',
            'client_birthdate' => 'nullable|date',
            'client_gender' => 'nullable|in:0,1,2',
            'client_language' => 'nullable|string|max:10',
            'client_active' => 'boolean',
            'client_group' => 'nullable|string|max:100',
        ]);
        
        $dto = new ClientDTO(...array_merge(['client_id' => $id], $validated));
        $this->clientService->update($dto);
        
        return redirect()
            ->route('clients.show', $id)
            ->with('success', 'Client updated successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->clientService->delete($id);
        
        return redirect()
            ->route('clients.index')
            ->with('success', 'Client deleted successfully');
    }
}
