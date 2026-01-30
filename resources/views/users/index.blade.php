<x-app-layout>
    <x-slot name="title">Users</x-slot>

    <div class="space-y-6">
        <!-- Page header -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-semibold leading-6 text-gray-900">Users</h3>
                <p class="mt-2 text-sm text-gray-700">Manage system users and their access</p>
            </div>
            @can('users.create')
            <div class="mt-4 sm:ml-16 sm:mt-0">
                <a href="{{ route('users.create') }}" 
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Add User
                </a>
            </div>
            @endcan
        </div>

        <!-- Search and filters -->
        <div class="bg-white p-4 rounded-lg shadow">
            <form method="GET" action="{{ route('users.index') }}" class="sm:flex sm:items-center sm:space-x-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search users..." 
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <div class="mt-3 sm:mt-0">
                    <select name="status" 
                            class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">All Status</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" 
                        class="mt-3 sm:mt-0 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Search
                </button>
            </form>
        </div>

        <!-- Users table -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead>
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Created</th>
                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="ml-2 inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">You</span>
                            @endif
                        </td>
                        <td class="px-3 py-4 text-sm text-gray-500">
                            {{ $user->email }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            @if($user->role === 'superadmin')
                                <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">Super Admin</span>
                            @elseif($user->role === 'admin')
                                <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">Admin</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">User</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            @if($user->active)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Inactive</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <div class="flex justify-end gap-2">
                                @can('users.edit')
                                <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                
                                <form method="POST" action="{{ route('users.toggle-active', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="{{ $user->active ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900' }}">
                                        {{ $user->active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                @endcan
                                
                                @can('users.delete')
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
