<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    /**
     * Get all programs
     */
    public function index()
    {
        $programs = Program::where('status', 'active')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    /**
     * Get program detail
     */
    public function show(Program $program)
    {
        $program->load(['users']);

        return response()->json([
            'success' => true,
            'data' => $program,
        ]);
    }

    /**
     * Join a program
     */
    public function join(Request $request, Program $program)
    {
        $request->validate([
            // Add custom fields if needed
        ]);

        $user = Auth::user();

        // Check if user is already enrolled
        $existing = ProgramEnrollment::where('user_id', $user->id)
            ->where('program_id', $program->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di program ini',
            ], 422);
        }

        // Create enrollment
        $enrollment = ProgramEnrollment::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'enrolled_at' => now(),
            'status' => 'pending', // or 'active' depending on requirement
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendaftar program',
            'data' => $enrollment,
        ], 201);
    }

    /**
     * Leave a program
     */
    public function leave(Program $program)
    {
        $user = Auth::user();

        $enrollment = ProgramEnrollment::where('user_id', $user->id)
            ->where('program_id', $program->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar di program ini',
            ], 404);
        }

        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar dari program',
        ]);
    }

    /**
     * Get user's programs
     */
    public function myPrograms()
    {
        $user = Auth::user();
        $programs = $user->programs()
            ->with('users')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    /**
     * Get program participants
     */
    public function participants(Program $program)
    {
        $participants = $program->users()
            ->with('user')
            ->where('status', 'active')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $participants->count(),
            'data' => $participants,
        ]);
    }

    /**
     * Approve enrollment (admin)
     */
    public function approveEnrollment(ProgramEnrollment $enrollment)
    {
        $enrollment->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran disetujui',
            'data' => $enrollment,
        ]);
    }

    /**
     * Reject enrollment (admin)
     */
    public function rejectEnrollment(ProgramEnrollment $enrollment)
    {
        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran ditolak',
        ]);
    }
}
