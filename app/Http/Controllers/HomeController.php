<?php

namespace App\Http\Controllers;

use App\Models\UserMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = UserMedia::public()
            ->with(['mediaPool', 'user', 'likes'])
            ->withCount('likes')
            ->latest();

        $search = $request->get('search');
        if ($search) {
            $query->whereHas('mediaPool', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }

        $publicMedia = $query->paginate(20);

        if (Auth::check()) {
            $userLikes = Auth::user()->likedMedia->pluck('id')->toArray();
        } else {
            $userLikes = [];
        }

        return view('home', compact('publicMedia', 'userLikes'));
    }
}
