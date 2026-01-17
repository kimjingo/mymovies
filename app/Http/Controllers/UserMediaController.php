<?php

namespace App\Http\Controllers;

use App\Models\UserMedia;
use App\Models\MediaPool;
use App\Enums\MediaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserMediaController extends Controller
{
    public function index()
    {
        $myMedia = Auth::user()->userMedia()
            ->with('mediaPool')
            ->latest()
            ->get();

        return view('user-media.index', compact('myMedia'));
    }

    public function create()
    {
        $typeOptions = MediaType::options();
        return view('user-media.create', compact('typeOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:1,2',
            'description' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1800|max:' . (date('Y') + 10),
            'visibility' => 'required|in:private,public',
            'media_pool_id' => 'nullable|exists:media_pool,id',
        ]);

        DB::transaction(function () use ($request) {
            if ($request->media_pool_id) {
                $mediaPool = MediaPool::find($request->media_pool_id);
            } else {
                $mediaPool = MediaPool::create([
                    'title' => $request->title,
                    'type' => MediaType::from((int)$request->type),
                    'description' => $request->description,
                    'release_year' => $request->release_year,
                    'created_by' => Auth::id(),
                ]);
            }

            UserMedia::create([
                'user_id' => Auth::id(),
                'media_pool_id' => $mediaPool->id,
                'visibility' => $request->visibility,
            ]);
        });

        return redirect()->route('user-media.index')
            ->with('success', 'Media added successfully!');
    }

    public function edit(UserMedia $user_medium)
    {
        if ($user_medium->user_id !== Auth::id()) {
            abort(403);
        }

        $canEditMediaPool = $user_medium->mediaPool->canBeEditedBy(Auth::id());
        $typeOptions = MediaType::options();

        return view('user-media.edit', [
            'userMedia' => $user_medium,
            'canEditMediaPool' => $canEditMediaPool,
            'typeOptions' => $typeOptions,
        ]);
    }

    public function update(Request $request, UserMedia $user_medium)
    {
        if ($user_medium->user_id !== Auth::id()) {
            abort(403);
        }

        $validationRules = [
            'visibility' => 'required|in:private,public',
        ];

        $canEditMediaPool = $user_medium->mediaPool->canBeEditedBy(Auth::id());

        // If user can edit media pool, add validation rules for media pool fields
        if ($canEditMediaPool) {
            $validationRules['title'] = 'required|string|max:255';
            $validationRules['type'] = 'required|in:1,2';
            $validationRules['description'] = 'nullable|string';
            $validationRules['release_year'] = 'nullable|integer|min:1800|max:' . (date('Y') + 10);
        }

        $request->validate($validationRules);

        DB::transaction(function () use ($request, $user_medium, $canEditMediaPool) {
            // Update visibility
            $user_medium->update([
                'visibility' => $request->visibility,
            ]);

            // Update media pool if allowed
            if ($canEditMediaPool) {
                $updateData = [
                    'title' => $request->title,
                    'type' => MediaType::from((int)$request->type),
                    'description' => $request->description,
                    'release_year' => $request->release_year,
                ];

                // If created_by is NULL, set it to the current user (claiming ownership)
                if ($user_medium->mediaPool->created_by === null) {
                    $updateData['created_by'] = Auth::id();
                }

                $user_medium->mediaPool->update($updateData);
            }
        });

        return redirect()->route('user-media.index')
            ->with('success', 'Media updated successfully!');
    }

    public function destroy(UserMedia $user_medium)
    {
        if ($user_medium->user_id !== Auth::id()) {
            abort(403);
        }

        $user_medium->delete();

        return redirect()->route('user-media.index')
            ->with('success', 'Media removed successfully!');
    }

    public function liked()
    {
        $likedMedia = Auth::user()->likedMedia()
            ->with('mediaPool')
            ->latest('likes.created_at')
            ->get();

        return view('user-media.liked', compact('likedMedia'));
    }

    public function myPublic()
    {
        $publicMedia = Auth::user()->userMedia()
            ->where('visibility', 'public')
            ->with('mediaPool', 'likes')
            ->latest()
            ->get();

        return view('user-media.public', compact('publicMedia'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $headers = array_shift($csvData);

        $errors = [];
        $successCount = 0;

        foreach ($csvData as $index => $row) {
            if (count($row) < 2) {
                continue;
            }

            try {
                $title = trim($row[0] ?? '');
                $typeInput = strtolower(trim($row[1] ?? 'movie'));
                $description = trim($row[2] ?? '');
                $releaseYear = trim($row[3] ?? '');
                $visibility = strtolower(trim($row[4] ?? 'private'));

                if (empty($title)) {
                    $errors[] = "Row " . ($index + 2) . ": Title is required";
                    continue;
                }

                // Convert type string to enum
                $type = MediaType::fromString($typeInput);

                if (!in_array($visibility, ['private', 'public'])) {
                    $visibility = 'private';
                }

                DB::transaction(function () use ($title, $type, $description, $releaseYear, $visibility) {
                    $mediaPool = MediaPool::firstOrCreate(
                        ['title' => $title, 'type' => $type->value],
                        [
                            'description' => $description,
                            'release_year' => $releaseYear ? (int)$releaseYear : null,
                            'created_by' => Auth::id(),
                        ]
                    );

                    UserMedia::firstOrCreate(
                        ['user_id' => Auth::id(), 'media_pool_id' => $mediaPool->id],
                        ['visibility' => $visibility]
                    );
                });

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        $message = "$successCount items imported successfully";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', array_slice($errors, 0, 5));
        }

        return redirect()->route('user-media.index')->with('success', $message);
    }
}
