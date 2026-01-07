<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Auth::user()->invitations()->latest()->get();
        return view('invitations.index', compact('invitations'));
    }

    public function create()
    {
        return view('invitations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:users,email', 'unique:invitations,email'],
        ]);

        $invitation = Invitation::create([
            'email' => $request->email,
            'token' => Str::random(32),
            'invited_by' => Auth::id(),
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        return redirect()->route('invitations.index')
            ->with('success', "Invitation sent to {$invitation->email}. Share this link: " . route('register', ['invitation' => $invitation->token]));
    }
}
