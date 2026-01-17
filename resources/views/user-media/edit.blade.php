<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Media') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(!$canEditMediaPool)
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold">{{ $userMedia->mediaPool->title }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ $userMedia->mediaPool->type_name }}
                                @if($userMedia->mediaPool->release_year)
                                    ({{ $userMedia->mediaPool->release_year }})
                                @endif
                            </p>
                            @if($userMedia->mediaPool->description)
                                <p class="mt-2 text-gray-700">{{ $userMedia->mediaPool->description }}</p>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user-media.update', $userMedia) }}">
                        @csrf
                        @method('PUT')

                        @if($canEditMediaPool)
                            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                                <p class="text-sm text-blue-800">You can edit this media's details because you created it, it's not used by others, and has no likes.</p>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="title" :value="__('Title')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $userMedia->mediaPool->title)" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="type" :value="__('Type')" />
                                <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($typeOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('type', $userMedia->mediaPool->type->value) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $userMedia->mediaPool->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="release_year" :value="__('Release Year')" />
                                <x-text-input id="release_year" class="block mt-1 w-full" type="number" name="release_year" :value="old('release_year', $userMedia->mediaPool->release_year)" min="1800" :max="date('Y') + 10" />
                                <x-input-error :messages="$errors->get('release_year')" class="mt-2" />
                            </div>
                        @endif

                        <div class="mb-4">
                            <x-input-label for="visibility" :value="__('Visibility')" />
                            <select id="visibility" name="visibility" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="private" {{ $userMedia->visibility === 'private' ? 'selected' : '' }}>Private - Only visible to you</option>
                                <option value="public" {{ $userMedia->visibility === 'public' ? 'selected' : '' }}>Public - Visible to everyone</option>
                            </select>
                            <x-input-error :messages="$errors->get('visibility')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('user-media.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Update Media') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
