<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $invitationToken = $request->query('invitation');

        if (!$invitationToken) {
            abort(403, 'Registration is invite-only. Please use an invitation link.');
        }

        $invitation = Invitation::where('token', $invitationToken)->first();

        if (!$invitation || $invitation->isUsed() || $invitation->isExpired()) {
            abort(403, 'Invalid or expired invitation.');
        }

        return view('auth.register', ['invitation' => $invitation]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invitation_token' => ['required', 'string'],
        ]);

        $invitation = Invitation::where('token', $request->invitation_token)->first();

        if (!$invitation || $invitation->isUsed() || $invitation->isExpired()) {
            return back()->withErrors(['invitation_token' => 'Invalid or expired invitation.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $invitation->update(['used_at' => Carbon::now()]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
