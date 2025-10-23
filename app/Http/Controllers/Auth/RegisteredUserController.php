<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Display the student registration view.
     */
    public function createStudent(): View
    {
        return view('auth.register-student');
    }

    /**
     * Display the client registration view.
     */
    public function createClient(): View
    {
        return view('auth.register-client');
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
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    /**
     * Handle student registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeStudent(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'university' => ['required', 'string', 'max:255'],
            'field_of_study' => ['required', 'string', 'max:255'],
            'year_of_study' => ['required', 'string', 'max:50'],
            'terms' => ['accepted'],
        ]);

        $user = null;

        DB::transaction(function () use ($request, &$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            $user->assignRole('student');

            StudentProfile::create([
                'user_id' => $user->id,
                'university' => $request->university,
                'field_of_study' => $request->field_of_study,
                'year_of_study' => $request->year_of_study,
                'bio' => null,
                'skills' => [],
                'languages' => [],
                'available_for_work' => true,
            ]);

            event(new Registered($user));
        });

        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', 'Please verify your email address to continue.');
    }

    /**
     * Handle client registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeClient(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'organization' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, &$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'client',
            ]);

            $user->assignRole('client');

            ClientProfile::create([
                'user_id' => $user->id,
                'organization' => $request->organization,
            ]);

            event(new Registered($user));
        });

        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', 'Please verify your email address to continue.');
    }
}
