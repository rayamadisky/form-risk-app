<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private function generateUsername(string $email): string
    {
        $base = Str::of($email)->before('@')->lower()->slug('_')->toString();
        $base = $base !== '' ? $base : 'user';

        $candidate = $base;
        $i = 1;

        while (User::where('username', $candidate)->exists()) {
            $candidate = $base . '_' . $i;
            $i++;
        }

        return $candidate;
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $branchId = $request->branch_id
            ?? Branch::query()->value('id')
            ?? Branch::create(['nama_cabang' => 'Pusat'])->id;

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username ?: $this->generateUsername((string) $request->email),
            'email' => $request->email,
            'branch_id' => $branchId,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
