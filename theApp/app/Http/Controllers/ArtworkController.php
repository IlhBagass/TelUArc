<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtworkController extends Controller
{
    // Tampilkan semua karya
    public function index()
    {
        $artworks = Artwork::with('user')->latest()->get();
        return view('landingPage', compact('artworks'));
    }

    // Simpan karya
    public function store(Request $request)
    {
        // kalau belum login, balikin error/peringatan
        if (!Auth::check()) {
            return redirect()->route('auth.form')->with('error', 'Silakan login dulu untuk mengupload karya.');
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'main_image'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'additional_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // simpan main image
        $mainImagePath = $request->file('main_image')->store('artworks', 'public');

        // simpan additional images kalau ada
        $additionalPaths = [];
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $file) {
                $additionalPaths[] = $file->store('artworks', 'public');
            }
        }

        Artwork::create([
            'user_id'           => Auth::id(), // pasti ada karena dicek di atas
            'title'             => $request->title,
            'description'       => $request->description,
            'main_image'        => $mainImagePath,
            'additional_images' => $additionalPaths,
        ]);

        return redirect()->route('artworks.index')->with('success', 'Karya berhasil diupload!');
    }


    public function view($id)
    {
        $artwork = Artwork::with('user','tags')->findOrFail($id);

        // decode additional_images (bisa array atau json string)
        $additional = is_array($artwork->additional_images)
            ? $artwork->additional_images
            : json_decode($artwork->additional_images ?? '[]', true);

        // increment views
        $artwork->increment('views');

        $payload = [
            'id' => $artwork->id,
            'title' => $artwork->title,
            'description' => $artwork->description,
            'main_image' => $artwork->main_image ? asset('storage/' . $artwork->main_image) : null,
            'additional_images' => array_map(fn($p) => asset('storage/' . $p), $additional),
            'created_at' => $artwork->created_at->diffForHumans(),
            'views' => $artwork->views,
            'user' => [
                'id' => $artwork->user->id ?? null,
                'name' => $artwork->user->name ?? 'Anonim',
                'avatar' => $artwork->user->avatar ? asset('storage/' . $artwork->user->avatar) : 'https://i.pravatar.cc/100'
            ],
            'tags' => $artwork->tags->pluck('name')->toArray(),
            'likes' => 0,
            'comments' => 0,
        ];

        return response()->json($payload);
    }
}