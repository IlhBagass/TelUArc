<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Get comments for a specific artwork
     */
    public function getByArtwork(int $artworkId): JsonResponse
    {
        $comments = Comment::with('user')
            ->where('artwork_id', $artworkId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->toISOString(),
                    'user_name' => $comment->user->name,
                    'user_avatar' => $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : null,
                    'user_id' => $comment->user_id, // Add user_id for ownership check
                ];
            });
        
        return response()->json($comments);
    }

    /**
     * Store a new comment
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'artwork_id' => 'required|exists:artworks,id',
            'content' => 'required|string|max:500',
        ]);

        $comment = Comment::create([
            'artwork_id' => $validated['artwork_id'],
            'user_id' => Auth::id(), 
            'content' => $validated['content'],
        ]);

        // Return the newly created comment with user info
        // Return the newly created comment with user info
        return response()->json([
            'id' => $comment->id,
            'content' => $comment->content,
            'created_at' => $comment->created_at->toISOString(),
            'user_id' => Auth::id(),
            'user' => [
                'name' => Auth::user()->name,
                'avatar' => Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : null,
            ]
        ]);
    }

    /**
     * Delete a comment
     */
    public function destroy(int $id): JsonResponse 
    {
        $comment = Comment::findOrFail($id);
        
        // Check if the user is authorized to delete this comment
        if (Auth::id() !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $comment->delete();

        return response()->json(['success' => true]);
    }
}