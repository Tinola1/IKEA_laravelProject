<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categories</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">All Categories</h3>
                    <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Category</a>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">#</th>
                            <th class="py-2">Name</th>
                            <th class="py-2">Description</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr class="border-b">
                            <td class="py-2">{{ $loop->iteration }}</td>
                            <td class="py-2">{{ $category->name }}</td>
                            <td class="py-2">{{ $category->description ?? '-' }}</td>
                            <td class="py-2 flex gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-500">No categories found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $categories->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>