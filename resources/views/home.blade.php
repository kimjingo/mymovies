<x-guest-layout>
    <div class="min-h-screen bg-gray-100">
        <div class="fixed top-4 right-6 z-50">
            @auth
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Login
                </a>
            @endauth
        </div>

        <div class="py-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Discover Movies & TV Shows</h1>
                <p class="mt-2 text-gray-600">See what people are sharing</p>
            </div>

            <div class="mb-6">
                <form method="GET" action="{{ route('home') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search movies and TV shows..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Search
                    </button>
                </form>
            </div>

            @if($publicMedia->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">No public media yet. Be the first to share!</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($publicMedia as $userMedia)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
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
                                @auth
                                    <button onclick="toggleLike({{ $userMedia->id }})" id="like-btn-{{ $userMedia->id }}" class="flex items-center gap-1 {{ in_array($userMedia->id, $userLikes) ? 'text-red-500' : 'text-gray-500' }} hover:text-red-500">
                                        <span id="like-icon-{{ $userMedia->id }}">{{ in_array($userMedia->id, $userLikes) ? '❤️' : '🤍' }}</span>
                                        <span id="likes-count-{{ $userMedia->id }}">{{ $userMedia->likes_count }}</span>
                                    </button>
                                @else
                                    <span class="text-gray-500">🤍 {{ $userMedia->likes_count }}</span>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $publicMedia->links() }}
                </div>
            @endif
        </div>
    </div>

    @auth
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
                document.getElementById(`like-icon-${userMediaId}`).textContent = data.liked ? '❤️' : '🤍';
                document.getElementById(`likes-count-${userMediaId}`).textContent = data.likes_count;
            });
        }
    </script>
    @endauth
</x-guest-layout>
