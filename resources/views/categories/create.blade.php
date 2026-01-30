<x-app-layout>
    <x-slot name="title">Create Category</x-slot>

    <div class="space-y-6">
        <!-- Page header -->
        <div>
            <h3 class="text-2xl font-bold leading-6 text-gray-900">Create Category</h3>
            <p class="mt-2 text-sm text-gray-600">Add a new category to organize your products</p>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-lg ring-1 ring-gray-900/5 sm:rounded-2xl overflow-hidden">
            <form method="POST" action="{{ route('categories.store') }}" class="p-8 space-y-8">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-bold leading-6 text-gray-900">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">A unique name for this category</p>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               placeholder="e.g., Electronics, Clothing, Furniture"
                               class="block w-full rounded-lg border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all @error('name') ring-red-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold leading-6 text-gray-900">
                        Description
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Brief description of this category</p>
                    <div class="mt-2">
                        <textarea name="description" id="description" rows="4"
                                  placeholder="Describe what products belong in this category..."
                                  class="block w-full rounded-lg border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all @error('description') ring-red-500 @enderror">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('categories.index') }}" 
                       class="rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-xl hover:-translate-y-0.5">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
