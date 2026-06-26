<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\PlacementQuestion;
use App\Models\PlacementTestAttempt;
use App\Models\ProgramCategory;
use App\Models\ProgramEnrollment;
use App\Models\SchedulePreference;
use App\Models\ScheduleTemplate;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function dashboard()
    {
        $programs = Program::latest()
            ->get()
            ->each(function (Program $program) {
                $program->registered_users_count = $program->registeredUsersCount();
                $program->remaining_quota = $program->remainingQuota();
                $program->is_full = $program->isFull();
            });

        $programLabels = Program::pluck('name', 'id')
            ->mapWithKeys(fn ($name, $id) => [(string) $id => $name])
            ->all();

        $registeredUsers = User::query()
            ->whereNotNull('program')
            ->latest()
            ->get();

        $programCounts = $registeredUsers
            ->groupBy('program')
            ->map(function ($users, $program) use ($programLabels) {
                return [
                    'name' => $programLabels[$program] ?? $program,
                    'count' => $users->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        $totalQuota = $programs->sum(fn (Program $program) => (int) $program->quota);
        $usedQuota = $programs->sum(fn (Program $program) => (int) $program->registered_users_count);
        $remainingQuota = $programs->sum(fn (Program $program) => (int) ($program->remaining_quota ?? 0));
        $paymentCounts = $registeredUsers->countBy(fn (User $user) => $user->payment_status ?: 'belum_upload');
        $weeklyRegistrants = collect(range(6, 0))
            ->map(function (int $daysAgo) use ($registeredUsers) {
                $date = now()->subDays($daysAgo);

                return [
                    'label' => $date->format('d M'),
                    'count' => $registeredUsers
                        ->filter(fn (User $user) => $user->updated_at?->isSameDay($date))
                        ->count(),
                ];
            });
        $maxWeeklyRegistrants = max(1, $weeklyRegistrants->max('count'));

        return view('admin.dashboard', [
            'title' => 'Admin Dashboard',
            'programLabels' => $programLabels,
            'programs' => $programs,
            'registeredUsers' => $registeredUsers,
            'programCounts' => $programCounts,
            'paymentCounts' => $paymentCounts,
            'weeklyRegistrants' => $weeklyRegistrants,
            'maxWeeklyRegistrants' => $maxWeeklyRegistrants,
            'stats' => [
                'totalUsers' => User::count(),
                'totalRegistrants' => $registeredUsers->count(),
                'activePrograms' => $programs->where('status', 'active')->count(),
                'totalPrograms' => $programs->count(),
                'totalQuota' => $totalQuota,
                'usedQuota' => $usedQuota,
                'remainingQuota' => $remainingQuota,
                'pendingPayments' => (int) $paymentCounts->get('menunggu_verifikasi', 0),
                'todayRegistrants' => $registeredUsers
                    ->filter(fn (User $user) => $user->updated_at?->isToday())
                    ->count(),
            ],
        ]);
    }

    public function registrants(Request $request)
    {
        $programs = Program::query()
            ->orderBy('name')
            ->get();

        $programLabels = $programs
            ->pluck('name', 'id')
            ->mapWithKeys(fn ($name, $id) => [(string) $id => $name])
            ->all();

        $selectedProgram = $request->query('program');
        $selectedClassType = $request->query('class_type');
        $validProgramIds = $programs
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
        $validClassTypes = array_keys($this->classTypes());
        $selectedProgram = in_array((string) $selectedProgram, $validProgramIds, true) ? (string) $selectedProgram : null;
        $selectedClassType = in_array($selectedClassType, $validClassTypes, true) ? $selectedClassType : null;

        $registrants = User::query()
            ->whereNotNull('program')
            ->when($selectedProgram, function ($query) use ($selectedProgram) {
                $query->where('program', $selectedProgram);
            })
            ->when($selectedClassType, function ($query) use ($selectedClassType) {
                $query->where('class_type', $selectedClassType);
            })
            ->latest()
            ->get();

        $groupedRegistrants = $registrants
            ->groupBy(fn (User $user) => (string) $user->program)
            ->sortBy(fn ($users, $programId) => $programLabels[$programId] ?? $programId);

        $programSummaries = $programs
            ->map(function (Program $program) use ($programLabels, $selectedClassType) {
                $students = User::query()
                    ->where('program', (string) $program->id)
                    ->when($selectedClassType, function ($query) use ($selectedClassType) {
                        $query->where('class_type', $selectedClassType);
                    })
                    ->get();

                return [
                    'id' => (string) $program->id,
                    'name' => $programLabels[(string) $program->id] ?? $program->name,
                    'total' => $students->count(),
                    'accepted' => $students->where('payment_status', 'diterima')->count(),
                    'pending' => $students->where('payment_status', 'menunggu_verifikasi')->count(),
                ];
            })
            ->filter(fn (array $summary) => $summary['total'] > 0)
            ->sortByDesc('total')
            ->values();

        return view('admin.registrants.index', [
            'title' => 'Data Pendaftar',
            'registrants' => $registrants,
            'groupedRegistrants' => $groupedRegistrants,
            'programs' => $programs,
            'programLabels' => $programLabels,
            'programSummaries' => $programSummaries,
            'selectedProgram' => $selectedProgram,
            'selectedClassType' => $selectedClassType,
            'classTypes' => $this->classTypes(),
            'paymentLabels' => $this->paymentStatuses(),
        ]);
    }

    public function editRegistrant(User $user)
    {
        if (!$user->program) {
            return redirect()
                ->route('admin.registrants.index')
                ->withErrors(['registrant' => 'Siswa ini belum memilih program.']);
        }

        return view('admin.registrants.edit', [
            'title' => 'Edit Pendaftar',
            'registrant' => $user,
            ...$this->registrantFormData(),
        ]);
    }

    public function updateRegistrant(Request $request, User $user)
    {
        $validated = $this->validateRegistrant($request);
        $program = Program::findOrFail($validated['program']);
        $classType = $this->programUsesClassType($program->name)
            ? ($validated['class_type'] ?? 'Reguler')
            : null;

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'address' => $validated['address'] ?? null,
            'program' => (string) $program->id,
            'class_type' => $classType,
            'payment_status' => $validated['payment_status'],
        ]);

        $enrollmentStatus = match ($validated['payment_status']) {
            'diterima' => 'active',
            'ditolak' => 'rejected',
            default => 'pending',
        };

        $latestEnrollment = ProgramEnrollment::query()
            ->where('user_id', $user->id)
            ->latest('end_date')
            ->latest()
            ->first();

        if ($latestEnrollment) {
            $latestEnrollment->update([
                'program_id' => $program->id,
                'class_type' => $classType,
                'status' => $enrollmentStatus,
            ]);
        } else {
            ProgramEnrollment::create([
                'user_id' => $user->id,
                'program_id' => $program->id,
                'class_type' => $classType,
                'type' => 'new',
                'enrolled_at' => now(),
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'status' => $enrollmentStatus,
            ]);
        }

        return redirect()
            ->route('admin.registrants.index', ['program' => $program->id, 'class_type' => $classType])
            ->with('success', 'Data pendaftar berhasil diperbarui.');
    }

    public function cancelRegistrant(User $user)
    {
        if (!$user->program) {
            return redirect()
                ->route('admin.registrants.index')
                ->withErrors(['registrant' => 'Pendaftaran siswa ini sudah tidak aktif.']);
        }

        if ($user->classSchedules()->exists()) {
            return redirect()
                ->route('admin.registrants.index', ['program' => $user->program, 'class_type' => $user->class_type])
                ->withErrors(['registrant' => 'Pendaftaran tidak bisa dibatalkan karena siswa sudah memiliki jadwal siswa. Hapus atau ubah jadwalnya terlebih dahulu.']);
        }

        $programId = $user->program;
        $classType = $user->class_type;

        ProgramEnrollment::query()
            ->where('user_id', $user->id)
            ->when($programId, fn ($query) => $query->where('program_id', (int) $programId))
            ->whereIn('status', ['pending', 'active'])
            ->latest('end_date')
            ->latest()
            ->first()
            ?->update(['status' => 'rejected']);

        $user->update([
            'program' => null,
            'class_type' => null,
            'payment_status' => 'ditolak',
        ]);

        return redirect()
            ->route('admin.registrants.index', ['program' => $programId, 'class_type' => $classType])
            ->with('success', 'Pendaftaran siswa berhasil dibatalkan. Akun siswa tetap tersimpan.');
    }

    public function programs()
    {
        $programs = Program::query()
            ->where('name', 'not like', '% - Reguler')
            ->where('name', 'not like', '% - Private')
            ->where('name', 'not like', '% - Conversation')
            ->latest()
            ->paginate(10);

        $programs->getCollection()->each(function (Program $program) {
            $program->registered_users_count = $program->registeredUsersCount();
            $program->remaining_quota = $program->remainingQuota();
            $activeScheduleTemplates = ScheduleTemplate::with('preferences')
                ->where('is_active', true)
                ->where('program_id', $program->id)
                ->get();
            $program->schedule_total_capacity = $activeScheduleTemplates->sum(fn (ScheduleTemplate $template) => (int) $template->max_students);
            $program->schedule_used_capacity = $activeScheduleTemplates->sum(fn (ScheduleTemplate $template) => $template->activeStudentCount());
            $program->schedule_remaining_capacity = max(0, $program->schedule_total_capacity - $program->schedule_used_capacity);
            $program->schedule_is_full = $activeScheduleTemplates->isNotEmpty() && $program->schedule_remaining_capacity <= 0;
        });

        return view('admin.programs.index', [
            'title' => 'Kelola Program',
            'programs' => $programs,
        ]);
    }

    private function validateRegistrant(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->route('user'))],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'program' => ['required', Rule::exists('programs', 'id')->where('status', 'active')],
            'class_type' => ['nullable', Rule::in(array_keys($this->classTypes()))],
            'payment_status' => ['required', Rule::in(array_keys($this->paymentStatuses()))],
        ]);

        $program = Program::findOrFail($validated['program']);

        if ($this->programUsesClassType($program->name) && empty($validated['class_type'])) {
            throw ValidationException::withMessages([
                'class_type' => 'Jenis kelas wajib dipilih untuk program ini.',
            ]);
        }

        return $validated;
    }

    private function registrantFormData(): array
    {
        return [
            'programs' => Program::where('status', 'active')->orderBy('name')->get(),
            'classTypes' => $this->classTypes(),
            'paymentStatuses' => $this->paymentStatuses(),
            'programsWithClassType' => $this->programsWithClassType(),
        ];
    }

    public function tutors()
    {
        $tutors = Tutor::with('program')
            ->withCount('classSchedules')
            ->orderBy('name')
            ->paginate(12);

        return view('admin.tutors.index', [
            'title' => 'Kelola Tutor',
            'tutors' => $tutors,
        ]);
    }

    public function createTutor()
    {
        return view('admin.tutors.create', [
            'title' => 'Tambah Tutor',
            ...$this->tutorFormData(),
        ]);
    }

    public function storeTutor(Request $request)
    {
        Tutor::create($this->validateTutor($request));

        return redirect()
            ->route('admin.tutors.index')
            ->with('success', 'Tutor berhasil ditambahkan.');
    }

    public function editTutor(Tutor $tutor)
    {
        return view('admin.tutors.edit', [
            'title' => 'Edit Tutor',
            'tutor' => $tutor,
            ...$this->tutorFormData(),
        ]);
    }

    public function updateTutor(Request $request, Tutor $tutor)
    {
        $tutor->update($this->validateTutor($request));

        return redirect()
            ->route('admin.tutors.index')
            ->with('success', 'Tutor berhasil diperbarui.');
    }

    public function destroyTutor(Tutor $tutor)
    {
        if ($tutor->classSchedules()->exists()) {
            return redirect()
                ->route('admin.tutors.index')
                ->withErrors(['tutor' => 'Tutor tidak bisa dihapus karena sudah dipakai pada jadwal siswa.']);
        }

        $tutor->delete();

        return redirect()
            ->route('admin.tutors.index')
            ->with('success', 'Tutor berhasil dihapus.');
    }

    public function payments(Request $request)
    {
        $programLabels = Program::pluck('name', 'id')
            ->mapWithKeys(fn ($name, $id) => [(string) $id => $name])
            ->all();

        $selectedStatus = $request->query('status');
        $validStatuses = array_keys($this->paymentStatuses());

        $users = User::query()
            ->whereNotNull('program')
            ->when(in_array($selectedStatus, $validStatuses, true), function ($query) use ($selectedStatus) {
                $query->where('payment_status', $selectedStatus);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.payments.index', [
            'title' => 'Verifikasi Pembayaran',
            'users' => $users,
            'programLabels' => $programLabels,
            'paymentStatuses' => $this->paymentStatuses(),
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function updatePayment(Request $request, User $user)
    {
        $validated = $request->validate([
            'payment_status' => ['required', Rule::in(['diterima', 'ditolak', 'menunggu_verifikasi'])],
        ]);

        if ($user->payment_status === 'diterima' && $validated['payment_status'] !== 'diterima') {
            return back()->withErrors(['payment' => 'Pembayaran yang sudah diterima tidak bisa diubah dari halaman verifikasi.']);
        }

        if (in_array($validated['payment_status'], ['diterima', 'ditolak'], true) && !$user->payment_proof_path) {
            return back()->withErrors(['payment' => 'Bukti pembayaran belum diupload siswa.']);
        }

        $user->update($validated);

        $latestEnrollment = ProgramEnrollment::query()
            ->where('user_id', $user->id)
            ->when($user->program, fn ($query) => $query->where('program_id', (int) $user->program))
            ->where('status', 'pending')
            ->latest('end_date')
            ->latest()
            ->first();

        if ($latestEnrollment) {
            $latestEnrollment->update([
                'status' => match ($validated['payment_status']) {
                    'diterima' => 'active',
                    'ditolak' => 'rejected',
                    default => 'pending',
                },
            ]);
        }

        if ($validated['payment_status'] === 'diterima') {
            $preference = SchedulePreference::with('scheduleTemplate')
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->oldest('priority')
                ->first();

            if ($preference?->scheduleTemplate && !$preference->scheduleTemplate->hasSeatForUser($user->id)) {
                return back()->withErrors(['payment' => 'Pilihan jadwal siswa sudah penuh. Minta siswa memilih jadwal belajar lain terlebih dahulu.']);
            }

            $this->assignChosenScheduleAfterPayment($user);
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function placementResults()
    {
        $attempts = PlacementTestAttempt::with('user')
            ->latest()
            ->paginate(12);

        return view('admin.placement.results', [
            'title' => 'Hasil Placement Test',
            'attempts' => $attempts,
        ]);
    }

    public function resetPlacementTest(User $user)
    {
        $deletedAttempts = PlacementTestAttempt::where('user_id', $user->id)->delete();

        return redirect()
            ->route('admin.placement.results')
            ->with(
                'success',
                $deletedAttempts > 0
                    ? "Placement test {$user->name} sudah dibuka ulang."
                    : "{$user->name} belum memiliki hasil placement test."
            );
    }

    public function schedules(Request $request)
    {
        $weekStart = \Illuminate\Support\Carbon::parse($request->query('week', now()))
            ->startOfWeek();
        $dayNames = ['Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu'];
        $weekDays = collect(range(0, 6))->map(function (int $dayOffset) use ($weekStart, $dayNames) {
            $date = $weekStart->copy()->addDays($dayOffset);

            return [
                'date' => $date,
                'day' => $date->format('D'),
                'name' => $dayNames[$date->format('D')] ?? $date->format('D'),
                'date_label' => $date->format('d M'),
                'is_today' => $date->isToday(),
            ];
        });

        $scheduleRows = ClassSchedule::with(['program', 'student', 'tutor', 'classRoom', 'scheduleTemplate'])
            ->whereBetween('class_date', [$weekStart->toDateString(), $weekStart->copy()->addDays(6)->toDateString()])
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get();

        $schedules = $scheduleRows
            ->groupBy(function (ClassSchedule $schedule) {
                return collect([
                    $schedule->schedule_template_id ?: 'manual',
                    $schedule->program_id,
                    $schedule->class_type ?: '-',
                    $schedule->class_date?->toDateString(),
                    $schedule->start_time?->format('H:i'),
                    $schedule->end_time?->format('H:i'),
                    $schedule->class_room_id ?: $schedule->room ?: '-',
                ])->join('|');
            })
            ->map(function ($groupedRows, string $groupKey) {
                /** @var \App\Models\ClassSchedule $schedule */
                $schedule = $groupedRows->first();
                $students = $groupedRows
                    ->pluck('student')
                    ->filter()
                    ->unique('id')
                    ->sortBy('name')
                    ->values();

                $schedule->students = $students;
                $schedule->student_count = $students->count();
                $schedule->capacity = (int) ($schedule->max_students ?: $schedule->scheduleTemplate?->max_students ?: max(1, $students->count()));
                $schedule->group_rows = $groupedRows->values();
                $schedule->group_key = 'schedule-group-' . Str::slug(substr(md5($groupKey), 0, 12));

                return $schedule;
            })
            ->sortBy([
                fn (ClassSchedule $schedule) => $schedule->class_date?->timestamp ?? 0,
                fn (ClassSchedule $schedule) => $schedule->start_time?->format('H:i') ?? '',
                fn (ClassSchedule $schedule) => $schedule->program?->name ?? '',
            ])
            ->values();

        return view('admin.schedules.index', [
            'title' => 'Jadwal Belajar Siswa',
            'schedules' => $schedules,
            'totalStudents' => $schedules->sum(fn (ClassSchedule $schedule) => (int) $schedule->student_count),
            'weekDays' => $weekDays,
            'weekStart' => $weekStart,
            'previousWeek' => $weekStart->copy()->subWeek()->toDateString(),
            'nextWeek' => $weekStart->copy()->addWeek()->toDateString(),
        ]);
    }

    public function scheduleTemplates(Request $request)
    {
        $selectedStatus = $request->query('status', 'active');
        $selectedStatus = in_array($selectedStatus, ['active', 'inactive', 'all'], true) ? $selectedStatus : 'active';

        $templates = ScheduleTemplate::with(['program', 'tutor', 'classRoom', 'preferences'])
            ->withCount([
                'classSchedules',
            ])
            ->when($selectedStatus === 'active', fn ($query) => $query->where('is_active', true))
            ->when($selectedStatus === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('program_id')
            ->orderBy('start_time')
            ->paginate(12)
            ->withQueryString();

        return view('admin.schedule-templates.index', [
            'title' => 'Batch & Pilihan Jadwal',
            'templates' => $templates,
            'dayLabels' => $this->dayLabels(),
            'selectedStatus' => $selectedStatus,
            'templateStats' => [
                'active' => ScheduleTemplate::where('is_active', true)->count(),
                'inactive' => ScheduleTemplate::where('is_active', false)->count(),
                'all' => ScheduleTemplate::count(),
            ],
        ]);
    }

    public function classRooms()
    {
        $rooms = ClassRoom::query()
            ->withCount(['scheduleTemplates', 'classSchedules'])
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(12);

        return view('admin.class-rooms.index', [
            'title' => 'Ruang Kelas',
            'rooms' => $rooms,
            'roomCategories' => $this->roomCategories(),
        ]);
    }

    public function createClassRoom()
    {
        return view('admin.class-rooms.create', [
            'title' => 'Tambah Ruang Kelas',
            'room' => null,
            'roomCategories' => $this->roomCategories(),
        ]);
    }

    public function showClassRoom(ClassRoom $room)
    {
        $room->load([
            'scheduleTemplates' => function ($query) {
                $query
                    ->with(['program', 'tutor', 'preferences.user'])
                    ->orderBy('program_id')
                    ->orderBy('start_time');
            },
            'classSchedules' => function ($query) {
                $query
                    ->with(['program', 'student', 'tutor'])
                    ->whereDate('class_date', '>=', now()->toDateString())
                    ->orderBy('class_date')
                    ->orderBy('start_time');
            },
        ]);

        return view('admin.class-rooms.show', [
            'title' => 'Isi Ruang Kelas',
            'room' => $room,
            'dayLabels' => $this->dayLabels(),
        ]);
    }

    public function storeClassRoom(Request $request)
    {
        ClassRoom::create($this->validateClassRoom($request));

        return redirect()
            ->route('admin.class-rooms.index')
            ->with('success', 'Ruang kelas berhasil ditambahkan.');
    }

    public function editClassRoom(ClassRoom $room)
    {
        return view('admin.class-rooms.edit', [
            'title' => 'Edit Ruang Kelas',
            'room' => $room,
            'roomCategories' => $this->roomCategories(),
        ]);
    }

    public function updateClassRoom(Request $request, ClassRoom $room)
    {
        $room->update($this->validateClassRoom($request, $room));

        return redirect()
            ->route('admin.class-rooms.index')
            ->with('success', 'Ruang kelas berhasil diperbarui.');
    }

    public function destroyClassRoom(ClassRoom $room)
    {
        if ($room->scheduleTemplates()->exists() || $room->classSchedules()->exists()) {
            return redirect()
                ->route('admin.class-rooms.index')
                ->withErrors(['room' => 'Ruang kelas tidak bisa dihapus karena sudah dipakai pada pilihan jadwal atau jadwal siswa.']);
        }

        $room->delete();

        return redirect()
            ->route('admin.class-rooms.index')
            ->with('success', 'Ruang kelas berhasil dihapus.');
    }

    public function createScheduleTemplate()
    {
        return view('admin.schedule-templates.create', [
            'title' => 'Tambah Batch Jadwal',
            ...$this->scheduleTemplateFormData(),
        ]);
    }

    public function storeScheduleTemplate(Request $request)
    {
        ScheduleTemplate::create($this->validateScheduleTemplate($request));

        return redirect()
            ->route('admin.schedule-templates.index')
            ->with('success', 'Pilihan jadwal berhasil ditambahkan.');
    }

    public function editScheduleTemplate(ScheduleTemplate $scheduleTemplate)
    {
        return view('admin.schedule-templates.edit', [
            'title' => 'Edit Batch Jadwal',
            'scheduleTemplate' => $scheduleTemplate,
            ...$this->scheduleTemplateFormData(),
        ]);
    }

    public function updateScheduleTemplate(Request $request, ScheduleTemplate $scheduleTemplate)
    {
        $scheduleTemplate->update($this->validateScheduleTemplate($request, $scheduleTemplate));

        return redirect()
            ->route('admin.schedule-templates.index')
            ->with('success', 'Pilihan jadwal berhasil diperbarui.');
    }

    public function destroyScheduleTemplate(ScheduleTemplate $scheduleTemplate)
    {
        if ($scheduleTemplate->preferences()->where('status', 'pending')->exists() || $scheduleTemplate->classSchedules()->exists()) {
            return redirect()
                ->route('admin.schedule-templates.index')
                ->withErrors(['schedule_template' => 'Pilihan jadwal tidak bisa dihapus karena sudah dipilih siswa atau sudah dipakai membuat jadwal siswa.']);
        }

        $scheduleTemplate->delete();

        return redirect()
            ->route('admin.schedule-templates.index')
            ->with('success', 'Pilihan jadwal berhasil dihapus.');
    }

    public function createSchedule()
    {
        return view('admin.schedules.create', [
            'title' => 'Tambah Jadwal Belajar',
            ...$this->scheduleFormData(),
        ]);
    }

    public function storeSchedule(Request $request)
    {
        $validated = $this->validateSchedule($request);

        ClassSchedule::create($this->schedulePayload($validated));

        return redirect()
            ->route('admin.schedules.index', ['week' => \Illuminate\Support\Carbon::parse($validated['class_date'])->startOfWeek()->toDateString()])
            ->with('success', 'Jadwal siswa berhasil ditambahkan.');
    }

    public function editSchedule(ClassSchedule $schedule)
    {
        return view('admin.schedules.edit', [
            'title' => 'Edit Jadwal Belajar',
            'schedule' => $schedule->load(['student.latestPlacementAttempt', 'tutor']),
            ...$this->scheduleFormData(),
        ]);
    }

    public function updateSchedule(Request $request, ClassSchedule $schedule)
    {
        $validated = $this->validateSchedule($request);

        $schedule->update($this->schedulePayload($validated));

        return redirect()
            ->route('admin.schedules.index', ['week' => \Illuminate\Support\Carbon::parse($validated['class_date'])->startOfWeek()->toDateString()])
            ->with('success', 'Jadwal siswa berhasil diperbarui.');
    }

    public function destroySchedule(ClassSchedule $schedule)
    {
        $week = $schedule->class_date?->copy()->startOfWeek()->toDateString() ?? now()->startOfWeek()->toDateString();

        $schedule->delete();

        return redirect()
            ->route('admin.schedules.index', ['week' => $week])
            ->with('success', 'Jadwal siswa berhasil dihapus.');
    }

    private function validateSchedule(Request $request): array
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')],
            'tutor_id' => ['nullable', Rule::exists('tutors', 'id')],
            'program_id' => ['required', Rule::exists('programs', 'id')],
            'class_type' => ['nullable', Rule::in(array_keys($this->classTypes()))],
            'class_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'class_room_id' => ['nullable', Rule::exists('class_rooms', 'id')],
            'room' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $student = User::with('latestPlacementAttempt')->findOrFail($validated['user_id']);
        $classType = in_array($program->name, $this->programsWithClassType(), true)
            ? ($validated['class_type'] ?? 'Reguler')
            : null;

        if ((string) $student->program !== (string) $program->id) {
            throw ValidationException::withMessages([
                'user_id' => 'Siswa ini tidak terdaftar pada program yang dipilih.',
            ]);
        }

        if ($classType && $student->class_type !== $classType) {
            throw ValidationException::withMessages([
                'class_type' => 'Jenis kelas tidak sesuai dengan jenis kelas yang diambil siswa.',
            ]);
        }

        if (!empty($validated['tutor_id'])) {
            $tutor = Tutor::findOrFail($validated['tutor_id']);
            $studentLevel = $student->latestPlacementAttempt?->level;

            if (!$tutor->is_active) {
                throw ValidationException::withMessages([
                    'tutor_id' => 'Tutor yang dipilih sedang nonaktif.',
                ]);
            }

            if ($tutor->program_id && (string) $tutor->program_id !== (string) $program->id) {
                throw ValidationException::withMessages([
                    'tutor_id' => 'Tutor tidak sesuai dengan program yang dipilih.',
                ]);
            }

            if ($tutor->level && $studentLevel && $tutor->level !== $studentLevel) {
                throw ValidationException::withMessages([
                    'tutor_id' => 'Tutor tidak sesuai dengan level placement test siswa.',
                ]);
            }
        }

        $classRoom = $this->validatedClassRoom($validated['class_room_id'] ?? null, $program);

        $validated['class_type'] = $classType;
        $validated['class_room_id'] = $classRoom?->id;
        $validated['room'] = $classRoom?->name ?? ($validated['room'] ?? null);
        $validated['max_students'] = $this->maxStudentsForClassType($classType);

        return $validated;
    }

    private function schedulePayload(array $validated): array
    {
        return [
            'user_id' => $validated['user_id'],
            'tutor_id' => $validated['tutor_id'] ?? null,
            'schedule_template_id' => $validated['schedule_template_id'] ?? null,
            'class_room_id' => $validated['class_room_id'] ?? null,
            'program_id' => $validated['program_id'],
            'class_type' => $validated['class_type'],
            'class_date' => $validated['class_date'],
            'session_name' => 'Kelas Manual',
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'room' => $validated['room'] ?? null,
            'max_students' => $validated['max_students'] ?? $this->maxStudentsForClassType($validated['class_type'] ?? null),
            'notes' => $validated['notes'] ?? null,
        ];
    }

    private function validateScheduleTemplate(Request $request, ?ScheduleTemplate $currentTemplate = null): array
    {
        $validated = $request->validate([
            'program_id' => ['required', Rule::exists('programs', 'id')],
            'tutor_id' => ['nullable', Rule::exists('tutors', 'id')],
            'class_type' => ['nullable', Rule::in(array_keys($this->classTypes()))],
            'level' => ['nullable', Rule::in(array_keys($this->placementLevels()))],
            'batch_name' => ['nullable', 'string', 'max:255'],
            'registration_start_date' => ['nullable', 'date'],
            'registration_end_date' => ['nullable', 'date', 'after_or_equal:registration_start_date'],
            'learning_start_date' => ['nullable', 'date', 'after_or_equal:registration_start_date'],
            'learning_end_date' => ['nullable', 'date', 'after_or_equal:learning_start_date'],
            'days' => ['required', 'array', 'min:1', 'max:2'],
            'days.*' => ['required', 'integer', Rule::in(array_keys($this->dayLabels()))],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'class_room_id' => ['required', Rule::exists('class_rooms', 'id')],
            'max_students' => ['nullable', 'integer', 'min:1', 'max:8'],
            'room' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $usesClassType = $this->programUsesClassType($program->name);

        $validated['days'] = collect($validated['days'])
            ->map(fn ($day) => (int) $day)
            ->unique()
            ->values()
            ->all();

        if (count($validated['days']) < 1 || count($validated['days']) > 2) {
            throw ValidationException::withMessages([
                'days' => 'Pilih 1 hari untuk sesi offline atau 2 hari untuk kelas mingguan.',
            ]);
        }

        $validated['class_type'] = $usesClassType ? ($validated['class_type'] ?? 'Reguler') : null;
        $validated['batch_name'] = $validated['batch_name'] ?? null;
        $classRoom = $this->validatedClassRoom($validated['class_room_id'] ?? null, $program);
        $validated['tutor_id'] = $validated['tutor_id'] ?? null;
        $validated['level'] = $validated['level'] ?? null;
        $validated['class_room_id'] = $classRoom?->id;
        $validated['room'] = $classRoom?->name;
        $validated['max_students'] = $validated['class_type'] === 'Private'
            ? 1
            : min((int) ($validated['max_students'] ?? 8), (int) $classRoom->capacity, 8);
        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_active']) {
            $currentLearningStart = $validated['learning_start_date'] ?? null;
            $currentLearningEnd = $validated['learning_end_date'] ?? null;
            $periodsOverlap = function (ScheduleTemplate $template) use ($currentLearningStart, $currentLearningEnd): bool {
                if (!$currentLearningStart || !$currentLearningEnd || !$template->learning_start_date || !$template->learning_end_date) {
                    return true;
                }

                return $template->learning_start_date->toDateString() <= $currentLearningEnd
                    && $template->learning_end_date->toDateString() >= $currentLearningStart;
            };

            $conflictingTemplate = ScheduleTemplate::query()
                ->where('is_active', true)
                ->where('class_room_id', $validated['class_room_id'])
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->when($currentTemplate, fn ($query) => $query->whereKeyNot($currentTemplate->id))
                ->get()
                ->first(function (ScheduleTemplate $template) use ($validated, $periodsOverlap) {
                    $templateDays = collect($template->days ?? [])->map(fn ($day) => (int) $day)->all();

                    return count(array_intersect($templateDays, $validated['days'])) > 0
                        && $periodsOverlap($template);
                });

            if ($conflictingTemplate) {
                throw ValidationException::withMessages([
                    'class_room_id' => 'Ruang ini sudah dipakai pada hari dan jam yang sama. Pilih ruang atau jam lain.',
                ]);
            }
        }

        return $validated;
    }

    private function scheduleTemplateFormData(): array
    {
        return [
            'programs' => Program::where('status', 'active')->orderBy('name')->get(),
            'tutors' => Tutor::query()->where('is_active', true)->orderBy('name')->get(),
            'classRooms' => ClassRoom::query()->where('is_active', true)->orderBy('category')->orderBy('name')->get(),
            'classTypes' => $this->classTypes(),
            'levels' => $this->placementLevels(),
            'dayLabels' => $this->dayLabels(),
            'programsWithClassType' => $this->programsWithClassType(),
        ];
    }

    private function datesForTemplateDays(\Illuminate\Support\Carbon $startDate, \Illuminate\Support\Carbon $endDate, array $days)
    {
        $days = collect($days)->map(fn ($day) => (int) $day)->all();
        $dates = collect();
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate)) {
            if (in_array($cursor->isoWeekday(), $days, true)) {
                $dates->push($cursor->copy());
            }

            $cursor->addDay();
        }

        return $dates;
    }

    private function createMonthlySchedulesFromTemplate(User $user, ScheduleTemplate $template, string $startDate, ?string $endDate = null): int
    {
        $startDate = \Illuminate\Support\Carbon::parse($startDate)->startOfDay();
        $endDate = $endDate
            ? \Illuminate\Support\Carbon::parse($endDate)->startOfDay()
            : $startDate->copy()->addMonth()->subDay();
        $dates = $this->datesForTemplateDays($startDate, $endDate, $template->days ?? []);

        foreach ($dates as $date) {
            ClassSchedule::firstOrCreate([
                'user_id' => $user->id,
                'schedule_template_id' => $template->id,
                'class_date' => $date->toDateString(),
                'start_time' => $template->start_time->format('H:i'),
            ], [
                'tutor_id' => $template->tutor_id,
                'class_room_id' => $template->class_room_id,
                'program_id' => $template->program_id,
                'class_type' => $template->class_type,
                'session_name' => $template->batch_name ?: 'Jadwal Belajar',
                'end_time' => $template->end_time->format('H:i'),
                'room' => $template->room,
                'max_students' => $template->max_students,
                'notes' => $template->notes,
            ]);
        }

        return $dates->count();
    }

    private function assignChosenScheduleAfterPayment(User $user): void
    {
        $preference = SchedulePreference::with('scheduleTemplate')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->oldest('priority')
            ->first();

        if (!$preference || !$preference->scheduleTemplate) {
            return;
        }

        $learningStartDate = $preference->scheduleTemplate->learning_start_date?->toDateString() ?? now()->toDateString();
        $learningEndDate = $preference->scheduleTemplate->learning_end_date?->toDateString();

        $this->createMonthlySchedulesFromTemplate($user, $preference->scheduleTemplate, $learningStartDate, $learningEndDate);

        SchedulePreference::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        $preference->update(['status' => 'assigned']);
    }

    private function scheduleFormData(): array
    {
        return [
            'programs' => Program::where('status', 'active')->orderBy('name')->get(),
            'students' => User::query()
                ->with('latestPlacementAttempt')
                ->whereNotNull('program')
                ->orderBy('name')
                ->get(),
            'tutors' => Tutor::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'classRooms' => ClassRoom::query()->where('is_active', true)->orderBy('category')->orderBy('name')->get(),
            'classTypes' => $this->classTypes(),
            'programsWithClassType' => $this->programsWithClassType(),
        ];
    }

    private function validateClassRoom(Request $request, ?ClassRoom $room = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('class_rooms', 'name')->ignore($room)],
            'category' => ['required', Rule::in(array_keys($this->roomCategories()))],
            'capacity' => ['required', 'integer', 'min:1', 'max:8'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    private function validatedClassRoom(?int $classRoomId, Program $program): ?ClassRoom
    {
        if (!$classRoomId) {
            return null;
        }

        $classRoom = ClassRoom::findOrFail($classRoomId);

        if (!$classRoom->is_active) {
            throw ValidationException::withMessages([
                'class_room_id' => 'Ruang kelas yang dipilih sedang nonaktif.',
            ]);
        }

        $expectedCategory = $this->roomCategoryForProgram($program);

        if ($classRoom->category !== $expectedCategory) {
            throw ValidationException::withMessages([
                'class_room_id' => "Ruang kelas harus kategori {$expectedCategory} untuk program ini.",
            ]);
        }

        return $classRoom;
    }

    private function roomCategories(): array
    {
        return [
            'English' => 'English',
            'Bimbel' => 'Bimbel',
        ];
    }

    private function roomCategoryForProgram(Program $program): string
    {
        return Str::lower($program->category ?? '') === 'bimbel'
            || Str::startsWith(Str::lower($program->name), 'bimbel')
                ? 'Bimbel'
                : 'English';
    }

    private function maxStudentsForClassType(?string $classType): int
    {
        return $classType === 'Private' ? 1 : 8;
    }

    private function validateTutor(Request $request): array
    {
        $validated = $request->validate([
            'program_id' => ['nullable', Rule::exists('programs', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'level' => ['nullable', Rule::in(array_keys($this->placementLevels()))],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['program_id'] = $validated['program_id'] ?? null;
        $validated['level'] = $validated['level'] ?? null;
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    private function tutorFormData(): array
    {
        return [
            'programs' => Program::where('status', 'active')->orderBy('name')->get(),
            'levels' => $this->placementLevels(),
        ];
    }

    public function placementQuestions()
    {
        $questions = PlacementQuestion::orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        return view('admin.placement.questions.index', [
            'title' => 'Kelola Soal Placement Test',
            'questions' => $questions,
        ]);
    }

    public function createPlacementQuestion()
    {
        return view('admin.placement.questions.create', [
            'title' => 'Tambah Soal Placement Test',
            'levels' => $this->placementLevels(),
        ]);
    }

    public function storePlacementQuestion(Request $request)
    {
        PlacementQuestion::create($this->validatePlacementQuestion($request));

        return redirect()
            ->route('admin.placement.questions.index')
            ->with('success', 'Soal placement test berhasil ditambahkan.');
    }

    public function editPlacementQuestion(PlacementQuestion $question)
    {
        return view('admin.placement.questions.edit', [
            'title' => 'Edit Soal Placement Test',
            'question' => $question,
            'levels' => $this->placementLevels(),
        ]);
    }

    public function updatePlacementQuestion(Request $request, PlacementQuestion $question)
    {
        $question->update($this->validatePlacementQuestion($request));

        return redirect()
            ->route('admin.placement.questions.index')
            ->with('success', 'Soal placement test berhasil diperbarui.');
    }

    public function destroyPlacementQuestion(PlacementQuestion $question)
    {
        $question->delete();

        return redirect()
            ->route('admin.placement.questions.index')
            ->with('success', 'Soal placement test berhasil dihapus.');
    }

    public function createProgram()
    {
        return view('admin.programs.create', [
            'title' => 'Tambah Program',
            'statuses' => $this->programStatuses(),
            'categories' => $this->programCategories(),
            'categoryLabels' => $this->programCategoryLabels(),
        ]);
    }

    public function storeProgram(Request $request)
    {
        Program::create($this->validateProgram($request));

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program berhasil ditambahkan.');
    }

    public function editProgram(Program $program)
    {
        return view('admin.programs.edit', [
            'title' => 'Edit Program',
            'program' => $program,
            'statuses' => $this->programStatuses(),
            'categories' => $this->programCategories(),
            'categoryLabels' => $this->programCategoryLabels(),
        ]);
    }

    public function updateProgram(Request $request, Program $program)
    {
        $program->update($this->validateProgram($request));

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program berhasil diperbarui.');
    }

    public function destroyProgram(Program $program)
    {
        $program->delete();

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program berhasil dihapus.');
    }

    public function programCategoriesIndex()
    {
        $categories = ProgramCategory::query()
            ->with(['parent', 'children', 'programs'])
            ->withCount(['children', 'programs'])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.program-categories.index', [
            'title' => 'Kelompok Program',
            'categories' => $categories,
        ]);
    }

    public function createProgramCategory()
    {
        return view('admin.program-categories.create', [
            'title' => 'Tambah Kelompok Program',
            'parentCategories' => $this->parentProgramCategories(),
        ]);
    }

    public function storeProgramCategory(Request $request)
    {
        $validated = $this->validateProgramCategory($request);
        $validated['parent_id'] = null;
        $validated['slug'] = $this->programCategorySlug($validated['name'], null);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? $this->nextProgramCategorySortOrder());

        ProgramCategory::create($validated);

        return redirect()
            ->route('admin.program-categories.index')
            ->with('success', 'Kelompok program berhasil ditambahkan.');
    }

    public function editProgramCategory(ProgramCategory $category)
    {
        return view('admin.program-categories.edit', [
            'title' => 'Edit Kelompok Program',
            'category' => $category,
            'parentCategories' => $this->parentProgramCategories($category),
        ]);
    }

    public function updateProgramCategory(Request $request, ProgramCategory $category)
    {
        $validated = $this->validateProgramCategory($request, $category);
        $validated['parent_id'] = null;
        $validated['slug'] = $this->programCategorySlug($validated['name'], null);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? $category->sort_order ?? $this->nextProgramCategorySortOrder());

        $category->update($validated);

        return redirect()
            ->route('admin.program-categories.index')
            ->with('success', 'Kelompok program berhasil diperbarui.');
    }

    public function destroyProgramCategory(ProgramCategory $category)
    {
        if ($category->children()->exists() || $category->programs()->exists()) {
            return redirect()
                ->route('admin.program-categories.index')
                ->withErrors(['category' => 'Kelompok tidak bisa dihapus karena masih dipakai oleh program.']);
        }

        $category->delete();

        return redirect()
            ->route('admin.program-categories.index')
            ->with('success', 'Kelompok program berhasil dihapus.');
    }

    private function validateProgram(Request $request): array
    {
        return $request->validate([
            'program_category_id' => ['nullable', Rule::exists('program_categories', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'price' => ['nullable', 'integer', 'min:0'],
            'private_price' => ['nullable', 'integer', 'min:0'],
            'conversation_price' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(array_keys($this->programStatuses()))],
        ]);
    }

    private function validateProgramCategory(Request $request, ?ProgramCategory $category = null): array
    {
        $slug = $this->programCategorySlug($request->input('name', ''), null);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slugExists = ProgramCategory::query()
            ->where('slug', $slug)
            ->when($category, fn ($query) => $query->whereKeyNot($category->id))
            ->exists();

        if ($slugExists) {
            throw ValidationException::withMessages([
                'name' => 'Kelompok program dengan nama ini sudah ada.',
            ]);
        }

        return $validated;
    }

    private function programStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
            'completed' => 'Selesai',
        ];
    }

    private function programCategories(): array
    {
        return ProgramCategory::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->all();
    }

    private function parentProgramCategories(?ProgramCategory $currentCategory = null): array
    {
        return ProgramCategory::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->when($currentCategory, fn ($query) => $query->whereKeyNot($currentCategory->id))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    private function programCategorySlug(string $name, ?int $parentId): string
    {
        return Str::slug(($parentId ? $parentId . '-' : '') . $name);
    }

    private function nextProgramCategorySortOrder(): int
    {
        return ((int) ProgramCategory::whereNull('parent_id')->max('sort_order')) + 10;
    }

    private function programCategoryLabels(): array
    {
        return [
            'Bahasa Inggris' => 'Bahasa Inggris',
            'Test Preparation' => 'Test Preparation',
            'BIMBEL' => 'BIMBEL',
        ];
    }

    private function paymentStatuses(): array
    {
        return [
            'belum_upload' => 'Belum Upload',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
        ];
    }

    private function classTypes(): array
    {
        return [
            'Reguler' => 'Reguler',
            'Private' => 'Private',
            'Conversation' => 'Conversation',
        ];
    }

    private function programsWithClassType(): array
    {
        return [
            'English for Kids',
            'English for Teens',
            'English for Adult',
        ];
    }

    private function programUsesClassType(string $programName): bool
    {
        return collect($this->programsWithClassType())
            ->contains(fn (string $name) => Str::lower($name) === Str::lower($programName));
    }

    private function placementLevels(): array
    {
        return [
            'Beginner' => 'Beginner',
            'Elementary' => 'Elementary',
            'Pre-Intermediate' => 'Pre-Intermediate',
            'Intermediate' => 'Intermediate',
            'Upper-Intermediate' => 'Upper-Intermediate',
            'Advanced' => 'Advanced',
        ];
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

    private function validatePlacementQuestion(Request $request): array
    {
        $validated = $request->validate([
            'section' => ['required', 'string', 'max:255'],
            'level' => ['required', Rule::in(array_keys($this->placementLevels()))],
            'question_text' => ['required', 'string'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', 'integer', 'between:0,3'],
            'explanation' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        return $validated;
    }

    private function classSessions(): array
    {
        return [
            'session_1500' => [
                'name' => 'Sesi Sore 1',
                'start_time' => '15:00',
                'end_time' => '16:00',
            ],
            'session_1615' => [
                'name' => 'Sesi Sore 2',
                'start_time' => '16:15',
                'end_time' => '17:15',
            ],
            'session_1900' => [
                'name' => 'Sesi Malam',
                'start_time' => '19:00',
                'end_time' => '20:00',
            ],
        ];
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
}
