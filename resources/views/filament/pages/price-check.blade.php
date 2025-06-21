<x-filament-panels::page>

    <div  class="max-w-lg flex gap-2 items-start"
          x-data="{timer:null}"
          x-on:pc-start.window="
                clearTimeout(timer);
                timer = setTimeout(() => $wire.set('query',''), 100);
          ">
        {{-- input pencarian --}}
        <input  type="text"
                wire:model.lazy="query"          {{-- tekan Enter = trigger search --}}
                placeholder="Scan / ketik nama • SKU • barcode …"
                autofocus
                class="filament-input w-full border rounded-md px-3 py-2
                       dark:bg-gray-800 dark:border-gray-700" />

        {{-- tombol Cari --}}
        <x-filament::button wire:click="search" wire:keydown.enter.prevent="search">
            Cari
        </x-filament::button>
    </div>

    {{-- Tabel hasil --}}
    @if(trim($this->query) !== '')
        <div class="mt-6">
            {{ $this->table }}
        </div>
    @endif

</x-filament-panels::page>
