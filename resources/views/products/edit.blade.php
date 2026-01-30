<x-app-layout>
    <x-slot name="title">Edit Product</x-slot>

    <div class="space-y-6">
        <!-- Page header -->
        <div>
            <h3 class="text-2xl font-semibold leading-6 text-gray-900">Edit Product</h3>
            <p class="mt-2 text-sm text-gray-700">Update product information</p>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Product Name</label>
                        <div class="mt-2">
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium leading-6 text-gray-900">SKU</label>
                        <div class="mt-2">
                            <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium leading-6 text-gray-900">Category</label>
                        <div class="mt-2">
                            <select name="category_id" id="category_id" required
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                @foreach($categories as $category)
                                    <option value="{{ $category['id'] }}" {{ old('category_id', $product->category_id) == $category['id'] ? 'selected' : '' }}>
                                        {{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="subcategory_id" class="block text-sm font-medium leading-6 text-gray-900">Subcategory (Optional)</label>
                        <div class="mt-2">
                            <select name="subcategory_id" id="subcategory_id"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="">None</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory['id'] }}" {{ old('subcategory_id', $product->subcategory_id) == $subcategory['id'] ? 'selected' : '' }}>
                                        {{ $subcategory['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Price</label>
                        <div class="mt-2">
                            <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" required
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium leading-6 text-gray-900">Quantity</label>
                        <div class="mt-2">
                            <input type="number" name="quantity" id="quantity" min="0" value="{{ old('quantity', $product->quantity) }}" required
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                    <div class="mt-2">
                        <textarea name="description" id="description" rows="3"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium leading-6 text-gray-900">Product Image</label>
                    @if($product->image_url)
                        <div class="mt-2 mb-4">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-32 w-32 object-cover rounded-lg">
                        </div>
                    @endif
                    <div class="mt-2">
                        <input type="file" name="image" id="image" accept="image/*"
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to keep current image</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-x-6">
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
                    <button type="submit" 
                            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
