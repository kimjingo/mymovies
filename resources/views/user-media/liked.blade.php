<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Media I Liked') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('user-media.index') }}" class="text-blue-600 hover:text-blue-900">← Back to My Media</a>
            </div>

            @if($likedMedia->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">You haven't liked any media yet. Explore the home page to find interesting movies and TV shows!</p>
                    <a href="{{ route('home') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-900">Browse Public Media</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($likedMedia as $userMedia)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 max-w-4xl mx-auto">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $userMedia->mediaPool->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $userMedia->mediaPool->type_name }}
                                @if($userMedia->mediaPool->release_year)
                                    ({{ $userMedia->mediaPool->release_year }})
                                @endif
                            </p>
                            @if($userMedia->mediaPool->description)
                                <p class="mt-3 text-gray-700">{{ Str::limit($userMedia->mediaPool->description, 150) }}</p>
                            @endif
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm text-gray-500">Shared by {{ $userMedia->user->name }}</span>
                                <button onclick="toggleLike({{ $userMedia->id }})" id="like-btn-{{ $userMedia->id }}" class="flex items-center gap-1 text-red-500 hover:text-red-700">
                                    <span id="like-icon-{{ $userMedia->id }}">❤️</span>
                                    <span id="likes-count-{{ $userMedia->id }}">{{ $userMedia->likes->count() }}</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleLike(userMediaId) {
            fetch(`/likes/${userMediaId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.liked) {
                    document.getElementById(`like-btn-${userMediaId}`).closest('.bg-white').remove();
                } else {
                    document.getElementById(`like-icon-${userMediaId}`).textContent = '❤️';
                    document.getElementById(`likes-count-${userMediaId}`).textContent = data.likes_count;
                }
            });
        }
    </script>
</x-app-layout>
