<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

        // Send invitation email
        $invitationUrl = route('register', ['invitation' => $invitation->token]);
        Mail::raw(
            "You have been invited to join MyMovies!\n\nClick the link below to register:\n{$invitationUrl}\n\nThis invitation expires in 7 days.",
            function ($message) use ($invitation) {
                $message->to($invitation->email)
                    ->subject('You\'re invited to MyMovies!');
            }
        );

        return redirect()->route('invitations.index')
            ->with('success', "Invitation sent to {$invitation->email}.");
    }
}
