<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CommentController extends Controller
{
    public function index(int $artwork): RedirectResponse | JsonResponse
    {
        $comments = Comment::with('user')
            ->where('artwork_id', $artwork)
            ->orderBy('created_at', 'desc')
            ->get();

        return redirect()->back()->with('comments', $comments);
    }

    public function store(Request $request): RedirectResponse | JsonResponse
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

        return redirect()->back()->with('success', 'Comment added successfully');
    }

    public function destroy(int $id): RedirectResponse 
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully');
    }
}
