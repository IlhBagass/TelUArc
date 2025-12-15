<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show(int $id) {
        $user = User::with('artworks', 'bookmarks', 'likes')->findOrFail($id);
        $artworks = $user->artworks;

        return view('profile', compact('user', 'artworks'));
    }

    public function update(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk update profil.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'bio' => $validated['bio'] ?? null,
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && $user->avatar !== 'default.jpg') {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
        }

        return redirect()->route('users.show', $user->id)->with('success', 'Profil berhasil diperbarui!');
    }

}