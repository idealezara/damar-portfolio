<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\ProfileSetting;
use App\Support\Media;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile.edit', ['setting' => ProfileSetting::current()]);
    }

    public function update(ProfileRequest $request)
    {
        $setting = ProfileSetting::current();

        $data = $request->safe()->except('profile_image');
        $data['profile_image'] = Media::replace($request->file('profile_image'), 'profile', $setting->profile_image);

        $setting->update($data);

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated. Changes are live on the public site.');
    }
}
