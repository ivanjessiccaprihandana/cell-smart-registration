<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ClassSchedule;
use App\Models\ProgramEnrollment;
use App\Models\PlacementTestAttempt;
use App\Models\SchedulePreference;
use App\Models\ScheduleTemplate;
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
                $activeScheduleTemplates = ScheduleTemplate::with('preferences')
                    ->where('is_active', true)
                    ->where('program_id', $program->id)
                    ->get();
                $program->schedule_total_capacity = $activeScheduleTemplates->sum(fn (ScheduleTemplate $template) => (int) $template->max_students);
                $program->schedule_used_capacity = $activeScheduleTemplates->sum(fn (ScheduleTemplate $template) => $template->activeStudentCount());
                $program->schedule_remaining_capacity = max(0, $program->schedule_total_capacity - $program->schedule_used_capacity);
                $program->available_schedule_count = $activeScheduleTemplates
                    ->filter(fn (ScheduleTemplate $template) => !$template->isFull())
                    ->count();
                $program->schedule_is_full = $activeScheduleTemplates->isNotEmpty()
                    && $program->available_schedule_count === 0;
                $program->class_type_counts = $this->programUsesClassType($program->name)
                    ? $this->classTypeCountsForProgram($program)
                    : [];
            })
            ->sortBy(fn (Program $program) => $this->programDisplayOrder($program->name))
            ->values();

        return view('program.cekkuota', [
            'programs' => $programs,
            'totalPrograms' => $programs->count(),
            'availablePrograms' => $programs->filter(fn (Program $program) => !$program->is_full && !$program->schedule_is_full)->count(),
            'fullPrograms' => $programs->filter(fn (Program $program) => $program->is_full || $program->schedule_is_full)->count(),
        ]);
    }

    public function studentStatus()
    {
        $user = Auth::user();

        if ($this->expireUnpaidRegistration($user)) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['program' => 'Batas upload bukti pembayaran 12 jam sudah lewat. Silakan pilih program dan jadwal kembali.']);
        }

        $program = $user->program ? Program::find($user->program) : null;
        $latestPlacementAttempt = PlacementTestAttempt::where('user_id', $user->id)
            ->latest()
            ->first();
        $latestEnrollment = $this->latestEnrollmentForUser($user, $program);
        $hasUpcomingSchedule = ClassSchedule::query()
            ->where('user_id', $user->id)
            ->whereDate('class_date', '>=', now()->toDateString())
            ->exists();
        $isProgramFinished = $program
            && $user->payment_status === 'diterima'
            && $latestEnrollment?->end_date
            && $latestEnrollment->end_date->lt(now()->startOfDay())
            && !$hasUpcomingSchedule;

        return view('student.status', [
            'auth' => $user,
            'program' => $program,
            'latestPlacementAttempt' => $latestPlacementAttempt,
            'requiresPlacementTest' => $program ? $this->programRequiresPlacementTest($program) : true,
            'latestEnrollment' => $latestEnrollment,
            'isProgramFinished' => $isProgramFinished,
        ]);
    }

    public function studentSchedule()
    {
        $user = Auth::user();
        $program = $user->program ? Program::find($user->program) : null;
        $latestPlacementAttempt = PlacementTestAttempt::where('user_id', $user->id)
            ->latest()
            ->first();

        $requiresPlacementTest = $program ? $this->programRequiresPlacementTest($program) : true;
        $allAssignedSchedules = ClassSchedule::with(['program', 'tutor', 'classRoom'])
            ->where('user_id', $user->id)
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get();
        $upcomingAssignedSchedules = $allAssignedSchedules
            ->filter(fn (ClassSchedule $schedule) => $schedule->class_date->gte(now()->startOfDay()))
            ->values();
        $latestEnrollment = $this->latestEnrollmentForUser($user, $program);
        $isProgramFinished = $allAssignedSchedules->isNotEmpty()
            && $upcomingAssignedSchedules->isEmpty();

        if ($requiresPlacementTest && !$latestPlacementAttempt) {
            return redirect()
                ->route('student.status')
                ->withErrors(['schedule' => 'Jadwal belajar akan tersedia setelah Anda menyelesaikan placement test.']);
        }

        return view('student.schedule', [
            'auth' => $user,
            'program' => $program,
            'latestPlacementAttempt' => $latestPlacementAttempt,
            'requiresPlacementTest' => $requiresPlacementTest,
            'latestEnrollment' => $latestEnrollment,
            'isProgramFinished' => $isProgramFinished,
            'scheduleDisplayMode' => $isProgramFinished ? 'history' : 'upcoming',
            'scheduleTemplates' => $program ? $this->matchingScheduleTemplates($user, $program, $latestPlacementAttempt?->level) : collect(),
            'schedulePreferences' => SchedulePreference::with('scheduleTemplate.program', 'scheduleTemplate.tutor')
                ->where('user_id', $user->id)
                ->orderBy('priority')
                ->get(),
            'dayLabels' => $this->dayLabels(),
            'assignedSchedules' => $isProgramFinished ? $allAssignedSchedules : $upcomingAssignedSchedules,
        ]);
    }

    public function storeSchedulePreferences(Request $request)
    {
        $user = Auth::user();
        $program = $user->program ? Program::find($user->program) : null;

        if (!$program || $user->payment_status !== 'diterima') {
            return redirect()
                ->route('student.status')
                ->withErrors(['schedule' => 'Preferensi jadwal hanya bisa dipilih setelah pembayaran disetujui.']);
        }

        $latestPlacementAttempt = PlacementTestAttempt::where('user_id', $user->id)
            ->latest()
            ->first();

        $validTemplateIds = $this->matchingScheduleTemplates($user, $program, $latestPlacementAttempt?->level)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $validated = $request->validate([
            'schedule_template_id' => ['required', Rule::in($validTemplateIds)],
        ], [
            'schedule_template_id.required' => 'Pilih salah satu jadwal belajar.',
        ]);

        SchedulePreference::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->delete();

        SchedulePreference::create([
            'user_id' => $user->id,
            'schedule_template_id' => $validated['schedule_template_id'],
            'priority' => 1,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('student.schedule')
            ->with('success', 'Jadwal belajar berhasil dipilih.');
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

        if ($auth) {
            $this->expireUnpaidRegistration($auth);
            $auth->refresh();
        }

        $selectedProgramId = (string) request('program', $auth->program ?? '');


        $programs = $this->activeSelectablePrograms();

        $selectedProgram = $selectedProgramId !== ''
            ? $programs->firstWhere('id', $selectedProgramId) ?? Program::find($selectedProgramId)
            : null;
        $currentProgram = $auth?->program ? Program::find($auth->program) : null;
        $canStartNewProgramAfterCompletion = $auth && $currentProgram
            ? $this->hasFinishedCurrentProgram($auth, $currentProgram)
            : false;
        $currentScheduleTemplateId = SchedulePreference::query()
            ->where('user_id', $auth->id)
            ->whereIn('status', ['pending', 'assigned'])
            ->oldest('priority')
            ->value('schedule_template_id');

        return view('program.index', [
            'auth' => $auth,
            'programs' => $programs,
            'selectedProgramModel' => $selectedProgram,
            'currentScheduleTemplateId' => $currentScheduleTemplateId ? (string) $currentScheduleTemplateId : '',
            'scheduleTemplates' => ScheduleTemplate::with(['program', 'tutor', 'classRoom', 'preferences'])
                ->where('is_active', true)
                ->where(function ($query) {
                    $query
                        ->whereNull('registration_start_date')
                        ->orWhereDate('registration_start_date', '<=', now()->toDateString());
                })
                ->where(function ($query) {
                    $query
                        ->whereNull('registration_end_date')
                        ->orWhereDate('registration_end_date', '>=', now()->toDateString());
                })
                ->where(function ($query) {
                    $query
                        ->whereNull('learning_end_date')
                        ->orWhereDate('learning_end_date', '>=', now()->toDateString());
                })
                ->orderBy('program_id')
                ->orderBy('start_time')
                ->get(),
            'dayLabels' => $this->dayLabels(),
            'isChangingSelection' => request()->boolean('change'),
            'canStartNewProgramAfterCompletion' => $canStartNewProgramAfterCompletion,
        ]);
    }

    public function change()
    {
        $auth = Auth::user();

        if ($this->expireUnpaidRegistration($auth)) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['program' => 'Batas upload bukti pembayaran 12 jam sudah lewat. Silakan pilih program dan jadwal kembali.']);
        }

        $paymentStatus = $auth->payment_status ?: 'belum_upload';
        $currentProgram = $auth->program ? Program::find($auth->program) : null;
        $canStartNewProgramAfterCompletion = $currentProgram
            ? $this->hasFinishedCurrentProgram($auth, $currentProgram)
            : false;
        $canChangeProgramBeforePayment = $auth->program
            && !$auth->payment_proof_path
            && $paymentStatus === 'belum_upload';

        if ($auth->program && !$canChangeProgramBeforePayment && !$canStartNewProgramAfterCompletion) {
            return redirect()
                ->route('programs.payment')
                ->withErrors(['program' => 'Program tidak bisa diubah setelah bukti pembayaran diupload. Silakan hubungi admin jika perlu perubahan.']);
        }

        return view('program.change', [
            'auth' => $auth,
            'programs' => $this->activeSelectablePrograms(),
            'currentProgramId' => (string) ($auth->program ?? ''),
            'paymentDeadline' => $auth->registration_expires_at,
            'canStartNewProgramAfterCompletion' => $canStartNewProgramAfterCompletion,
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
    $this->expireUnpaidRegistration($user);
    $user->refresh();

    $canChangeProgramBeforePayment = $user->program
        && !$user->payment_proof_path
        && ($user->payment_status ?: 'belum_upload') === 'belum_upload';
    $currentProgramModel = $user->program ? Program::find($user->program) : null;
    $canStartNewProgramAfterCompletion = $currentProgramModel
        ? $this->hasFinishedCurrentProgram($user, $currentProgramModel)
        : false;

    if ($user->program && !$canChangeProgramBeforePayment && !$canStartNewProgramAfterCompletion) {
        return redirect()
            ->route('programs.payment')
            ->withErrors(['program' => 'Program tidak bisa diubah setelah bukti pembayaran diupload. Silakan hubungi admin jika perlu perubahan.']);
    }

    $request->validate([
        'whatsapp' => ['required', 'string', 'max:20'],
        'address' => ['required', 'string', 'max:255'],
        'program' => [
            'required',
            Rule::exists('programs', 'id')->where('status', 'active'),
        ],
            'class_type' => ['nullable', Rule::in(['Reguler', 'Private'])],
            'private_package' => ['nullable', Rule::in(array_keys(Program::privatePackages()))],
            'schedule_template_id' => ['nullable', Rule::exists('schedule_templates', 'id')],
        ]);

    $selectedProgram = Program::findOrFail($request->program);
    $classType = $selectedProgram->usesClassType()
        ? ($request->class_type ?: 'Reguler')
        : 'Reguler';

    if (!$selectedProgram->allowsClassType($classType)) {
        return back()
            ->withErrors(['class_type' => 'Jenis kelas ini tidak tersedia untuk program yang dipilih.'])
            ->withInput();
    }

    $privatePackage = $classType === 'Private' ? $request->input('private_package') : null;

    if ($classType === 'Private' && !$selectedProgram->allowsPrivatePackage($privatePackage)) {
        return back()
            ->withErrors(['private_package' => 'Pilih paket private yang tersedia untuk English for Adult.'])
            ->withInput();
    }
    $selectedScheduleTemplateId = $request->input('schedule_template_id');

    $matchingScheduleTemplates = $this->matchingRegistrationScheduleTemplates($selectedProgram, $classType, false, $privatePackage);
    $availableScheduleTemplates = $matchingScheduleTemplates->filter(fn (ScheduleTemplate $template) => $template->hasSeatForUser($user->id))->values();
    $validScheduleTemplateIds = $availableScheduleTemplates
        ->pluck('id')
        ->map(fn ($id) => (string) $id);

    if ($matchingScheduleTemplates->isNotEmpty() && $availableScheduleTemplates->isEmpty()) {
        return back()
            ->withErrors(['schedule_template_id' => 'Semua pilihan jadwal untuk program dan jenis kelas ini sudah penuh. Silakan pilih program/jenis kelas lain atau hubungi admin.'])
            ->withInput();
    }

    if ($validScheduleTemplateIds->isNotEmpty() && !$selectedScheduleTemplateId) {
        return back()
            ->withErrors(['schedule_template_id' => 'Pilih salah satu jadwal belajar yang tersedia.'])
            ->withInput();
    }

    if ($selectedScheduleTemplateId && !$validScheduleTemplateIds->contains((string) $selectedScheduleTemplateId)) {
        return back()
            ->withErrors(['schedule_template_id' => 'Jadwal belajar tidak sesuai dengan program, jenis kelas, atau kapasitas sudah penuh.'])
            ->withInput();
    }

    $program = $selectedProgram;
    $currentProgram = (string) $user->program;
    $currentClassType = $user->class_type;
    $currentPrivatePackage = $user->private_package;
    $programChanged = $currentProgram !== (string) $program->id || $currentClassType !== $classType || $currentPrivatePackage !== $privatePackage;

    if ($program->isFull() && $currentProgram !== (string) $program->id) {
        return back()
            ->withErrors(['program' => 'Kuota program ini sudah penuh. Silakan pilih program lain.'])
            ->withInput();
    }

    $user->update([
        'whatsapp' => $request->whatsapp,
        'address' => $request->address,
        'program' => (string) $program->id,
        'class_type' => $classType,
        'private_package' => $privatePackage,
        'payment_proof_path' => null,
        'payment_status' => 'belum_upload',
        'registration_expires_at' => now()->addHours(12),
    ]);

    $selectedScheduleTemplate = $selectedScheduleTemplateId
        ? ScheduleTemplate::find($selectedScheduleTemplateId)
        : null;
    $period = $selectedScheduleTemplate
        ? $this->enrollmentPeriodForScheduleTemplate($program, $selectedScheduleTemplate)
        : $this->monthlyEnrollmentPeriod($program);

    ProgramEnrollment::query()
        ->where('user_id', $user->id)
        ->whereIn('status', ['pending', 'rejected'])
        ->update(['status' => 'rejected']);

    ProgramEnrollment::create([
        'user_id' => $user->id,
        'program_id' => $program->id,
        'class_type' => $classType,
        'private_package' => $privatePackage,
        'type' => 'new',
        'enrolled_at' => now(),
        'start_date' => $period['start_date'],
        'end_date' => $period['end_date'],
        'status' => 'pending',
    ]);

    SchedulePreference::where('user_id', $user->id)
        ->whereIn('status', ['pending', 'rejected'])
        ->delete();

    if ($selectedScheduleTemplateId) {
        SchedulePreference::create([
            'user_id' => $user->id,
            'schedule_template_id' => $selectedScheduleTemplateId,
            'priority' => 1,
            'status' => 'pending',
        ]);
    }

    return redirect()
        ->route('programs.payment')
        ->with('success', 'Pendaftaran program berhasil disimpan. Silakan upload bukti pembayaran.');
}

    public function payment()
    {
        $user = Auth::user();

        if ($this->expireUnpaidRegistration($user)) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['program' => 'Batas upload bukti pembayaran 12 jam sudah lewat. Silakan pilih program dan jadwal kembali.']);
        }

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

        if ($this->expireUnpaidRegistration($user)) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['program' => 'Batas upload bukti pembayaran 12 jam sudah lewat. Silakan pilih program dan jadwal kembali.']);
        }

        if (!$user->program) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['program' => 'Silakan pilih program terlebih dahulu sebelum upload pembayaran.']);
        }

        $preference = SchedulePreference::with('scheduleTemplate')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->oldest('priority')
            ->first();

        if ($preference?->scheduleTemplate && !$preference->scheduleTemplate->hasSeatForUser($user->id)) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['schedule_template_id' => 'Jadwal belajar yang Anda pilih sudah penuh. Silakan pilih jadwal lain sebelum upload bukti pembayaran.']);
        }

        if ($user->payment_proof_path) {
            Storage::disk('public')->delete($user->payment_proof_path);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $user->update([
            'payment_proof_path' => $path,
            'payment_status' => 'menunggu_verifikasi',
            'registration_expires_at' => null,
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
            'private_package' => $user->private_package,
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
            'requiresPlacementTest' => $this->programRequiresPlacementTest($program),
        ]);
    }

    public function invoice()
    {
        $user = Auth::user();

        $program = $this->selectedProgramOrRedirect($user);
        if ($program instanceof \Illuminate\Http\RedirectResponse) {
            return $program;
        }

        if ($user->payment_status !== 'diterima') {
            return redirect()
                ->route('student.status')
                ->withErrors(['invoice' => 'Invoice hanya bisa dicetak setelah pembayaran disetujui admin.']);
        }

        $latestEnrollment = ProgramEnrollment::query()
            ->where('user_id', $user->id)
            ->where('program_id', $program->id)
            ->latest('end_date')
            ->latest()
            ->first();

        return view('program.invoice', [
            'auth' => $user,
            'program' => $program,
            'latestEnrollment' => $latestEnrollment,
            'invoiceNumber' => 'INV-CELL-' . now()->format('Y') . '-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad((string) $program->id, 3, '0', STR_PAD_LEFT),
            'paidAt' => $user->updated_at ?? now(),
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

    private function latestEnrollmentForUser(User $user, ?Program $program): ?ProgramEnrollment
    {
        if (!$program) {
            return null;
        }

        return ProgramEnrollment::query()
            ->where('user_id', $user->id)
            ->where('program_id', $program->id)
            ->latest('end_date')
            ->latest()
            ->first();
    }

    private function hasFinishedCurrentProgram(User $user, Program $program): bool
    {
        $latestEnrollment = $this->latestEnrollmentForUser($user, $program);

        if (
            ($user->payment_status ?: 'belum_upload') !== 'diterima'
            || !$latestEnrollment?->end_date
            || $latestEnrollment->end_date->greaterThanOrEqualTo(now()->startOfDay())
        ) {
            return false;
        }

        return !ClassSchedule::query()
            ->where('user_id', $user->id)
            ->where('program_id', $program->id)
            ->whereDate('class_date', '>=', now()->toDateString())
            ->exists();
    }

    private function expireUnpaidRegistration(User $user): bool
    {
        $paymentStatus = $user->payment_status ?: 'belum_upload';

        if (
            !$user->program
            || $user->payment_proof_path
            || $paymentStatus !== 'belum_upload'
            || !$user->registration_expires_at
            || now()->lessThanOrEqualTo($user->registration_expires_at)
        ) {
            return false;
        }

        ProgramEnrollment::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->update(['status' => 'rejected']);

        SchedulePreference::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->delete();

        $user->update([
            'program' => null,
            'class_type' => null,
            'private_package' => null,
            'payment_status' => 'belum_upload',
            'registration_expires_at' => null,
        ]);

        return true;
    }

    private function activeSelectablePrograms()
    {
        return Program::where('status', 'active')
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
    }

    private function programDisplayOrder(string $programName): int
    {
        return [
            'English for Kids' => 10,
            'English for Teens' => 20,
            'English for Adult' => 30,
            'BIMBEL TK' => 60,
            'BIMBEL SD' => 70,
            'BIMBEL SMP' => 80,
            'BIMBEL SMA' => 90,
        ][$programName] ?? 999;
    }

    private function programsWithClassType(): array
    {
        return [
            'English for Adult',
        ];
    }

    private function classTypeCountsForProgram(Program $program): array
    {
        $counts = ProgramEnrollment::query()
            ->current()
            ->where('program_id', $program->id)
            ->whereHas('user', function ($query) {
                $query->whereIn('payment_status', ['menunggu_verifikasi', 'diterima']);
            })
            ->selectRaw('COALESCE(class_type, ?) as class_type, COUNT(*) as total', ['Reguler'])
            ->groupBy('class_type')
            ->pluck('total', 'class_type');

        return [
            'Reguler' => (int) $counts->get('Reguler', 0),
            'Private' => (int) $counts->get('Private', 0),
        ];
    }

    private function matchingScheduleTemplates(User $user, Program $program, ?string $level)
    {
        return ScheduleTemplate::with(['program', 'tutor', 'classRoom', 'preferences'])
            ->where('is_active', true)
            ->where('program_id', $program->id)
            ->where(function ($query) {
                $query
                    ->whereNull('registration_start_date')
                    ->orWhereDate('registration_start_date', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('registration_end_date')
                    ->orWhereDate('registration_end_date', '>=', now()->toDateString());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('learning_end_date')
                    ->orWhereDate('learning_end_date', '>=', now()->toDateString());
            })
            ->where(function ($query) use ($user) {
                $query
                    ->whereNull('class_type')
                    ->orWhere('class_type', $user->class_type);
            })
            ->where(function ($query) use ($user) {
                if ($user->class_type === 'Private') {
                    $query->where('private_package', $user->private_package);
                    return;
                }

                $query->whereNull('private_package');
            })
            ->where(function ($query) use ($level) {
                $query
                    ->whereNull('level')
                    ->when($level, fn ($query) => $query->orWhere('level', $level));
            })
            ->orderBy('start_time')
            ->get()
            ->filter(fn (ScheduleTemplate $template) => !$template->isFull())
            ->values();
    }

    private function matchingRegistrationScheduleTemplates(Program $program, ?string $classType, bool $onlyAvailable = true, ?string $privatePackage = null)
    {
        $templates = ScheduleTemplate::with('preferences')
            ->where('is_active', true)
            ->where('program_id', $program->id)
            ->where(function ($query) {
                $query
                    ->whereNull('registration_start_date')
                    ->orWhereDate('registration_start_date', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('registration_end_date')
                    ->orWhereDate('registration_end_date', '>=', now()->toDateString());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('learning_end_date')
                    ->orWhereDate('learning_end_date', '>=', now()->toDateString());
            })
            ->where(function ($query) use ($classType) {
                $query
                    ->whereNull('class_type')
                    ->when($classType, fn ($query) => $query->orWhere('class_type', $classType));
            })
            ->where(function ($query) use ($classType, $privatePackage) {
                if ($classType === 'Private') {
                    $query->where('private_package', $privatePackage);
                    return;
                }

                $query->whereNull('private_package');
            })
            ->get();

        return $onlyAvailable
            ? $templates->filter(fn (ScheduleTemplate $template) => !$template->isFull())->values()
            : $templates->values();
    }

    private function dayLabels(): array
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
    }

    private function programUsesClassType(string $programName): bool
    {
        $program = Program::where('name', $programName)->first();

        return $program?->usesClassType() ?? false;
    }

    private function programRequiresPlacementTest(Program $program): bool
    {
        $category = Str::lower($program->category ?? '');
        $name = Str::lower($program->name);

        return !(
            $category === 'bimbel'
            || $category === 'test preparation'
            || Str::startsWith($name, 'bimbel')
            || in_array($name, ['toeic', 'toefl'], true)
        );
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

    private function enrollmentPeriodForScheduleTemplate(Program $program, ScheduleTemplate $template): array
    {
        $meetingCount = $program->meetingCountForClassType($template->class_type, $template->private_package);

        if ($meetingCount && $template->learning_start_date) {
            $endDate = $this->nthDateForTemplateDays(
                $template->learning_start_date->copy(),
                $template->days ?? [],
                $meetingCount
            );

            return [
                'start_date' => $template->learning_start_date->toDateString(),
                'end_date' => $endDate->toDateString(),
            ];
        }

        if ($template->learning_start_date && $template->learning_end_date) {
            return [
                'start_date' => $template->learning_start_date->toDateString(),
                'end_date' => $template->learning_end_date->toDateString(),
            ];
        }

        return $this->monthlyEnrollmentPeriod($program);
    }

    private function nthDateForTemplateDays(Carbon $startDate, array $days, int $targetCount): Carbon
    {
        $days = collect($days)->map(fn ($day) => (int) $day)->all();
        $cursor = $startDate->copy()->startOfDay();
        $matched = 0;

        while ($matched < $targetCount) {
            if (in_array($cursor->isoWeekday(), $days, true)) {
                $matched++;
            }

            if ($matched < $targetCount) {
                $cursor->addDay();
            }
        }

        return $cursor;
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
