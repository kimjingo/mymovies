<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Media') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('user-media.store') }}" id="mediaForm">
                        @csrf

                        <input type="hidden" name="media_pool_id" id="media_pool_id">

                        <div id="existingMediaBanner" class="mb-4 p-4 bg-green-50 border border-green-300 rounded-md hidden">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-800 font-semibold">✓ Adding existing media to your collection</p>
                                    <p class="text-xs text-green-600 mt-1">You can only change visibility. To create a new media, click "Create New" button.</p>
                                </div>
                                <button type="button" id="clearSelection" class="text-green-700 hover:text-green-900 font-medium text-sm underline">
                                    Create New Instead
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Title')" />
                            <div class="relative">
                                <x-text-input
                                    id="title"
                                    class="block mt-1 w-full"
                                    type="text"
                                    name="title"
                                    :value="old('title')"
                                    required
                                    autofocus
                                    autocomplete="off"
                                />
                                <div id="searchingIndicator" class="absolute right-3 top-3 text-gray-400 text-sm hidden">
                                    Searching...
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Type to search existing media or enter a new title</p>
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />

                            <div id="suggestions" class="mt-2 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Type')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($typeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="release_year" :value="__('Release Year (Optional)')" />
                            <x-text-input
                                id="release_year"
                                class="block mt-1 w-full"
                                type="number"
                                name="release_year"
                                :value="old('release_year')"
                                min="1800"
                                max="{{ date('Y') + 10 }}"
                            />
                            <x-input-error :messages="$errors->get('release_year')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="visibility" :value="__('Visibility')" />
                            <select id="visibility" name="visibility" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="private" {{ old('visibility', 'private') === 'private' ? 'selected' : '' }}>Private</option>
                                <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>Public</option>
                            </select>
                            <x-input-error :messages="$errors->get('visibility')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('user-media.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Add Media') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let searchTimeout;
        const titleInput = document.getElementById('title');
        const typeInput = document.getElementById('type');
        const suggestionsDiv = document.getElementById('suggestions');
        const mediaPoolIdInput = document.getElementById('media_pool_id');
        const descriptionInput = document.getElementById('description');
        const releaseYearInput = document.getElementById('release_year');
        const searchingIndicator = document.getElementById('searchingIndicator');
        const existingMediaBanner = document.getElementById('existingMediaBanner');
        const clearSelectionBtn = document.getElementById('clearSelection');

        function clearMediaSelection() {
            mediaPoolIdInput.value = '';
            titleInput.value = '';
            titleInput.readOnly = false;
            typeInput.disabled = false;
            typeInput.value = '1'; // Reset to Movie
            releaseYearInput.readOnly = false;
            releaseYearInput.value = '';
            descriptionInput.readOnly = false;
            descriptionInput.value = '';
            existingMediaBanner.classList.add('hidden');
            titleInput.focus();
        }

        clearSelectionBtn.addEventListener('click', clearMediaSelection);

        titleInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                suggestionsDiv.classList.add('hidden');
                searchingIndicator.classList.add('hidden');
                return;
            }

            searchingIndicator.classList.remove('hidden');

            searchTimeout = setTimeout(() => {
                fetch(`/media-pool/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchingIndicator.classList.add('hidden');

                        if (data.length === 0) {
                            suggestionsDiv.classList.add('hidden');
                            return;
                        }

                        suggestionsDiv.innerHTML = data.map(item => `
                            <div class="p-3 hover:bg-gray-100 cursor-pointer suggestion-item border-b last:border-b-0"
                                 data-id="${item.id}"
                                 data-title="${item.title}"
                                 data-type="${item.type}"
                                 data-year="${item.release_year || ''}"
                                 data-description="${item.description || ''}">
                                <div class="font-semibold">${item.title}</div>
                                <div class="text-sm text-gray-600">
                                    ${item.type === 1 ? 'Movie' : 'TV Show'}
                                    ${item.release_year ? `(${item.release_year})` : ''}
                                </div>
                                ${item.description ? `<div class="text-xs text-gray-500 mt-1">${item.description.substring(0, 100)}${item.description.length > 100 ? '...' : ''}</div>` : ''}
                            </div>
                        `).join('');

                        suggestionsDiv.classList.remove('hidden');

                        document.querySelectorAll('.suggestion-item').forEach(item => {
                            item.addEventListener('click', function() {
                                const id = this.dataset.id;
                                const title = this.dataset.title;
                                const type = this.dataset.type;
                                const year = this.dataset.year;
                                const description = this.dataset.description;

                                mediaPoolIdInput.value = id;
                                titleInput.value = title;
                                typeInput.value = type;
                                if (year) releaseYearInput.value = year;
                                if (description) descriptionInput.value = description;

                                titleInput.readOnly = true;
                                typeInput.disabled = true;
                                releaseYearInput.readOnly = true;
                                descriptionInput.readOnly = true;

                                existingMediaBanner.classList.remove('hidden');
                                suggestionsDiv.classList.add('hidden');
                            });
                        });
                    })
                    .catch(() => {
                        searchingIndicator.classList.add('hidden');
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!titleInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
