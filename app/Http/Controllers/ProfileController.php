<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Upload / Update Profile Photo
     */
    public function uploadPhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = Auth::user();
        $file = $request->file('profile_photo');
        $ext = $file->getClientOriginalExtension();
        $filename = 'profile_' . $user->id_user . '.' . $ext;

        $upload_dir = public_path('uploads/profiles');
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Delete old profile photos
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        foreach ($allowed_ext as $e) {
            $old_file = $upload_dir . DIRECTORY_SEPARATOR . 'profile_' . $user->id_user . '.' . $e;
            if (file_exists($old_file)) {
                @unlink($old_file);
            }
        }

        $file->move($upload_dir, $filename);

        return Redirect::route('profile.edit')->with('success', 'Foto profil berhasil diperbarui.');
    }
}
