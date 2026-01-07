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

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Title')" />
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

        titleInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                suggestionsDiv.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/media-pool/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            suggestionsDiv.classList.add('hidden');
                            mediaPoolIdInput.value = '';
                            return;
                        }

                        suggestionsDiv.innerHTML = data.map(item => `
                            <div class="p-3 hover:bg-gray-100 cursor-pointer suggestion-item border-b last:border-b-0"
                                 data-id="${item.id}"
                                 data-title="${item.title}"
                                 data-type="${item.type}"
                                 data-year="${item.release_year || ''}">
                                <div class="font-semibold">${item.title}</div>
                                <div class="text-sm text-gray-600">${item.type.replace('_', ' ')} ${item.release_year ? `(${item.release_year})` : ''}</div>
                            </div>
                        `).join('');

                        suggestionsDiv.classList.remove('hidden');

                        document.querySelectorAll('.suggestion-item').forEach(item => {
                            item.addEventListener('click', function() {
                                const id = this.dataset.id;
                                const title = this.dataset.title;
                                const type = this.dataset.type;
                                const year = this.dataset.year;

                                mediaPoolIdInput.value = id;
                                titleInput.value = title;
                                typeInput.value = type;
                                if (year) releaseYearInput.value = year;

                                titleInput.readOnly = true;
                                typeInput.disabled = true;
                                releaseYearInput.readOnly = true;
                                descriptionInput.readOnly = true;

                                suggestionsDiv.classList.add('hidden');
                            });
                        });
                    });
            }, 300);
        });

        titleInput.addEventListener('change', function() {
            if (!mediaPoolIdInput.value) {
                titleInput.readOnly = false;
                typeInput.disabled = false;
                releaseYearInput.readOnly = false;
                descriptionInput.readOnly = false;
            }
        });

        document.addEventListener('click', function(e) {
            if (!titleInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
