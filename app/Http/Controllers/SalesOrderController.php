<?php

namespace App\Http\Controllers;

use App\Services\SalesOrderService;
use App\DTOs\SalesOrderDTO;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class SalesOrderController extends Controller
{
    public function __construct(
        private SalesOrderService $salesOrderService
    ) {
        $this->middleware(['auth', 'permission:manage-quotes']);
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $clientId = $request->query('client_id');
        $search = $request->query('search');

        $salesOrders = $this->salesOrderService->getAll($status, $clientId, $search);

        return response()->json($salesOrders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'so_number' => 'required|string|max:100|unique:sales_orders,so_number',
            'reference' => 'nullable|string|max:255',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after:order_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_total' => 'required|numeric|min:0',
            'discount_total' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $dto = new SalesOrderDTO(
            client_id: $validated['client_id'],
            so_number: $validated['so_number'],
            order_date: $validated['order_date'],
            subtotal: $validated['subtotal'],
            tax_total: $validated['tax_total'],
            discount_total: $validated['discount_total'] ?? 0,
            total: $validated['total'],
            quote_id: $validated['quote_id'] ?? null,
            reference: $validated['reference'] ?? null,
            delivery_date: $validated['delivery_date'] ?? null,
            notes: $validated['notes'] ?? null,
            terms: $validated['terms'] ?? null
        );

        $salesOrder = $this->salesOrderService->create($dto);

        return response()->json($salesOrder, 201);
    }

    public function show(int $id): JsonResponse
    {
        $salesOrder = $this->salesOrderService->getById($id);

        if (!$salesOrder) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }

        return response()->json($salesOrder);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'sometimes|required|exists:clients,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'so_number' => 'sometimes|required|string|max:100',
            'reference' => 'nullable|string|max:255',
            'order_date' => 'sometimes|required|date',
            'delivery_date' => 'nullable|date',
            'subtotal' => 'sometimes|required|numeric|min:0',
            'tax_total' => 'sometimes|required|numeric|min:0',
            'discount_total' => 'nullable|numeric|min:0',
            'total' => 'sometimes|required|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $dto = new SalesOrderDTO(
            id: $id,
            client_id: $validated['client_id'] ?? null,
            so_number: $validated['so_number'] ?? '',
            order_date: $validated['order_date'] ?? '',
            subtotal: $validated['subtotal'] ?? 0,
            tax_total: $validated['tax_total'] ?? 0,
            discount_total: $validated['discount_total'] ?? 0,
            total: $validated['total'] ?? 0,
            quote_id: $validated['quote_id'] ?? null,
            reference: $validated['reference'] ?? null,
            delivery_date: $validated['delivery_date'] ?? null,
            notes: $validated['notes'] ?? null,
            terms: $validated['terms'] ?? null
        );

        $salesOrder = $this->salesOrderService->update($dto);

        if (!$salesOrder) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }

        return response()->json($salesOrder);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->salesOrderService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }

        return response()->json(['message' => 'Sales Order deleted successfully']);
    }

    public function confirm(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $salesOrder = $this->salesOrderService->confirm($id, $userId);
            return response()->json($salesOrder);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function complete(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $salesOrder = $this->salesOrderService->complete($id, $userId);
            return response()->json($salesOrder);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        try {
            $userId = $request->user()->id;
            $salesOrder = $this->salesOrderService->cancel($id, $userId, $validated['cancellation_reason']);
            return response()->json($salesOrder);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function convertToInvoice(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $invoice = $this->salesOrderService->convertToInvoice($id, $userId);
            return response()->json($invoice);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
