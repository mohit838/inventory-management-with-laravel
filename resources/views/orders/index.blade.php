<x-app-layout>
    <x-slot name="title">Orders</x-slot>

    <div class="space-y-6">
        <!-- Page header -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Orders</h3>
                <p class="mt-2 text-sm text-gray-700">Manage customer orders</p>
            </div>
            @can('orders.create')
            <div class="mt-4 sm:ml-16 sm:mt-0">
                <a href="{{ route('orders.create') }}" 
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Create Order
                </a>
            </div>
            @endcan
        </div>

        <!-- Status Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1">
            <nav class="flex space-x-1" aria-label="Tabs">
                <a href="{{ route('orders.index') }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ !request('status') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    All Orders
                </a>
                <a href="{{ route('orders.index', ['status' => 'pending']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('status') === 'pending' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Pending
                </a>
                <a href="{{ route('orders.index', ['status' => 'processing']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('status') === 'processing' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Processing
                </a>
                <a href="{{ route('orders.index', ['status' => 'completed']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('status') === 'completed' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Completed
                </a>
                <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('status') === 'cancelled' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Cancelled
                </a>
            </nav>
        </div>

        <!-- Orders table -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead>
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Order ID</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Customer</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            #{{ $order->id }}
                        </td>
                        <td class="px-3 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ $order->customer_name }}</div>
                            <div class="text-gray-500">{{ $order->customer_email }}</div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">
                            ${{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            @if($order->status->value === 'pending')
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Pending</span>
                            @elseif($order->status->value === 'completed')
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Completed</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ ucfirst($order->status->value) }}</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('orders.invoice', $order->id) }}" class="text-green-600 hover:text-green-900">Invoice</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">
                            No orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} results
            </div>
            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
