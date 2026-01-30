<x-app-layout>
    <x-slot name="title">Products</x-slot>

    <div class="space-y-6">
        <!-- Page header -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Products</h3>
                <p class="mt-2 text-sm text-gray-700">Manage your inventory products</p>
            </div>
            @can('products.create')
            <div class="mt-4 sm:ml-16 sm:mt-0">
                <a href="{{ route('products.create') }}" 
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Add Product
                </a>
            </div>
            @endcan
        </div>

        <!-- Inventory Status Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1">
            <nav class="flex space-x-1" aria-label="Tabs">
                <a href="{{ route('products.index') }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ !request('inventory') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    All Products
                </a>
                <a href="{{ route('products.index', ['inventory' => 'active']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('inventory') === 'active' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Active
                </a>
                <a href="{{ route('products.index', ['inventory' => 'low']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('inventory') === 'low' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Low Stock
                </a>
                <a href="{{ route('products.index', ['inventory' => 'out']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('inventory') === 'out' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Out of Stock
                </a>
            </nav>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <form method="GET" action="{{ route('products.index') }}" class="flex items-center space-x-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search products by name or SKU..." 
                           class="block w-full rounded-lg border-0 pl-10 pr-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all">
                </div>
                <button type="submit" 
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-md hover:bg-indigo-500 transition-all">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>
            </form>
        </div>

        <!-- Products grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
                @if($product->image_url)
                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-48 w-full object-cover object-center">
                </div>
                @else
                <div class="h-48 w-full bg-gray-100 flex items-center justify-center">
                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                @endif
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900">{{ $product->name }}</h3>
                    <p class="mt-1 text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                    <p class="mt-2 text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</p>
                    <p class="mt-1 text-sm text-gray-600">Stock: {{ $product->quantity }}</p>
                    
                    <div class="mt-4 flex gap-2">
                        @can('products.edit')
                        <a href="{{ route('products.edit', $product) }}" 
                           class="flex-1 text-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            Edit
                        </a>
                        @endcan
                        @can('products.delete')
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="flex-1" 
                              onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-md bg-red-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-sm text-gray-500">No products found.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
