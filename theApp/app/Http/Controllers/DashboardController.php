<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::count();
        $totalArtworks = Artwork::count();
        $totalReports = Report::count();
        $pendingReportsCount = Report::where('status', 'pending')->count() ?? Report::count(); // Fallback if status doesn't exist yet

        // Data for Lists
        $users = User::latest()->get();
        $artworks = Artwork::with('user')->latest()->get();
        $reports = Report::with(['user', 'artwork'])->latest()->get();
        
        // Recent activities (Mocked or derived)
        $activities = []; // Can be filled if we have an activity log

        return view('dashboard.index', compact(
            'totalUsers',
            'totalArtworks',
            'totalReports',
            'pendingReportsCount',
            'users',
            'artworks',
            'reports',
        ));
    }

    public function destroyArtwork($id)
    {
        $artwork = Artwork::findOrFail($id);
        // Delete image file if exists
        // Storage::delete($artwork->image_path); 
        $artwork->delete();
        
        return redirect()->back()->with('success', 'Artwork deleted successfully.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function toggleUserBan($id)
    {
        $user = User::findOrFail($id);
        // Simple toggle logic, assuming 'role' or a 'status' column determines ban, 
        // For now, let's assume we just change role to 'banned' or strictly 'user'
        if ($user->role === 'banned') {
            $user->role = 'user';
        } else {
             // Don't ban other admins easily, or do check
            if ($user->role !== 'admin') {
                $user->role = 'banned';
            }
        }
        $user->save();

        return redirect()->back()->with('success', 'User status updated.');
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->back()->with('success', 'Report deleted.');
    }
}
