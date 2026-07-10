<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Seed default users if DB is empty for demo convenience.
     */
    protected function ensureDefaultUsersExist()
    {
        if (User::count() === 0) {
            // Ensure at least one tenant exists
            $tenant = Tenant::first();
            if (!$tenant) {
                $tenant = Tenant::create([
                    'name' => 'Onboardify Default',
                    'primary_color' => '#4f46e5',
                    'secondary_color' => '#0f172a',
                    'logo_url' => '/logo.png'
                ]);
            }

            // Create admin
            User::create([
                'name' => 'Jane Doe',
                'email' => 'admin@onboardify.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'tenant_id' => $tenant->id,
                'level' => 1,
                'points' => 0
            ]);

            // Create employee
            User::create([
                'name' => 'Alex Mercer',
                'email' => 'employee@onboardify.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'tenant_id' => $tenant->id,
                'level' => 2,
                'points' => 120
            ]);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $this->ensureDefaultUsersExist();

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'level' => $user->level,
                'points' => $user->points,
                'tenant_id' => $user->tenant_id
            ]
        ]);
    }

    public function acceptInvite(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
            'token' => 'nullable|string'
        ]);

        // Dynamically locate the tenant to attach the new user to
        $tenant = Tenant::first();
        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'Onboardify Default',
                'primary_color' => '#4f46e5',
                'secondary_color' => '#0f172a',
                'logo_url' => '/logo.png'
            ]);
        }

        // For demo convenience, let's create a new employee with this password
        $email = 'newhire_' . rand(100, 999) . '@company.com';
        
        $user = User::create([
            'name' => 'New Hire',
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
            'tenant_id' => $tenant->id,
            'level' => 1,
            'points' => 0
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Account activated successfully',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'level' => $user->level,
                'points' => $user->points,
                'tenant_id' => $user->tenant_id
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
