<?php

namespace App\Http\Controllers;

use App\Models\TenantRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    /**
     * Show the premium landing page.
     */
    public function index()
    {
        return view('landing');
    }

    /**
     * Store a new onboarding request.
     * Rate-limited via route middleware.
     */
    public function submitRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:tenant_requests,email|unique:users,email',
            'organization_name' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
        ], [
            'email.unique' => 'This email is already registered or has a pending request.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        TenantRequest::create([
            'email' => $request->email,
            'organization_name' => $request->organization_name,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your request has been received. Our team will review it and send an invitation shortly.'
        ]);
    }
}
