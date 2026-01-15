@props(['invoice'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                INVOICE
            </h1>
            <p class="text-lg text-gray-700 dark:text-gray-300">
                <strong>Invoice #{{ $invoice->invoice_number }}</strong>
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Date: {{ $invoice->invoice_date_created ? $invoice->invoice_date_created->format('d-m-Y') : date('d-m-Y') }}
            </p>
            @if($invoice->invoice_date_due)
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Due Date: {{ $invoice->invoice_date_due->format('d-m-Y') }}
            </p>
            @endif
        </div>
        <div class="text-right">
            @if($invoice->status)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                {{ $invoice->status->value === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                {{ $invoice->status->value === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                {{ $invoice->status->value === 'sent' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                {{ $invoice->status->value === 'overdue' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                {{ $invoice->status->label() }}
            </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mt-6">
        <!-- From Section -->
        <div>
            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 border-b border-gray-200 dark:border-gray-700 pb-1">
                From:
            </h3>
            <div class="text-sm text-gray-900 dark:text-white">
                <p class="font-semibold">{{ config('app.name', 'Company Name') }}</p>
                @if(config('peppol.supplier.address.street'))
                <p>{{ config('peppol.supplier.address.street') }}</p>
                @endif
                @if(config('peppol.supplier.address.postal_code') || config('peppol.supplier.address.city'))
                <p>{{ config('peppol.supplier.address.postal_code') }} {{ config('peppol.supplier.address.city') }}</p>
                @endif
                @if(config('peppol.supplier.address.country_code'))
                <p>{{ config('peppol.supplier.address.country_code') }}</p>
                @endif
                @if(config('peppol.supplier.vat_number'))
                <p class="mt-2">VAT: {{ config('peppol.supplier.vat_number') }}</p>
                @endif
            </div>
        </div>

        <!-- Bill To Section -->
        <div>
            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 border-b border-gray-200 dark:border-gray-700 pb-1">
                Bill To:
            </h3>
            <div class="text-sm text-gray-900 dark:text-white">
                <p class="font-semibold">{{ $invoice->client->name ?? 'N/A' }}</p>
                @if($invoice->client->client_address_1 ?? null)
                <p>{{ $invoice->client->client_address_1 }}</p>
                @endif
                @if($invoice->client->client_address_2 ?? null)
                <p>{{ $invoice->client->client_address_2 }}</p>
                @endif
                @if($invoice->client->client_city ?? null)
                <p>{{ $invoice->client->client_zip }} {{ $invoice->client->client_city }}</p>
                @endif
                @if($invoice->client->client_country ?? null)
                <p>{{ $invoice->client->client_country }}</p>
                @endif
                @if($invoice->client->client_vat_id ?? null)
                <p class="mt-2">VAT: {{ $invoice->client->client_vat_id }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
