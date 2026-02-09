<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->schema }}

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
            @foreach ($this->getCachedFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
</x-filament-panels::page>
