<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function resolve(Request $request)
    {
        // Try to fetch by tenant_id query param or fallback to first tenant
        $tenantId = $request->query('tenant_id');
        $tenant = null;

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
        }

        if (!$tenant) {
            $tenant = Tenant::first();
        }

        // If no tenant exists in DB, seed a default one dynamically
        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'Onboardify Default',
                'primary_color' => '#4f46e5',
                'secondary_color' => '#0f172a',
                'logo_url' => '/logo.png'
            ]);
        }

        return response()->json([
            'id' => $tenant->id,
            'name' => $tenant->name,
            'primaryColor' => $tenant->primary_color,
            'secondaryColor' => $tenant->secondary_color,
            'logoUrl' => $tenant->logo_url
        ]);
    }

    public function updateBranding(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'primaryColor' => 'required|string',
            'secondaryColor' => 'required|string',
            'logoUrl' => 'required|string'
        ]);

        $tenant = Tenant::findOrFail($request->tenant_id);
        $tenant->update([
            'primary_color' => $request->primaryColor,
            'secondary_color' => $request->secondaryColor,
            'logo_url' => $request->logoUrl
        ]);

        return response()->json([
            'message' => 'Branding updated successfully',
            'tenant' => $tenant
        ]);
    }
}
