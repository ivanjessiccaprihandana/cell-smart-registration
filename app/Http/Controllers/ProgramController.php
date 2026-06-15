<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\PlacementTestAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function quota()
    {
        $programs = Program::where('status', 'active')
            ->where('name', 'not like', '% - Reguler')
            ->where('name', 'not like', '% - Private')
            ->where('name', 'not like', '% - Conversation')
            ->latest()
            ->get()
            ->each(function (Program $program) {
                $program->registered_users_count = $program->registeredUsersCount();
                $program->remaining_quota = $program->remainingQuota();
                $program->is_full = $program->isFull();
                $program->class_type_counts = $this->programUsesClassType($program->name)
                    ? $this->classTypeCountsForProgram($program)
                    : [];
            })
            ->sortBy(fn (Program $program) => $this->programDisplayOrder($program->name))
            ->values();

        return view('program.cekkuota', [
            'programs' => $programs,
            'totalPrograms' => $programs->count(),
            'availablePrograms' => $programs->filter(fn (Program $program) => !$program->is_full)->count(),
            'fullPrograms' => $programs->filter(fn (Program $program) => $program->is_full)->count(),
        ]);
    }

    public function studentStatus()
    {
        $user = Auth::user();
        $program = $user->program ? Program::find($user->program) : null;
        $latestPlacementAttempt = PlacementTestAttempt::where('user_id', $user->id)
            ->latest()
            ->first();

        return view('student.status', [
            'auth' => $user,
            'program' => $program,
            'latestPlacementAttempt' => $latestPlacementAttempt,
        ]);
    }

    public function studentSchedule()
    {
        $user = Auth::user();
        $program = $user->program ? Program::find($user->program) : null;
        $latestPlacementAttempt = PlacementTestAttempt::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$latestPlacementAttempt) {
            return redirect()
                ->route('student.status')
                ->withErrors(['schedule' => 'Jadwal kelas akan tersedia setelah Anda menyelesaikan placement test.']);
        }

        return view('student.schedule', [
            'auth' => $user,
            'program' => $program,
            'latestPlacementAttempt' => $latestPlacementAttempt,
        ]);
    }

    private function scheduleForLevel(string $level): array
    {
        return [
            'Beginner' => [
                'days' => 'Senin & Rabu',
                'time' => '15.00-16.00',
                'frequency' => 'Seminggu 2x',
                'duration' => '1 jam / meeting',
                'room' => 'Kelas Beginner A',
            ],
            'Elementary' => [
                'days' => 'Selasa & Kamis',
                'time' => '15.00-16.00',
                'frequency' => 'Seminggu 2x',
                'duration' => '1 jam / meeting',
                'room' => 'Kelas Elementary A',
            ],
            'Pre-Intermediate' => [
                'days' => 'Senin & Rabu',
                'time' => '16.15-17.15',
                'frequency' => 'Seminggu 2x',
                'duration' => '1 jam / meeting',
                'room' => 'Kelas Pre-Intermediate A',
            ],
            'Intermediate' => [
                'days' => 'Selasa & Kamis',
                'time' => '16.15-17.15',
                'frequency' => 'Seminggu 2x',
                'duration' => '1 jam / meeting',
                'room' => 'Kelas Intermediate A',
            ],
            'Upper-Intermediate' => [
                'days' => 'Jumat & Sabtu',
                'time' => '15.00-16.00',
                'frequency' => 'Seminggu 2x',
                'duration' => '1 jam / meeting',
                'room' => 'Kelas Upper-Intermediate A',
            ],
            'Advanced' => [
                'days' => 'Jumat & Sabtu',
                'time' => '16.15-17.15',
                'frequency' => 'Seminggu 2x',
                'duration' => '1 jam / meeting',
                'room' => 'Kelas Advanced A',
            ],
        ][$level] ?? [
            'days' => 'Senin & Rabu',
            'time' => '15.00-16.00',
            'frequency' => 'Seminggu 2x',
            'duration' => '1 jam / meeting',
            'room' => 'Kelas CELL English',
        ];
    }

    /**
     * Get all programs
     */
    public function index()
    {
        $auth = Auth::user();
        $selectedProgramId = (string) request('program', $auth->program ?? '');


        $programs = Program::where('status', 'active')
            ->where('name', 'not like', '% - Reguler')
            ->where('name', 'not like', '% - Private')
            ->where('name', 'not like', '% - Conversation')
            ->latest()
            ->get()
            ->each(function (Program $program) {
                $program->registered_users_count = $program->registeredUsersCount();
                $program->remaining_quota = $program->remainingQuota();
                $program->is_full = $program->isFull();
                $program->class_type_counts = $this->programUsesClassType($program->name)
                    ? $this->classTypeCountsForProgram($program)
                    : [];
            })
            ->sortBy(fn (Program $program) => $this->programDisplayOrder($program->name))
            ->values();

        $selectedProgram = $selectedProgramId !== ''
            ? $programs->firstWhere('id', $selectedProgramId) ?? Program::find($selectedProgramId)
            : null;

        return view('program.index', [
            'auth' => $auth,
            'programs' => $programs,
            'selectedProgramModel' => $selectedProgram,
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


public function store(Request $request)
{
    $user = auth()->user();

    if ($user->program) {
        return redirect()
            ->route('programs.payment')
            ->with('success', 'Anda sudah terdaftar pada program. Silakan lanjutkan proses pembayaran.');
    }

    $request->validate([
        'whatsapp' => ['required', 'string', 'max:20'],
        'address' => ['required', 'string', 'max:255'],
        'program' => [
            'required',
            Rule::exists('programs', 'id')->where('status', 'active'),
        ],
        'class_type' => ['nullable', Rule::in(['Reguler', 'Private', 'Conversation'])],
    ]);

    $selectedProgram = Program::findOrFail($request->program);
    $classType = $this->programUsesClassType($selectedProgram->name)
        ? ($request->class_type ?: 'Reguler')
        : null;
    $program = $selectedProgram;
    $currentProgram = (string) $user->program;
    $currentClassType = $user->class_type;
    $programChanged = $currentProgram !== (string) $program->id || $currentClassType !== $classType;

    if ($program->isFull() && $currentProgram !== (string) $program->id) {
        return back()
            ->withErrors(['program' => 'Kuota program ini sudah penuh. Silakan pilih program lain.'])
            ->withInput();
    }

    if ($programChanged && $user->payment_proof_path) {
        Storage::disk('public')->delete($user->payment_proof_path);
    }

    $user->update([
        'whatsapp' => $request->whatsapp,
        'address' => $request->address,
        'program' => (string) $program->id,
        'class_type' => $classType,
        'payment_proof_path' => $programChanged ? null : $user->payment_proof_path,
        'payment_status' => $programChanged
            ? 'belum_upload'
            : ($user->payment_proof_path ? $user->payment_status : 'belum_upload'),
    ]);

    $period = $this->monthlyEnrollmentPeriod($program);

    ProgramEnrollment::create([
        'user_id' => $user->id,
        'program_id' => $program->id,
        'class_type' => $classType,
        'type' => 'new',
        'enrolled_at' => now(),
        'start_date' => $period['start_date'],
        'end_date' => $period['end_date'],
        'status' => 'pending',
    ]);

    return redirect()
        ->route('programs.payment')
        ->with('success', 'Pendaftaran program berhasil disimpan. Silakan upload bukti pembayaran.');
}

    public function payment()
    {
        $user = Auth::user();

        $program = $this->selectedProgramOrRedirect($user);
        if ($program instanceof \Illuminate\Http\RedirectResponse) {
            return $program;
        }

        return view('program.payment', [
            'auth' => $user,
            'program' => $program,
        ]);
    }

    public function uploadPayment(Request $request)
    {
        $request->validate([
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'payment_proof.required' => 'Silakan unggah bukti pembayaran terlebih dahulu.',
            'payment_proof.mimes' => 'Bukti pembayaran harus berupa JPG, PNG, atau PDF.',
            'payment_proof.max' => 'Ukuran bukti pembayaran maksimal 5MB.',
        ]);

        $user = Auth::user();

        if (!$user->program) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['program' => 'Silakan pilih program terlebih dahulu sebelum upload pembayaran.']);
        }

        if ($user->payment_proof_path) {
            Storage::disk('public')->delete($user->payment_proof_path);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $user->update([
            'payment_proof_path' => $path,
            'payment_status' => 'menunggu_verifikasi',
        ]);

        return redirect()
            ->route('programs.payment.success')
            ->with('success', 'Bukti pembayaran berhasil dikirim. Silakan tunggu admin memverifikasi pembayaran Anda.');
    }

    public function renew()
    {
        $user = Auth::user();

        $program = $this->selectedProgramOrRedirect($user);
        if ($program instanceof \Illuminate\Http\RedirectResponse) {
            return $program;
        }

        $latestEnrollment = ProgramEnrollment::query()
            ->where('user_id', $user->id)
            ->where('program_id', $program->id)
            ->latest('end_date')
            ->latest()
            ->first();

        $pendingRenewal = ProgramEnrollment::query()
            ->where('user_id', $user->id)
            ->where('program_id', $program->id)
            ->where('type', 'renewal')
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingRenewal) {
            return redirect()
                ->route('programs.payment')
                ->with('success', 'Perpanjangan program sudah dibuat. Silakan lanjutkan pembayaran.');
        }

        $period = $this->monthlyEnrollmentPeriod($program, $latestEnrollment);

        if ($user->payment_proof_path) {
            Storage::disk('public')->delete($user->payment_proof_path);
        }

        $user->update([
            'payment_proof_path' => null,
            'payment_status' => 'belum_upload',
        ]);

        ProgramEnrollment::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'class_type' => $user->class_type,
            'type' => 'renewal',
            'enrolled_at' => now(),
            'start_date' => $period['start_date'],
            'end_date' => $period['end_date'],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('programs.payment')
            ->with('success', 'Perpanjangan program berhasil dibuat. Silakan upload bukti pembayaran.');
    }

    public function paymentSuccess()
    {
        $user = Auth::user();

        $program = $this->selectedProgramOrRedirect($user);
        if ($program instanceof \Illuminate\Http\RedirectResponse) {
            return $program;
        }

        if (!$user->payment_proof_path) {
            return redirect()
                ->route('programs.payment')
                ->withErrors(['payment_proof' => 'Silakan upload bukti pembayaran terlebih dahulu.']);
        }

        return view('program.payment-success', [
            'auth' => $user,
            'program' => $program,
        ]);
    }

    private function selectedProgramOrRedirect(User $user): Program|\Illuminate\Http\RedirectResponse
    {
        if (!$user->program) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['program' => 'Silakan pilih program terlebih dahulu sebelum upload pembayaran.']);
        }

        $program = Program::find($user->program);

        if (!$program) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['program' => 'Program yang dipilih tidak ditemukan. Silakan pilih ulang.']);
        }

        return $program;
    }

    private function programDisplayOrder(string $programName): int
    {
        return [
            'English for Kids' => 10,
            'English for Teens' => 20,
            'English for Adult' => 30,
            'English Conversation' => 35,
            'TOEIC' => 40,
            'TOEFL' => 50,
            'BIMBEL TK' => 60,
            'BIMBEL SD' => 70,
            'BIMBEL SMP' => 80,
            'BIMBEL SMA' => 90,
        ][$programName] ?? 999;
    }

    private function programsWithClassType(): array
    {
        return [
            'English for Kids',
            'English for Teens',
            'English for Adult',
        ];
    }

    private function classTypeCountsForProgram(Program $program): array
    {
        $counts = User::query()
            ->where('program', (string) $program->id)
            ->selectRaw('COALESCE(class_type, ?) as class_type, COUNT(*) as total', ['Reguler'])
            ->groupBy('class_type')
            ->pluck('total', 'class_type');

        return [
            'Reguler' => (int) $counts->get('Reguler', 0),
            'Private' => (int) $counts->get('Private', 0),
            'Conversation' => (int) $counts->get('Conversation', 0),
        ];
    }

    private function programUsesClassType(string $programName): bool
    {
        return collect($this->programsWithClassType())
            ->contains(fn (string $name) => Str::lower($name) === Str::lower($programName));
    }

    private function monthlyEnrollmentPeriod(Program $program, ?ProgramEnrollment $latestEnrollment = null): array
    {
        $startDate = $latestEnrollment?->end_date
            ? $latestEnrollment->end_date->copy()->addDay()
            : Carbon::parse($program->start_date ?? now());

        return [
            'start_date' => $startDate->toDateString(),
            'end_date' => $startDate->copy()->addMonth()->toDateString(),
        ];
    }

    /**
     * Join a program
     */
    public function join(Request $request, Program $program)
    {
        $request->validate([
            
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
        ProgramEnrollment::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'program_id' => $program->id,
            'enrolled_at' => now(),
            'status' => 'pending', // or 'active' depending on requirement
        ]);

        return redirect()->route('programs.index')->with('success', 'Berhasil mendaftar ke program. Tunggu konfirmasi dari admin.');
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
