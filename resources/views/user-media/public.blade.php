<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Public Media') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('user-media.index') }}" class="text-blue-600 hover:text-blue-900">← Back to My Media</a>
            </div>

            @if($publicMedia->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">You don't have any public media yet. Change the visibility of your media to public to share with others!</p>
                    <a href="{{ route('user-media.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-900">Go to My Media</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($publicMedia as $media)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 max-w-4xl mx-auto">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $media->mediaPool->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $media->mediaPool->type_name }}
                                @if($media->mediaPool->release_year)
                                    ({{ $media->mediaPool->release_year }})
                                @endif
                            </p>
                            @if($media->mediaPool->description)
                                <p class="mt-3 text-gray-700">{{ Str::limit($media->mediaPool->description, 150) }}</p>
                            @endif
                            <div class="mt-4 flex items-center justify-between">
                                <a href="{{ route('likes.likers', $media) }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-1">
                                    <span>❤️</span>
                                    <span>{{ $media->likes->count() }} likes</span>
                                </a>
                                <div class="flex gap-2">
                                    <a href="{{ route('user-media.edit', $media) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
