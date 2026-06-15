<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ClassSchedule;
use App\Models\PlacementQuestion;
use App\Models\PlacementTestAttempt;
use App\Models\ProgramCategory;
use App\Models\ProgramEnrollment;
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
            'recentRegistrants' => $registeredUsers->take(8),
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
        });

        return view('admin.programs.index', [
            'title' => 'Kelola Program',
            'programs' => $programs,
        ]);
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

        $schedules = ClassSchedule::with('program')
            ->whereBetween('class_date', [$weekStart->toDateString(), $weekStart->copy()->addDays(6)->toDateString()])
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get()
            ->map(function (ClassSchedule $schedule) {
                $schedule->students = User::query()
                    ->where('program', (string) $schedule->program_id)
                    ->when($schedule->class_type, function ($query) use ($schedule) {
                        $query->where('class_type', $schedule->class_type);
                    })
                    ->where('payment_status', 'diterima')
                    ->orderBy('name')
                    ->get();

                return $schedule;
            });

        return view('admin.schedules.index', [
            'title' => 'Jadwal Kelas',
            'schedules' => $schedules,
            'totalStudents' => $schedules->sum(fn (ClassSchedule $schedule) => $schedule->students->count()),
            'weekDays' => $weekDays,
            'weekStart' => $weekStart,
            'previousWeek' => $weekStart->copy()->subWeek()->toDateString(),
            'nextWeek' => $weekStart->copy()->addWeek()->toDateString(),
        ]);
    }

    public function createSchedule()
    {
        return view('admin.schedules.create', [
            'title' => 'Tambah Jadwal Kelas',
            'programs' => Program::where('status', 'active')->orderBy('name')->get(),
            'classTypes' => $this->classTypes(),
            'programsWithClassType' => $this->programsWithClassType(),
        ]);
    }

    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'program_id' => ['required', Rule::exists('programs', 'id')],
            'class_type' => ['nullable', Rule::in(array_keys($this->classTypes()))],
            'class_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $classType = in_array($program->name, $this->programsWithClassType(), true)
            ? ($validated['class_type'] ?? 'Reguler')
            : null;

        ClassSchedule::create([
            'program_id' => $validated['program_id'],
            'class_type' => $classType,
            'class_date' => $validated['class_date'],
            'session_name' => 'Kelas Manual',
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'room' => $validated['room'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.schedules.index', ['week' => \Illuminate\Support\Carbon::parse($validated['class_date'])->startOfWeek()->toDateString()])
            ->with('success', 'Jadwal kelas berhasil ditambahkan.');
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
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.program-categories.index', [
            'title' => 'Kategori Tampilan',
            'categories' => $categories,
        ]);
    }

    public function createProgramCategory()
    {
        return view('admin.program-categories.create', [
            'title' => 'Tambah Kategori Tampilan',
            'parentCategories' => $this->parentProgramCategories(),
        ]);
    }

    public function storeProgramCategory(Request $request)
    {
        $validated = $this->validateProgramCategory($request);
        $validated['slug'] = $this->programCategorySlug($validated['name'], $validated['parent_id'] ?? null);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        ProgramCategory::create($validated);

        return redirect()
            ->route('admin.program-categories.index')
            ->with('success', 'Kategori tampilan berhasil ditambahkan.');
    }

    public function editProgramCategory(ProgramCategory $category)
    {
        return view('admin.program-categories.edit', [
            'title' => 'Edit Kategori Tampilan',
            'category' => $category,
            'parentCategories' => $this->parentProgramCategories($category),
        ]);
    }

    public function updateProgramCategory(Request $request, ProgramCategory $category)
    {
        $validated = $this->validateProgramCategory($request, $category);
        $validated['slug'] = $this->programCategorySlug($validated['name'], $validated['parent_id'] ?? null);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $category->update($validated);

        return redirect()
            ->route('admin.program-categories.index')
            ->with('success', 'Kategori tampilan berhasil diperbarui.');
    }

    public function destroyProgramCategory(ProgramCategory $category)
    {
        if ($category->children()->exists() || $category->programs()->exists()) {
            return redirect()
                ->route('admin.program-categories.index')
                ->withErrors(['category' => 'Kategori tidak bisa dihapus karena masih memiliki sub-kategori atau program.']);
        }

        $category->delete();

        return redirect()
            ->route('admin.program-categories.index')
            ->with('success', 'Kategori tampilan berhasil dihapus.');
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
        $parentId = $request->input('parent_id') ?: null;
        $slug = $this->programCategorySlug($request->input('name', ''), $parentId);

        $validated = $request->validate([
            'parent_id' => [
                'nullable',
                Rule::exists('program_categories', 'id'),
                Rule::notIn([$category?->id]),
            ],
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
                'name' => 'Kategori tampilan dengan nama dan parent ini sudah ada.',
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
            ->with('parent')
            ->where('is_active', true)
            ->where(function ($query) {
                $query
                    ->whereNull('parent_id')
                    ->orWhereHas('parent', fn ($query) => $query->where('is_active', true));
            })
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (ProgramCategory $category) {
                $label = $category->parent
                    ? "{$category->parent->name} / {$category->name}"
                    : $category->name;

                return [$category->id => $label];
            })
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

    private function programCategoryLabels(): array
    {
        return [
            'Bahasa Inggris' => 'Bahasa Inggris',
            'Private' => 'Private',
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
