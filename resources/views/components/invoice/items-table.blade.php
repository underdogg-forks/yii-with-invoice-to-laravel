@props(['items'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Description
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Quantity
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Price
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Tax
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Total
                </th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($items as $item)
            <tr>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                    <div class="font-medium">{{ $item->item_name ?? $item->item_description }}</div>
                    @if($item->item_description && $item->item_name !== $item->item_description)
                    <div class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $item->item_description }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white text-right">
                    {{ number_format($item->item_quantity, 2) }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white text-right">
                    ${{ number_format($item->item_price, 2) }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white text-right">
                    @if($item->item_tax_rate)
                    {{ number_format($item->item_tax_rate, 2) }}%
                    @else
                    -
                    @endif
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white text-right">
                    ${{ number_format($item->item_total ?? ($item->item_quantity * $item->item_price), 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    No items found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
