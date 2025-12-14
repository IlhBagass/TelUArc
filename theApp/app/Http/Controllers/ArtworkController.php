<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtworkController extends Controller
{
    // Tampilkan semua karya
    public function index(Request $request)
    {
        $query = Artwork::with('user')->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'LIKE', "%{$search}%");
        }

        $artworks = $query->get();
        return view('landingPage', compact('artworks'));
    }

    // Simpan karya
    public function store(Request $request)
    {
        // pastikan user login
        if (!Auth::check()) {
            return redirect()
                ->route('auth.form')
                ->with('error', 'Silakan login dulu untuk mengupload karya.');
        }

        // validasi (FIX: pakai sometimes)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'main_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',

            'additional_images' => 'sometimes|array',
            'additional_images.*' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'main_image.max' => 'Ukuran gambar utama tidak boleh lebih dari 2MB.',
            'main_image.image' => 'File harus berupa gambar.',
            'main_image.mimes' => 'Format gambar harus jpg, jpeg, atau png.',
            'additional_images.*.max' => 'Ukuran gambar tambahan tidak boleh lebih dari 2MB.',
        ]);

        // simpan main image
        $mainImagePath = $request->file('main_image')->store('artworks', 'public');

        // simpan additional images (opsional)
        $additionalPaths = [];
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $file) {
                $additionalPaths[] = $file->store('artworks', 'public');
            }
        }

        // insert ke database
        Artwork::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'main_image' => $mainImagePath,
            'additional_images' => $additionalPaths,
        ]);

        // redirect sukses
        return redirect()
            ->route('artworks.index')
            ->with('success', 'Karya berhasil diupload!');
    }

    public function show($id)
    {
        $artwork = Artwork::with(['user', 'tags', 'comments.user'])
            ->findOrFail($id);

        $artwork->increment('views');

        return view('artwork.show', compact('artwork'));
    }


    public function view($id)
    {
        // Tambahkan 'comments.user' di eager loading
        $artwork = Artwork::with(['user', 'tags', 'comments.user'])->findOrFail($id);

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
            'likes' => $artwork->likes()->count(),
            'comments_count' => $artwork->comments()->count(), // Add comment count
            'comments' => $artwork->comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'avatar' => $comment->user->avatar 
                            ? asset('storage/' . $comment->user->avatar) 
                            : 'https://i.pravatar.cc/100'
                    ]
                ];
            })->toArray(),
        ];

        return response()->json($payload);
    }
}