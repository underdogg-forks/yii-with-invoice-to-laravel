@props(['invoice'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-end">
        <div class="w-full md:w-1/2 lg:w-1/3">
            <dl class="space-y-2">
                <div class="flex justify-between text-sm">
                    <dt class="font-medium text-gray-600 dark:text-gray-400">Subtotal:</dt>
                    <dd class="text-gray-900 dark:text-white">${{ number_format($invoice->invoice_subtotal ?? 0, 2) }}</dd>
                </div>
                
                @if($invoice->invoice_discount_amount ?? 0)
                <div class="flex justify-between text-sm">
                    <dt class="font-medium text-gray-600 dark:text-gray-400">
                        Discount
                        @if($invoice->invoice_discount_percent ?? 0)
                        ({{ number_format($invoice->invoice_discount_percent, 2) }}%)
                        @endif:
                    </dt>
                    <dd class="text-red-600 dark:text-red-400">-${{ number_format($invoice->invoice_discount_amount, 2) }}</dd>
                </div>
                @endif
                
                <div class="flex justify-between text-sm">
                    <dt class="font-medium text-gray-600 dark:text-gray-400">Tax:</dt>
                    <dd class="text-gray-900 dark:text-white">${{ number_format($invoice->invoice_tax_total ?? 0, 2) }}</dd>
                </div>
                
                @if($invoice->invoice_shipping_amount ?? 0)
                <div class="flex justify-between text-sm">
                    <dt class="font-medium text-gray-600 dark:text-gray-400">Shipping:</dt>
                    <dd class="text-gray-900 dark:text-white">${{ number_format($invoice->invoice_shipping_amount, 2) }}</dd>
                </div>
                @endif
                
                <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                    <dt class="text-gray-900 dark:text-white">Total:</dt>
                    <dd class="text-gray-900 dark:text-white">${{ number_format($invoice->invoice_total ?? 0, 2) }}</dd>
                </div>
                
                @if($invoice->invoice_amount_paid ?? 0)
                <div class="flex justify-between text-sm text-green-600 dark:text-green-400">
                    <dt class="font-medium">Amount Paid:</dt>
                    <dd>-${{ number_format($invoice->invoice_amount_paid, 2) }}</dd>
                </div>
                @endif
                
                @if(($invoice->invoice_total ?? 0) - ($invoice->invoice_amount_paid ?? 0) > 0)
                <div class="flex justify-between text-base font-bold text-red-600 dark:text-red-400 border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                    <dt>Balance Due:</dt>
                    <dd>${{ number_format(($invoice->invoice_total ?? 0) - ($invoice->invoice_amount_paid ?? 0), 2) }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
    
    @if($invoice->invoice_notes ?? null)
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Notes:</h4>
        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $invoice->invoice_notes }}</p>
    </div>
    @endif
    
    @if($invoice->invoice_terms ?? null)
    <div class="mt-4">
        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Terms & Conditions:</h4>
        <p class="text-xs text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $invoice->invoice_terms }}</p>
    </div>
    @endif
</div>
