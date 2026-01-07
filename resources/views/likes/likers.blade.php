<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('People Who Liked This') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('user-media.public') }}" class="text-blue-600 hover:text-blue-900">← Back to My Public Media</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-2xl font-bold text-gray-900">{{ $userMedia->mediaPool->title }}</h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $userMedia->mediaPool->type_name }}
                    @if($userMedia->mediaPool->release_year)
                        ({{ $userMedia->mediaPool->release_year }})
                    @endif
                </p>
                @if($userMedia->mediaPool->description)
                    <p class="mt-3 text-gray-700">{{ $userMedia->mediaPool->description }}</p>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-lg font-semibold mb-4">Liked by {{ $likers->count() }} {{ Str::plural('person', $likers->count()) }}</h4>

                    @if($likers->isEmpty())
                        <p class="text-gray-500">No one has liked this yet. Share your invitation link to get more viewers!</p>
                    @else
                        <div class="space-y-3">
                            @foreach($likers as $liker)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $liker->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $liker->email }}</p>
                                    </div>
                                    <div class="text-red-500">
                                        ❤️
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
