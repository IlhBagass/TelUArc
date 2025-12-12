<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'artwork_id' => 'required|exists:artworks,id',
        ]);

        $like = Like::firstOrCreate([
            'user_id' => Auth::id(),
            'artwork_id' => $validated['artwork_id'],
        ]);

        return response()->json($like, 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $like = Like::where('user_id', Auth::id())
            ->where('artwork_id', $id)
            ->firstOrFail();
            
        $like->delete();
        
        return response()->json(['message' => 'Like removed successfully']);
    }
}