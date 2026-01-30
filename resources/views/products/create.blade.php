<x-app-layout>
    <x-slot name="title">Create Product</x-slot>

    <div class="space-y-6">
        <!-- Page header -->
        <div>
            <h3 class="text-2xl font-bold leading-6 text-gray-900">Create Product</h3>
            <p class="mt-2 text-sm text-gray-600">Add a new product to your inventory</p>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-lg ring-1 ring-gray-900/5 sm:rounded-2xl overflow-hidden">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="p-8 space-y-8">
                @csrf

                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-bold leading-6 text-gray-900">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">A unique name for your product</p>
                        <div class="mt-2">
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   placeholder="e.g., Premium Wireless Headphones"
                                   class="block w-full rounded-lg border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all @error('name') ring-red-500 @enderror">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-bold leading-6 text-gray-900">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Stock Keeping Unit</p>
                        <div class="mt-2">
                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                   placeholder="e.g., WH-001-BLK"
                                   class="block w-full rounded-lg border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all @error('sku') ring-red-500 @enderror">
                        </div>
                        @error('sku')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-bold leading-6 text-gray-900">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Select the product category</p>
                        <div class="mt-2">
                            <select name="category_id" id="category_id" required
                                    class="block w-full rounded-lg border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category['id'] }}" {{ old('category_id') == $category['id'] ? 'selected' : '' }}>
                                        {{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subcategory_id" class="block text-sm font-bold leading-6 text-gray-900">
                            Subcategory <span class="text-gray-400">(Optional)</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Narrow down the category</p>
                        <div class="mt-2">
                            <select name="subcategory_id" id="subcategory_id"
                                    class="block w-full rounded-lg border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all">
                                <option value="">None</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory['id'] }}" {{ old('subcategory_id') == $subcategory['id'] ? 'selected' : '' }}>
                                        {{ $subcategory['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-bold leading-6 text-gray-900">
                            Price <span class="text-red-500">*</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Selling price per unit (USD)</p>
                        <div class="mt-2 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price') }}" required
                                   placeholder="0.00"
                                   class="block w-full rounded-lg border-0 pl-8 pr-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all @error('price') ring-red-500 @enderror">
                        </div>
                        @error('price')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-bold leading-6 text-gray-900">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Stock quantity</p>
                        <div class="mt-2">
                            <input type="number" name="quantity" id="quantity" min="0" value="{{ old('quantity') }}" required
                                   placeholder="0"
                                   class="block w-full rounded-lg border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all @error('quantity') ring-red-500 @enderror">
                        </div>
                        @error('quantity')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold leading-6 text-gray-900">
                        Description
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Product details and features</p>
                    <div class="mt-2">
                        <textarea name="description" id="description" rows="4"
                                  placeholder="Describe your product features, specifications, and benefits..."
                                  class="block w-full rounded-lg border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="image" class="block text-sm font-bold leading-6 text-gray-900">
                        Product Image
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Upload a product photo (JPG, PNG, max 2MB)</p>
                    <div class="mt-2">
                        <div class="flex items-center justify-center w-full">
                            <label for="image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                </div>
                                <input type="file" name="image" id="image" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('products.index') }}" 
                       class="rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-xl hover:-translate-y-0.5">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
