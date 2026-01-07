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
            ->get(['id', 'title', 'type', 'release_year']);

        return response()->json($media);
    }
}
