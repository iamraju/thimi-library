<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('users/Index', [
            'users' => User::query()
                ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%')->orWhere('email', 'like', '%'.$request->string('search').'%')))
                ->orderBy('name')->paginate(15)->withQueryString(),
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();
        User::create($data);

        return to_route('users.index')->with('success', __('User created.'));
    }

    public function edit(User $user): Response
    {
        return Inertia::render('users/Form', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }
        unset($data['password']);

        if ($user->isSuperadmin() && $data['role'] !== 'superadmin' && User::where('role', 'superadmin')->count() === 1) {
            return back()->withErrors(['role' => __('At least one superadmin must remain.')]);
        }

        $user->update($data);

        return to_route('users.index')->with('success', __('User updated.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => __('You cannot delete your own account.')]);
        }

        if ($user->isSuperadmin() && User::where('role', 'superadmin')->count() === 1) {
            return back()->withErrors(['user' => __('At least one superadmin must remain.')]);
        }

        $user->delete();

        return to_route('users.index')->with('success', __('User deleted.'));
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'role' => ['required', Rule::in(['superadmin', 'librarian', 'reader'])],
            'locale' => ['nullable', Rule::in(array_keys(config('locales.available')))],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
