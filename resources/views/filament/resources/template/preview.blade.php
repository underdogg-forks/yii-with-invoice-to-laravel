<x-filament::section>
    <x-slot name="heading">
        {{ $template->name }}
    </x-slot>
    
    @if($template->subject)
        <x-slot name="description">
            <strong>Subject:</strong> {{ $template->subject }}
        </x-slot>
    @endif
    
    <div class="space-y-4">
        @if($template->description)
            <x-filament::section>
                <x-slot name="heading">
                    Description
                </x-slot>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $template->description }}
                </div>
            </x-filament::section>
        @endif
        
        <x-filament::section>
            <x-slot name="heading">
                Template Content
            </x-slot>
            
            <div class="prose prose-sm max-w-none dark:prose-invert">
                {!! $template->content !!}
            </div>
        </x-filament::section>
        
        <x-filament::section>
            <x-slot name="heading">
                Template Details
            </x-slot>
            
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="font-medium text-gray-500 dark:text-gray-400">Category</dt>
                    <dd class="mt-1">
                        <x-filament::badge :color="match($template->category->value) {
                            'standard' => 'primary',
                            'custom' => 'success',
                            'system' => 'warning',
                            'archived' => 'danger',
                            default => 'gray',
                        }">
                            {{ $template->category->label() }}
                        </x-filament::badge>
                    </dd>
                </div>
                
                <div>
                    <dt class="font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        <x-filament::badge :color="$template->is_active ? 'success' : 'danger'">
                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                        </x-filament::badge>
                    </dd>
                </div>
                
                @if($template->is_default)
                    <div>
                        <dt class="font-medium text-gray-500 dark:text-gray-400">Default Template</dt>
                        <dd class="mt-1">
                            <x-filament::badge color="warning">
                                <x-slot name="icon">
                                    heroicon-o-star
                                </x-slot>
                                Yes
                            </x-filament::badge>
                        </dd>
                    </div>
                @endif
                
                <div>
                    <dt class="font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">
                        {{ $template->updated_at->diffForHumans() }}
                    </dd>
                </div>
            </dl>
        </x-filament::section>
    </div>
</x-filament::section>
