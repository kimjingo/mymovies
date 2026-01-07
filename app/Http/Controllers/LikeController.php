<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\UserMedia;
use App\Events\MediaLiked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Request $request, UserMedia $userMedia)
    {
        if ($userMedia->user_id === Auth::id()) {
            return response()->json(['error' => 'Cannot like your own media'], 400);
        }

        if (!$userMedia->isPublic()) {
            return response()->json(['error' => 'Cannot like private media'], 403);
        }

        $like = Like::where('user_id', Auth::id())
            ->where('user_media_id', $userMedia->id)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'user_media_id' => $userMedia->id,
            ]);
            $liked = true;
        }

        $likesCount = $userMedia->likes()->count();

        if ($liked) {
            broadcast(new MediaLiked($userMedia, Auth::user(), $likesCount));
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }

    public function likers(UserMedia $userMedia)
    {
        if ($userMedia->user_id !== Auth::id()) {
            abort(403, 'You can only view likers of your own media');
        }

        $likers = $userMedia->likedBy()->get();

        return view('likes.likers', compact('userMedia', 'likers'));
    }
}
