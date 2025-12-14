<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'artwork_id' => 'required|exists:artworks,id',
        ]);

        Report::create([
            'user_id' => Auth::id(),
            'artwork_id' => $request->artwork_id,
            'reason' => $request->reason ?? 'Reported by user',
            'description' => $request->description ?? 'No description provided',
        ]);

        return response()->json(['message' => 'Laporan berhasil dikirim'], 201);
    }
}
