<x-app-layout>
    <x-slot name="title">Order #{{ $order->id }}</x-slot>

    <div class="space-y-6">
        <!-- Page header -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Order #{{ $order->id }}</h3>
                <p class="mt-2 text-sm text-gray-700">Order details and items</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 flex gap-3">
                <a href="{{ route('orders.invoice', $order->id) }}" 
                   class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                    Download Invoice
                </a>
                <a href="{{ route('orders.index') }}" 
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Back to Orders
                </a>
            </div>
        </div>

        <!-- Order info -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
            <div class="px-4 py-6 sm:px-6">
                <h3 class="text-base font-semibold leading-7 text-gray-900">Order Information</h3>
                <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Customer Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Order Status</dt>
                        <dd class="mt-1">
                            @if($order->status->value === 'pending')
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Pending</span>
                            @elseif($order->status->value === 'completed')
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Completed</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ ucfirst($order->status->value) }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->payment_method->value) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->payment_status->value) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('F d, Y h:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Order items -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
            <div class="px-4 py-6 sm:px-6">
                <h3 class="text-base font-semibold leading-7 text-gray-900">Order Items</h3>
                <div class="mt-6">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Product</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Quantity</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Unit Price</th>
                                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                    {{ $item->product_name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $item->quantity }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    ${{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-right text-sm font-semibold text-gray-900">
                                    ${{ number_format($item->total_price, 2) }}
                                </td>
                            </tr>
                            @endforeach
                            <tr class="border-t-2 border-gray-900">
                                <td colspan="3" class="py-4 pl-4 pr-3 text-right text-sm font-semibold text-gray-900 sm:pl-0">
                                    Total Amount
                                </td>
                                <td class="whitespace-nowrap py-4 text-right text-lg font-bold text-gray-900">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
