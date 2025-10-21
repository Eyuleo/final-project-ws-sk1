<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['studentProfile', 'clientProfile'])
            ->whereIn('role', ['student', 'client']); // Exclude admin users

        // Filter by role
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['studentProfile', 'clientProfile']);

        // Get user statistics
        if ($user->role === 'student' && $user->studentProfile) {
            $stats = [
                'total_services' => $user->studentProfile->serviceListings()->count(),
                'active_services' => $user->studentProfile->serviceListings()->where('status', 'active')->count(),
                'total_orders' => $user->studentProfile->orders()->count(),
                'completed_orders' => $user->studentProfile->orders()->where('status', 'approved')->count(),
                'total_earnings' => $user->studentProfile->total_earnings ?? 0,
                'average_rating' => $user->studentProfile->average_rating ?? 0,
            ];
        } elseif ($user->role === 'client' && $user->clientProfile) {
            $stats = [
                'total_orders' => $user->clientProfile->orders()->count(),
                'completed_orders' => $user->clientProfile->orders()->where('status', 'approved')->count(),
                'total_spent' => $user->clientProfile->orders()
                    ->where('status', 'approved')
                    ->sum('total_amount'),
            ];
        } else {
            // Fallback for users without profiles
            $stats = [
                'total_services' => 0,
                'active_services' => 0,
                'total_orders' => 0,
                'completed_orders' => 0,
                'total_earnings' => 0,
                'average_rating' => 0,
            ];
        }

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Delete a user
     */
    public function destroy(Request $request, User $user)
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        if ($user->role === 'student') {
            $activeOrders = $user->studentProfile->orders()
                ->whereIn('status', ['pending', 'accepted', 'in_progress', 'completed'])
                ->count();

            if ($activeOrders > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete user with active orders.');
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
