<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Ambil semua artwork_id yang sudah dibookmark user
     */
    public function index(): JsonResponse
    {
        $bookmarks = Bookmark::where('user_id', Auth::id())
            ->pluck('artwork_id')
            ->toArray();

        return response()->json($bookmarks);
    }

    /**
     * Simpan bookmark baru
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'artwork_id' => 'required|exists:artworks,id',
        ]);

        $userId = Auth::id() ?? 1; // fallback ke 1 kalau belum login

        // Cek apakah sudah ada
        $existing = Bookmark::where('user_id', $userId)
            ->where('artwork_id', $request->artwork_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Sudah dibookmark'
            ], 409);
        }

        $bookmark = Bookmark::create([
            'user_id' => $userId,
            'artwork_id' => $request->artwork_id,
        ]);

        return response()->json([
            'message' => 'Bookmark berhasil disimpan',
            'data' => $bookmark
        ], 201);
    }

    /**
     * Hapus bookmark tertentu
     */
    public function destroy($artworkId): JsonResponse
    {
        $userId = Auth::id() ?? 1;

        $bookmark = Bookmark::where('user_id', $userId)
            ->where('artwork_id', $artworkId)
            ->first();

        if (!$bookmark) {
            return response()->json([
                'message' => 'Bookmark tidak ditemukan'
            ], 404);
        }

        $bookmark->delete();

        return response()->json([
            'message' => 'Bookmark berhasil dihapus',
            'artwork_id' => $artworkId
        ]);
    }

    /**
     * Hapus semua bookmark milik user
     */
    public function clearAll(): JsonResponse
    {
        $userId = Auth::id() ?? 1;

        Bookmark::where('user_id', $userId)->delete();

        return response()->json(['message' => 'Semua bookmark dihapus']);
    }
}
