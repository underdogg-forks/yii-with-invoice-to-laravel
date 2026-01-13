<?php

namespace App\Http\Controllers;

use App\Services\QuoteService;
use App\DTOs\QuoteDTO;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    public function __construct(
        private QuoteService $quoteService
    ) {
        $this->middleware(['auth', 'permission:manage-quotes']);
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $clientId = $request->query('client_id');
        $search = $request->query('search');

        $quotes = $this->quoteService->getAll($status, $clientId, $search);

        return response()->json($quotes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quote_number' => 'required|string|max:100|unique:quotes,quote_number',
            'reference' => 'nullable|string|max:255',
            'quote_date' => 'required|date',
            'expiry_date' => 'required|date|after:quote_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_total' => 'required|numeric|min:0',
            'discount_total' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $dto = new QuoteDTO(
            client_id: $validated['client_id'],
            quote_number: $validated['quote_number'],
            reference: $validated['reference'] ?? null,
            quote_date: $validated['quote_date'],
            expiry_date: $validated['expiry_date'],
            subtotal: $validated['subtotal'],
            tax_total: $validated['tax_total'],
            discount_total: $validated['discount_total'] ?? 0,
            total: $validated['total'],
            notes: $validated['notes'] ?? null,
            terms: $validated['terms'] ?? null
        );

        $quote = $this->quoteService->create($dto);

        return response()->json($quote, 201);
    }

    public function show(int $id): JsonResponse
    {
        $quote = $this->quoteService->getById($id);

        if (!$quote) {
            return response()->json(['message' => 'Quote not found'], 404);
        }

        return response()->json($quote);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'sometimes|required|exists:clients,id',
            'quote_number' => 'sometimes|required|string|max:100',
            'reference' => 'nullable|string|max:255',
            'quote_date' => 'sometimes|required|date',
            'expiry_date' => 'sometimes|required|date',
            'subtotal' => 'sometimes|required|numeric|min:0',
            'tax_total' => 'sometimes|required|numeric|min:0',
            'discount_total' => 'nullable|numeric|min:0',
            'total' => 'sometimes|required|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $dto = new QuoteDTO(
            id: $id,
            client_id: $validated['client_id'] ?? null,
            quote_number: $validated['quote_number'] ?? '',
            quote_date: $validated['quote_date'] ?? '',
            expiry_date: $validated['expiry_date'] ?? '',
            subtotal: $validated['subtotal'] ?? 0,
            tax_total: $validated['tax_total'] ?? 0,
            discount_total: $validated['discount_total'] ?? 0,
            total: $validated['total'] ?? 0,
            reference: $validated['reference'] ?? null,
            notes: $validated['notes'] ?? null,
            terms: $validated['terms'] ?? null
        );

        $quote = $this->quoteService->update($dto);

        if (!$quote) {
            return response()->json(['message' => 'Quote not found'], 404);
        }

        return response()->json($quote);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->quoteService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Quote not found'], 404);
        }

        return response()->json(['message' => 'Quote deleted successfully']);
    }

    public function send(int $id): JsonResponse
    {
        try {
            $quote = $this->quoteService->send($id);
            return response()->json($quote);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $quote = $this->quoteService->approve($id, $userId);
            return response()->json($quote);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        try {
            $userId = $request->user()->id;
            $quote = $this->quoteService->reject($id, $userId, $validated['rejection_reason']);
            return response()->json($quote);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function convertToSalesOrder(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $salesOrder = $this->quoteService->convertToSalesOrder($id, $userId);
            return response()->json($salesOrder);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
