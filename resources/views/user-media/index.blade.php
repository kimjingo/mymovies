<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Media') }}
            </h2>
            <a href="{{ route('user-media.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Media
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-6 flex gap-4">
                <a href="{{ route('user-media.liked') }}" class="text-blue-600 hover:text-blue-900">View Liked Media</a>
                <a href="{{ route('user-media.public') }}" class="text-blue-600 hover:text-blue-900">View My Public Media</a>
                <a href="{{ route('invitations.index') }}" class="text-blue-600 hover:text-blue-900">Manage Invitations</a>
            </div>

            @if($myMedia->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">No media added yet. Add your first movie or TV show!</p>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visibility</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($myMedia as $media)
                                        <tr>
                                            <td class="px-6 py-4">{{ $media->mediaPool->title }}</td>
                                            <td class="px-6 py-4">{{ $media->mediaPool->type_name }}</td>
                                            <td class="px-6 py-4">{{ $media->mediaPool->release_year ?? '-' }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $media->visibility === 'public' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($media->visibility) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('user-media.edit', $media) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                @if($media->visibility === 'public')
                                                    <a href="{{ route('likes.likers', $media) }}" class="text-blue-600 hover:text-blue-900 mr-3">View Likes</a>
                                                @endif
                                                <form action="{{ route('user-media.destroy', $media) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Import from CSV</h3>
                <form action="{{ route('user-media.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">CSV File</label>
                            <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">
                                Format: title,type,description,release_year,visibility
                                <a href="{{ asset('sample.csv') }}" download class="text-blue-600 hover:text-blue-900 ml-2">Download sample CSV</a>
                            </p>
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
