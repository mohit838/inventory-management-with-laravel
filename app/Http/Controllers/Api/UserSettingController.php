<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserSettingRepository;
use App\Models\UserSetting;

class UserSettingController extends Controller
{
    public function __construct(protected UserSettingRepository $repo)
    {}

    public function index(Request $request)
    {
        $userId = $request->user()->id; // Assuming JWT Auth
        $settings = $this->repo->getAllForUser($userId);
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'nullable|string',
        ]);

        $userId = $request->user()->id;
        $setting = $this->repo->set($userId, $data['key'], $data['value']);
        
        return response()->json($setting);
    }
}
