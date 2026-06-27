<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\PlacementQuestion;
use App\Models\PlacementTestAttempt;
use App\Models\Program;
use App\Models\ProgramCategory;
use App\Models\ProgramEnrollment;
use App\Models\SchedulePreference;
use App\Models\ScheduleTemplate;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CellRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(now()->setDate(2026, 6, 25)->setTime(9, 0));
    }

    public function test_home_and_quota_pages_can_be_opened(): void
    {
        $this->createProgram('English for Kids', 'Bahasa Inggris');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('CELL English Course');

        $this->get(route('programs.quota'))
            ->assertOk()
            ->assertSee('English for Kids');
    }

    public function test_authenticated_user_can_register_program_and_choose_schedule(): void
    {
        $user = User::factory()->create();
        $program = $this->createProgram('English for Kids', 'Bahasa Inggris');
        $template = $this->createScheduleTemplate($program, 'Reguler');

        $this->actingAs($user)
            ->post(route('programs.store'), [
                'whatsapp' => '081292538501',
                'address' => 'Jl. CELL English',
                'program' => $program->id,
                'class_type' => 'Reguler',
                'schedule_template_id' => $template->id,
            ])
            ->assertRedirect(route('programs.payment'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'program' => (string) $program->id,
            'class_type' => 'Reguler',
            'payment_status' => 'belum_upload',
        ]);

        $this->assertDatabaseHas('program_enrollments', [
            'user_id' => $user->id,
            'program_id' => $program->id,
            'class_type' => 'Reguler',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('schedule_preferences', [
            'user_id' => $user->id,
            'schedule_template_id' => $template->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_change_program_before_uploading_payment_proof(): void
    {
        $user = User::factory()->create();
        $kids = $this->createProgram('English for Kids', 'Bahasa Inggris');
        $teens = $this->createProgram('English for Teens', 'Bahasa Inggris');
        $kidsTemplate = $this->createScheduleTemplate($kids, 'Reguler', 'English Room 1');
        $teensTemplate = $this->createScheduleTemplate($teens, 'Reguler', 'English Room 2');

        $this->actingAs($user)->post(route('programs.store'), [
            'whatsapp' => '081292538501',
            'address' => 'Jl. CELL English',
            'program' => $kids->id,
            'class_type' => 'Reguler',
            'schedule_template_id' => $kidsTemplate->id,
        ]);

        $this->actingAs($user)
            ->post(route('programs.store'), [
                'whatsapp' => '081292538501',
                'address' => 'Jl. CELL English',
                'program' => $teens->id,
                'class_type' => 'Reguler',
                'schedule_template_id' => $teensTemplate->id,
            ])
            ->assertRedirect(route('programs.payment'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'program' => (string) $teens->id,
        ]);

        $this->assertDatabaseMissing('schedule_preferences', [
            'user_id' => $user->id,
            'schedule_template_id' => $kidsTemplate->id,
            'status' => 'pending',
        ]);
    }

    public function test_upload_payment_changes_status_to_waiting_verification(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $program = $this->createProgram('English for Kids', 'Bahasa Inggris');
        $user->update([
            'program' => (string) $program->id,
            'class_type' => 'Reguler',
            'payment_status' => 'belum_upload',
            'registration_expires_at' => now()->addHours(12),
        ]);

        $this->actingAs($user)
            ->post(route('programs.payment.store'), [
                'payment_proof' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('programs.payment.success'));

        $user->refresh();

        $this->assertSame('menunggu_verifikasi', $user->payment_status);
        $this->assertNotNull($user->payment_proof_path);
        Storage::disk('public')->assertExists($user->payment_proof_path);
    }

    public function test_admin_can_accept_payment_and_system_creates_student_schedules(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create([
            'payment_status' => 'menunggu_verifikasi',
            'payment_proof_path' => 'payment-proofs/bukti.png',
        ]);
        $program = $this->createProgram('English for Kids', 'Bahasa Inggris');
        $template = $this->createScheduleTemplate($program, 'Reguler');

        $student->update([
            'program' => (string) $program->id,
            'class_type' => 'Reguler',
        ]);

        ProgramEnrollment::create([
            'user_id' => $student->id,
            'program_id' => $program->id,
            'class_type' => 'Reguler',
            'type' => 'new',
            'enrolled_at' => now(),
            'start_date' => $template->learning_start_date,
            'end_date' => $template->learning_end_date,
            'status' => 'pending',
        ]);

        SchedulePreference::create([
            'user_id' => $student->id,
            'schedule_template_id' => $template->id,
            'priority' => 1,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.payments.update', $student), [
                'payment_status' => 'diterima',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'payment_status' => 'diterima',
        ]);

        $this->assertDatabaseHas('schedule_preferences', [
            'user_id' => $student->id,
            'schedule_template_id' => $template->id,
            'status' => 'assigned',
        ]);

        $this->assertGreaterThan(0, ClassSchedule::where('user_id', $student->id)->count());
    }

    public function test_adult_private_toefl_package_payment_creates_twenty_five_meetings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create([
            'payment_status' => 'menunggu_verifikasi',
            'payment_proof_path' => 'payment-proofs/bukti.png',
        ]);
        $program = $this->createProgram('English for Adult', 'Bahasa Inggris');
        $program->update(['private_price' => 2499000]);
        $template = $this->createScheduleTemplate($program, 'Private', 'English Room 3', 1);
        $template->update([
            'private_package' => 'TOEFL Preparation',
            'learning_end_date' => now()->addMonths(4)->toDateString(),
        ]);

        $student->update([
            'program' => (string) $program->id,
            'class_type' => 'Private',
            'private_package' => 'TOEFL Preparation',
        ]);

        ProgramEnrollment::create([
            'user_id' => $student->id,
            'program_id' => $program->id,
            'class_type' => 'Private',
            'private_package' => 'TOEFL Preparation',
            'type' => 'new',
            'enrolled_at' => now(),
            'start_date' => $template->learning_start_date,
            'end_date' => $template->learning_start_date->copy()->addMonths(3),
            'status' => 'pending',
        ]);

        SchedulePreference::create([
            'user_id' => $student->id,
            'schedule_template_id' => $template->id,
            'priority' => 1,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.payments.update', $student), [
                'payment_status' => 'diterima',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(25, ClassSchedule::where('user_id', $student->id)->count());
        $this->assertDatabaseHas('class_schedules', [
            'user_id' => $student->id,
            'program_id' => $program->id,
            'class_type' => 'Private',
            'private_package' => 'TOEFL Preparation',
            'max_students' => 1,
        ]);
    }

    public function test_english_program_requires_placement_test_and_stores_result(): void
    {
        $user = User::factory()->create(['payment_status' => 'diterima']);
        $program = $this->createProgram('English for Kids', 'Bahasa Inggris');
        $this->createScheduleTemplate($program, 'Reguler');

        $user->update([
            'program' => (string) $program->id,
            'class_type' => 'Reguler',
        ]);

        PlacementQuestion::query()->delete();

        $questionA = $this->createPlacementQuestion(0);
        $questionB = $this->createPlacementQuestion(1);

        $this->actingAs($user)
            ->post(route('placement-test.store'), [
                'started_at' => now()->subMinutes(5)->timestamp,
                'answers' => [
                    $questionA->id => 0,
                    $questionB->id => 0,
                ],
            ])
            ->assertRedirect(route('student.schedule'));

        $this->assertDatabaseHas('placement_test_attempts', [
            'user_id' => $user->id,
            'total_questions' => 2,
            'correct_answers' => 1,
            'score_percentage' => 50,
            'level' => 'Pre-Intermediate',
        ]);
    }

    public function test_toeic_and_bimbel_do_not_require_placement_test(): void
    {
        $toeic = $this->createProgram('TOEIC', 'Test Preparation');
        $bimbel = $this->createProgram('BIMBEL SD', 'BIMBEL');

        foreach ([$toeic, $bimbel] as $program) {
            $user = User::factory()->create([
                'program' => (string) $program->id,
                'payment_status' => 'diterima',
            ]);

            $this->actingAs($user)
                ->get(route('placement-test'))
                ->assertRedirect(route('student.schedule'));

            $this->assertSame(0, PlacementTestAttempt::where('user_id', $user->id)->count());
        }
    }

    public function test_student_cannot_retake_placement_test_without_admin_reset(): void
    {
        $user = User::factory()->create(['payment_status' => 'diterima']);
        $program = $this->createProgram('English for Kids', 'Bahasa Inggris');

        $user->update([
            'program' => (string) $program->id,
            'class_type' => 'Reguler',
        ]);

        PlacementQuestion::query()->delete();
        $question = $this->createPlacementQuestion(0);

        PlacementTestAttempt::create([
            'user_id' => $user->id,
            'total_questions' => 1,
            'correct_answers' => 1,
            'score_percentage' => 100,
            'level' => 'Advanced',
            'recommended_program' => 'Advanced English',
            'answers' => [],
            'duration_seconds' => 60,
        ]);

        $this->actingAs($user)
            ->post(route('placement-test.store'), [
                'started_at' => now()->subMinute()->timestamp,
                'answers' => [$question->id => 0],
            ])
            ->assertRedirect(route('placement-test'));

        $this->assertSame(1, PlacementTestAttempt::where('user_id', $user->id)->count());
    }

    public function test_admin_can_allow_student_to_retake_placement_test(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create();

        PlacementTestAttempt::create([
            'user_id' => $student->id,
            'total_questions' => 2,
            'correct_answers' => 1,
            'score_percentage' => 50,
            'level' => 'Pre-Intermediate',
            'recommended_program' => 'English Conversation',
            'answers' => [],
            'duration_seconds' => 120,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.placement.results.reset', $student))
            ->assertRedirect(route('admin.placement.results'));

        $this->assertDatabaseMissing('placement_test_attempts', [
            'user_id' => $student->id,
        ]);
    }

    public function test_admin_program_group_page_only_shows_main_groups(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $english = $this->createProgramCategory('Bahasa Inggris');
        $this->createProgram('English for Kids', 'Bahasa Inggris', $english);

        $this->actingAs($admin)
            ->get(route('admin.program-categories.index'))
            ->assertOk()
            ->assertSee('Kelompok Program')
            ->assertSee('Bahasa Inggris')
            ->assertDontSee('English for Kids</div>', false)
            ->assertDontSee('Urutan');
    }

    public function test_only_admin_can_open_admin_dashboard(): void
    {
        $student = User::factory()->create(['is_admin' => false]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($student)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    private function createProgram(string $name, string $category, ?ProgramCategory $programCategory = null): Program
    {
        $programCategory ??= $this->createProgramCategory($category);

        return Program::create([
            'program_category_id' => $programCategory->id,
            'name' => $name,
            'description' => "Program {$name}",
            'category' => $category,
            'quota' => 8,
            'price' => 750000,
            'private_price' => $category === 'Bahasa Inggris' ? 1200000 : null,
            'conversation_price' => $category === 'Bahasa Inggris' ? 950000 : null,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonths(6),
            'status' => 'active',
        ]);
    }

    private function createProgramCategory(string $name): ProgramCategory
    {
        return ProgramCategory::firstOrCreate(
            ['slug' => Str::slug($name)],
            [
                'name' => $name,
                'description' => "Kelompok {$name}",
                'sort_order' => 10,
                'is_active' => true,
            ]
        );
    }

    private function createScheduleTemplate(
        Program $program,
        ?string $classType = null,
        string $roomName = 'English Room 1',
        int $maxStudents = 8
    ): ScheduleTemplate {
        $room = ClassRoom::firstOrCreate(
            ['name' => $roomName],
            [
                'category' => str_contains($roomName, 'Bimbel') ? 'Bimbel' : 'English',
                'capacity' => $maxStudents,
                'is_active' => true,
            ]
        );

        $tutor = Tutor::create([
            'program_id' => $program->id,
            'name' => 'Tutor CELL',
            'email' => 'tutor-' . $program->id . '@cell.local',
            'phone' => '081200000000',
            'is_active' => true,
        ]);

        return ScheduleTemplate::create([
            'program_id' => $program->id,
            'tutor_id' => $tutor->id,
            'class_room_id' => $room->id,
            'class_type' => $classType,
            'batch_name' => 'Batch Juli 2026',
            'registration_start_date' => now()->subDay()->toDateString(),
            'registration_end_date' => now()->addDays(5)->toDateString(),
            'learning_start_date' => '2026-07-06',
            'learning_end_date' => '2026-07-31',
            'days' => [1, 3],
            'start_time' => '15:00',
            'end_time' => '16:00',
            'room' => $room->name,
            'max_students' => $maxStudents,
            'is_active' => true,
        ]);
    }

    private function createPlacementQuestion(int $correctOption): PlacementQuestion
    {
        return PlacementQuestion::create([
            'section' => 'Grammar',
            'level' => 'Beginner',
            'question_text' => 'Choose the correct answer.',
            'options' => ['Answer A', 'Answer B', 'Answer C', 'Answer D'],
            'correct_option' => $correctOption,
            'is_active' => true,
            'sort_order' => $correctOption,
        ]);
    }
}
