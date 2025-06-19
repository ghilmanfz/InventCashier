<x-filament-panels::page>
    <x-filament::grid class="gap-6 items-start" default="2">
        <x-filament::section>
            <x-slot name="heading">
                Select Product
            </x-slot>
            <x-slot name="description">
                Select the product you want to order.
            </x-slot>
            {{ $this->form }}
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">
                Detail Orders
            </x-slot>
            <div class="-mx-4 flow-root sm:mx-0">
                <form wire:submit="finalizeOrder">
                    @php($discount = $discount ?? ($record->discount ?? 0))
                    <x-table>
                        <colgroup>
                            <col class="w-full sm:w-1/2">
                            <col class="sm:w-1/6">
                            <col class="sm:w-1/6">
                            <col class="sm:w-1/6">
                        </colgroup>
                        <x-table.thead>
                            <tr>
                                <x-table.th>Name</x-table.th>
                                <x-table.th>Quantity</x-table.th>
                                <x-table.th>Price</x-table.th>
                            </tr>
                        </x-table.thead>
                        <tbody>
                        @forelse ($record->orderDetails as $orderDetail)
                            <x-table.tr>
                                <x-table.td>
                                    <div
                                        class="font-medium dark:text-white text-zinc-900">{{ $orderDetail->product->name }}</div>
                                    <div class="mt-1 truncate text-zinc-500 dark:text-zinc-400">
                                        The current stock is {{ $orderDetail->product->stock_quantity }}
                                    </div>
                                </x-table.td>
                                <x-table.td>
                                    <input
                                        class="w-20 text-sm h-8 dark:bg-zinc-800 dark:text-white rounded-md border shadow-sm border-zinc-200 dark:border-zinc-700"
                                        type="number"
                                        value="{{ $orderDetail->quantity }}"
                                        wire:change="updateQuantity({{ $orderDetail->id }}, $event.target.value)"
                                        min="1"
                                        max="{{ $orderDetail->product->stock_quantity }}"
                                    />
                                </x-table.td>
                                <x-table.td class="text-right">
                                    {{ number_format($orderDetail->price * $orderDetail->quantity) }}
                                </x-table.td>
                                <x-table.td>
                                    <button type="button" wire:click="removeProduct({{ $orderDetail->id }})">
                                        @svg('heroicon-o-x-mark', [ 'width' => '20px' ])
                                    </button>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div
                                        class="py-5 pl-4 pr-3 text-sm sm:pl-0 text-center dark:text-zinc-500 text-zinc-500">
                                        No products selected.
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <!-- More projects... -->
                        </tbody>
                        <tfoot>
                        <tr>
                            <th scope="row" colspan="2"
                                class="hidden pl-4 pr-3 pt-6 text-right text-sm font-normal text-zinc-500 sm:table-cell sm:pl-0">
                                Subtotal
                            </th>
                            <th scope="row"
                                class="pl-4 pr-3 pt-6 text-left text-sm font-normal text-zinc-500 sm:hidden">
                                Subtotal
                            </th>
                            <td class="pl-3 pr-4 pt-6 text-right text-sm text-zinc-500 sm:pr-0">
                                {{ number_format($record->orderDetails->sum('subtotal')) }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="2"
                                class="hidden pl-4 pr-3 pt-4 text-right text-sm font-normal dark:text-zinc-400 text-zinc-500 sm:table-cell sm:pl-0">
                                Discount
                            </th>
                            <th scope="row"
                                class="pl-4 pr-3 pt-4 text-left text-sm font-normal dark:text-zinc-400 text-zinc-500 sm:hidden">
                                Rp
                            </th>
                            <td colspan="2" class="pl-3 pr-4 pt-4 text-right text-sm text-zinc-500 sm:pr-0">
                                <input
                                    class="w-full text-sm h-8 dark:bg-zinc-800 dark:text-white rounded-md border shadow-sm border-zinc-200 dark:border-zinc-700"
                                    type="number"
                                    wire:model.lazy="discount"
                                    min="0"
                                    placeholder="Discount"
                                />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="2"
                                class="hidden pl-4 pr-3 pt-4 text-right text-sm font-semibold dark:text-white text-zinc-900 sm:table-cell sm:pl-0">
                                Total
                            </th>
                            <th scope="row"
                                class="pl-4 pr-3 pt-4 text-left text-sm font-semibold dark:text-white text-zinc-900 sm:hidden">
                                Total
                            </th>
                            <td class="pl-3 pr-4 pt-4 text-right text-sm font-semibold dark:text-white text-zinc-900 sm:pr-0">
                                {{ number_format($record->orderDetails->sum('subtotal') - $discount) }}
                            </td>
                        </tr>
                        </tfoot>
                    </x-table>

                    <div class="flex justify-end mt-10">

                        <x-filament::button type="button"
                                            color="gray"
                                            wire:click="saveAsDraft">
                            Save as Draft
                        </x-filament::button>

                        <x-filament::button type="submit" class="ml-2">
                            Make Transaction
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </x-filament::section>
    </x-filament::grid>
</x-filament-panels::page>
