<div class="space-y-4">
    <div class="border-b pb-4">
        <h3 class="text-lg font-semibold">{{ $template->name }}</h3>
        @if($template->subject)
            <p class="text-sm text-gray-600 mt-1"><strong>Subject:</strong> {{ $template->subject }}</p>
        @endif
        @if($template->description)
            <p class="text-sm text-gray-500 mt-1">{{ $template->description }}</p>
        @endif
    </div>
    
    <div class="prose max-w-none">
        <h4 class="text-md font-semibold mb-2">Template Content:</h4>
        <div class="bg-gray-50 p-4 rounded border">
            {!! $template->content !!}
        </div>
    </div>
    
    <div class="text-xs text-gray-500 pt-4 border-t">
        <p><strong>Type:</strong> {{ ucfirst($template->type->value ?? 'N/A') }}</p>
        <p><strong>Category:</strong> {{ $template->category->label() }}</p>
        <p><strong>Status:</strong> {{ $template->is_active ? 'Active' : 'Inactive' }}</p>
        <p><strong>Last Updated:</strong> {{ $template->updated_at->format('Y-m-d H:i:s') }}</p>
    </div>
</div>
