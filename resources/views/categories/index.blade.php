<x-app-layout>
    <x-slot name="title">Categories</x-slot>

    <div class="space-y-6">
        <!-- Page header -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Categories</h3>
                <p class="mt-2 text-sm text-gray-700">Manage your product categories</p>
            </div>
            @can('categories.create')
            <div class="mt-4 sm:ml-16 sm:mt-0">
                <a href="{{ route('categories.create') }}" 
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Add Category
                </a>
            </div>
            @endcan
        </div>

        <!-- Search and filters -->
        <div class="bg-white p-4 rounded-lg shadow">
            <form method="GET" action="{{ route('categories.index') }}" class="sm:flex sm:items-center sm:space-x-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search categories..." 
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <div class="mt-3 sm:mt-0">
                    <select name="status" 
                            class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>Archived</option>
                        <option value="" {{ $status === '' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <button type="submit" 
                        class="mt-3 sm:mt-0 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Search
                </button>
            </form>
        </div>

        <!-- Categories table -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead>
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Description</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $category)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            {{ $category->name }}
                        </td>
                        <td class="px-3 py-4 text-sm text-gray-500">
                            {{ Str::limit($category->description, 50) }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            @if($category->active)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Archived</span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <div class="flex justify-end gap-2">
                                @can('categories.edit')
                                <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endcan
                                @can('categories.delete')
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500">
                            No categories found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} results
            </div>
            <div>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
