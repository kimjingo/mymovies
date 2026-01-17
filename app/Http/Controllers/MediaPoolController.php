<?php

namespace App\Http\Controllers;

use App\Models\MediaPool;
use Illuminate\Http\Request;

class MediaPoolController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json([]);
        }

        $media = MediaPool::search($query)
            ->limit(10)
            ->get(['id', 'title', 'type', 'release_year', 'description'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'type' => $item->type->value,
                    'release_year' => $item->release_year,
                    'description' => $item->description,
                ];
            });

        return response()->json($media);
    }
}
