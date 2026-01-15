<?php

namespace App\Http\Controllers;

use App\Services\ClientPeppolService;
use App\DTOs\ClientPeppolDTO;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClientPeppolController extends Controller
{
    public function __construct(
        private ClientPeppolService $service
    ) {}

    public function index(): View
    {
        $clientPeppols = $this->service->getAll();
        
        return view('clientpeppol.index', compact('clientPeppols'));
    }

    public function add(int $client_id): View
    {
        return view('clientpeppol.form', [
            'clientPeppol' => null,
            'client_id' => $client_id,
            'actionRoute' => route('clientpeppol.store', ['client_id' => $client_id]),
        ]);
    }

    public function store(Request $request, int $client_id): RedirectResponse
    {
        $validated = $request->validate([
            'endpointid' => 'required|email|max:100',
            'endpointid_schemeid' => 'required|max:4',
            'identificationid' => 'required|max:100',
            'identificationid_schemeid' => 'required|max:4',
            'taxschemecompanyid' => 'required|max:100',
            'taxschemeid' => 'required|max:7',
            'legal_entity_registration_name' => 'required|max:100',
            'legal_entity_companyid' => 'required|max:100',
            'legal_entity_companyid_schemeid' => 'required|max:5',
            'legal_entity_company_legal_form' => 'required|max:50',
            'financial_institution_branchid' => 'required|max:20',
            'accounting_cost' => 'required|max:30',
            'supplier_assigned_accountid' => 'required|max:20',
            'buyer_reference' => 'required|max:20',
        ]);

        $validated['client_id'] = $client_id;
        
        $dto = new ClientPeppolDTO(...$validated);
        $this->service->create($dto);

        return redirect()->route('clientpeppol.index')
            ->with('success', 'Client Peppol record created successfully.');
    }

    public function edit(int $id): View
    {
        $clientPeppol = $this->service->getById($id);
        
        if (!$clientPeppol) {
            abort(404, 'Client Peppol not found');
        }

        return view('clientpeppol.form', [
            'clientPeppol' => $clientPeppol,
            'client_id' => $clientPeppol->client_id,
            'actionRoute' => route('clientpeppol.update', ['id' => $id]),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'endpointid' => 'required|email|max:100',
            'endpointid_schemeid' => 'required|max:4',
            'identificationid' => 'required|max:100',
            'identificationid_schemeid' => 'required|max:4',
            'taxschemecompanyid' => 'required|max:100',
            'taxschemeid' => 'required|max:7',
            'legal_entity_registration_name' => 'required|max:100',
            'legal_entity_companyid' => 'required|max:100',
            'legal_entity_companyid_schemeid' => 'required|max:5',
            'legal_entity_company_legal_form' => 'required|max:50',
            'financial_institution_branchid' => 'required|max:20',
            'accounting_cost' => 'required|max:30',
            'supplier_assigned_accountid' => 'required|max:20',
            'buyer_reference' => 'required|max:20',
        ]);

        $dto = new ClientPeppolDTO(...array_merge(['id' => $id], $validated));
        $this->service->update($id, $dto);

        return redirect()->route('clientpeppol.index')
            ->with('success', 'Client Peppol record updated successfully.');
    }

    public function view(int $id): View
    {
        $clientPeppol = $this->service->getById($id);
        
        if (!$clientPeppol) {
            abort(404, 'Client Peppol not found');
        }

        return view('clientpeppol.view', compact('clientPeppol'));
    }

    public function delete(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('clientpeppol.index')
            ->with('success', 'Client Peppol record deleted successfully.');
    }
}
