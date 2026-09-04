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

class AdminManagementFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(now()->setDate(2026, 7, 2)->setTime(10, 0));
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_create_update_and_delete_unused_program(): void
    {
        $category = $this->createProgramCategory('Bahasa Inggris');

        $this->actingAs($this->admin)
            ->post(route('admin.programs.store'), [
                'program_category_id' => $category->id,
                'name' => 'English for Exam',
                'description' => 'Program persiapan ujian Bahasa Inggris.',
                'category' => 'Bahasa Inggris',
                'quota' => 12,
                'price' => 1000000,
                'private_price' => 1500000,
                'conversation_price' => 1250000,
                'start_date' => '2026-07-02',
                'end_date' => '2026-12-27',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.programs.index'))
            ->assertSessionHasNoErrors();

        $program = Program::where('name', 'English for Exam')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('admin.programs.update', $program), [
                'program_category_id' => $category->id,
                'name' => 'English for Exam Updated',
                'description' => 'Program persiapan ujian yang diperbarui.',
                'category' => 'Bahasa Inggris',
                'quota' => 10,
                'price' => 1100000,
                'private_price' => 1600000,
                'conversation_price' => 1300000,
                'start_date' => '2026-07-02',
                'end_date' => '2026-12-27',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('admin.programs.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
            'name' => 'English for Exam Updated',
            'quota' => 10,
            'price' => 1100000,
            'status' => 'inactive',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.programs.destroy', $program))
            ->assertRedirect(route('admin.programs.index'));

        $this->assertDatabaseMissing('programs', ['id' => $program->id]);
    }

    public function test_admin_can_manage_tutor_and_auto_fill_empty_schedule_template(): void
    {
        $program = $this->createProgram('English for Kids', 'Bahasa Inggris');
        $room = $this->createClassRoom('English Room 1');
        $template = $this->createScheduleTemplate($program, $room, tutor: null);

        $this->actingAs($this->admin)
            ->post(route('admin.tutors.store'), [
                'program_id' => $program->id,
                'name' => 'Rani Putri',
                'email' => 'rani.putri@cell.local',
                'phone' => '081200000001',
                'level' => null,
                'notes' => 'Tutor Bahasa Inggris anak.',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.tutors.index'))
            ->assertSessionHasNoErrors();

        $tutor = Tutor::where('email', 'rani.putri@cell.local')->firstOrFail();

        $this->assertDatabaseHas('schedule_templates', [
            'id' => $template->id,
            'tutor_id' => $tutor->id,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.tutors.update', $tutor), [
                'program_id' => $program->id,
                'name' => 'Rani Putri, S.Pd',
                'email' => 'rani.putri@cell.local',
                'phone' => '081200000002',
                'level' => 'Beginner',
                'notes' => 'Tutor Bahasa Inggris level dasar.',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.tutors.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tutors', [
            'id' => $tutor->id,
            'name' => 'Rani Putri, S.Pd',
            'level' => 'Beginner',
        ]);

        $unusedTutor = Tutor::create([
            'program_id' => $program->id,
            'name' => 'Tutor Cadangan',
            'email' => 'cadangan@cell.local',
            'phone' => '081200000003',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.tutors.destroy', $unusedTutor))
            ->assertRedirect(route('admin.tutors.index'));

        $this->assertDatabaseMissing('tutors', ['id' => $unusedTutor->id]);
    }

    public function test_admin_can_manage_class_room_and_cannot_delete_used_room(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.class-rooms.store'), [
                'name' => 'English Room Test',
                'category' => 'English',
                'capacity' => 8,
                'notes' => 'Ruang untuk pengujian.',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.class-rooms.index'))
            ->assertSessionHasNoErrors();

        $room = ClassRoom::where('name', 'English Room Test')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('admin.class-rooms.update', $room), [
                'name' => 'English Room Test Updated',
                'category' => 'English',
                'capacity' => 6,
                'notes' => 'Ruang diperbarui.',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.class-rooms.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('class_rooms', [
            'id' => $room->id,
            'name' => 'English Room Test Updated',
            'capacity' => 6,
        ]);

        $program = $this->createProgram('English for Teens', 'Bahasa Inggris');
        $this->createScheduleTemplate($program, $room->fresh());

        $this->actingAs($this->admin)
            ->delete(route('admin.class-rooms.destroy', $room))
            ->assertRedirect(route('admin.class-rooms.index'))
            ->assertSessionHasErrors('room');

        $this->assertDatabaseHas('class_rooms', ['id' => $room->id]);
    }

    public function test_admin_can_create_update_and_delete_placement_question(): void
    {
        $payload = [
            'section' => 'Grammar',
            'level' => 'Beginner',
            'question_text' => 'She ... a student.',
            'options' => ['am', 'is', 'are', 'be'],
            'correct_option' => 1,
            'explanation' => 'Subject she menggunakan is.',
            'sort_order' => 1,
            'is_active' => '1',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.placement.questions.store'), $payload)
            ->assertRedirect(route('admin.placement.questions.index'))
            ->assertSessionHasNoErrors();

        $question = PlacementQuestion::where('question_text', 'She ... a student.')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('admin.placement.questions.update', $question), [
                ...$payload,
                'level' => 'Elementary',
                'question_text' => 'They ... students.',
                'correct_option' => 2,
            ])
            ->assertRedirect(route('admin.placement.questions.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('placement_questions', [
            'id' => $question->id,
            'level' => 'Elementary',
            'question_text' => 'They ... students.',
            'correct_option' => 2,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.placement.questions.destroy', $question))
            ->assertRedirect(route('admin.placement.questions.index'));

        $this->assertDatabaseMissing('placement_questions', ['id' => $question->id]);
    }

    public function test_admin_can_view_overall_recap_with_database_statistics(): void
    {
        $program = $this->createProgram('English for Recap', 'Bahasa Inggris');
        $student = User::factory()->create([
            'name' => 'Siswa Rekap',
            'program' => (string) $program->id,
            'class_type' => 'Reguler',
            'payment_status' => 'diterima',
        ]);

        ProgramEnrollment::create([
            'user_id' => $student->id,
            'program_id' => $program->id,
            'class_type' => 'Reguler',
            'type' => 'new',
            'enrolled_at' => now(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'status' => 'active',
        ]);

        PlacementTestAttempt::create([
            'user_id' => $student->id,
            'total_questions' => 10,
            'correct_answers' => 8,
            'score_percentage' => 80,
            'level' => 'Upper-Intermediate',
            'recommended_program' => 'Upper-Intermediate',
            'answers' => [],
            'duration_seconds' => 900,
        ]);

        ClassSchedule::create([
            'user_id' => $student->id,
            'program_id' => $program->id,
            'class_type' => 'Reguler',
            'class_date' => now()->toDateString(),
            'session_name' => 'Kelas Rekap',
            'start_time' => '15:00',
            'end_time' => '16:00',
            'room' => 'Ruang Rekap',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.recap', [
                'from' => now()->subDay()->toDateString(),
                'to' => now()->addDay()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Rekap Keseluruhan')
            ->assertSee('English for Recap')
            ->assertSee('Siswa Rekap')
            ->assertSee('Upper-Intermediate')
            ->assertViewHas('stats', function (array $stats) {
                return $stats['totalEnrollments'] === 1
                    && $stats['uniqueStudents'] === 1
                    && $stats['acceptedPayments'] === 1
                    && $stats['placementCompleted'] === 1
                    && $stats['averagePlacementScore'] === 80
                    && $stats['scheduledStudents'] === 1
                    && $stats['classSessions'] === 1;
            });

        $exportResponse = $this->actingAs($this->admin)
            ->get(route('admin.recap.export', [
                'from' => now()->subDay()->toDateString(),
                'to' => now()->addDay()->toDateString(),
            ]));

        $exportResponse
            ->assertOk()
            ->assertDownload('rekap-cell-2026-07-01-sampai-2026-07-03.xlsx');

        $exportPath = $exportResponse->baseResponse->getFile()->getPathname();
        $archive = new \ZipArchive();

        $this->assertTrue($archive->open($exportPath) === true);
        $this->assertNotFalse($archive->locateName('xl/workbook.xml'));
        $this->assertNotFalse($archive->locateName('xl/worksheets/sheet1.xml'));
        $workbookXml = $archive->getFromName('xl/workbook.xml');

        $this->assertIsString($workbookXml);
        $this->assertStringContainsString('name="Ringkasan"', $workbookXml);
        $this->assertStringContainsString('name="Per Program"', $workbookXml);
        $this->assertStringContainsString('name="Pendaftaran"', $workbookXml);
        $this->assertStringContainsString('name="Placement Test"', $workbookXml);
        $this->assertStringContainsString('name="Jadwal Kelas"', $workbookXml);

        $archive->close();
        @unlink($exportPath);
    }

    public function test_overall_recap_rejects_an_invalid_date_range(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.recap', [
                'from' => '2026-07-10',
                'to' => '2026-07-01',
            ]))
            ->assertSessionHasErrors('to');
    }

    public function test_exported_program_recap_formats_many_student_names_as_a_bounded_numbered_list(): void
    {
        $program = $this->createProgram('English for Export', 'Bahasa Inggris');

        foreach (range(1, 10) as $studentNumber) {
            $studentName = sprintf('Siswa %02d Export', $studentNumber);
            $student = User::factory()->create([
                'name' => $studentName,
                'program' => (string) $program->id,
                'class_type' => 'Reguler',
            ]);

            ProgramEnrollment::create([
                'user_id' => $student->id,
                'program_id' => $program->id,
                'class_type' => 'Reguler',
                'type' => 'new',
                'enrolled_at' => now(),
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'status' => 'active',
            ]);
        }

        $exportResponse = $this->actingAs($this->admin)
            ->get(route('admin.recap.export'));
        $exportPath = $exportResponse->baseResponse->getFile()->getPathname();
        $archive = new \ZipArchive();

        $this->assertTrue($archive->open($exportPath) === true);

        $programSheetXml = $archive->getFromName('xl/worksheets/sheet2.xml');

        $this->assertIsString($programSheetXml);
        $this->assertStringContainsString("1. Siswa 01 Export\n2. Siswa 02 Export", $programSheetXml);
        $this->assertStringContainsString("8. Siswa 08 Export\n+ 2 siswa lainnya (lihat sheet Pendaftaran)", $programSheetXml);
        $this->assertMatchesRegularExpression('/<row r="2"[^>]*ht="162"[^>]*>/', $programSheetXml);

        $archive->close();
        @unlink($exportPath);
    }

    public function test_admin_can_create_update_schedule_template_and_sync_existing_student_schedules(): void
    {
        $program = $this->createProgram('English for Adult', 'Bahasa Inggris');
        $room = $this->createClassRoom('English Room 2');
        $newRoom = $this->createClassRoom('English Room 3');
        $tutor = $this->createTutor($program, 'Mira Anggraini');
        $newTutor = $this->createTutor($program, 'Laras Wulandari');

        $this->actingAs($this->admin)
            ->post(route('admin.schedule-templates.store'), [
                'program_id' => $program->id,
                'tutor_id' => $tutor->id,
                'class_room_id' => $room->id,
                'class_type' => 'Private',
                'private_package' => 'Conversation',
                'level' => null,
                'batch_name' => 'Conversation Private - August 2026',
                'registration_start_date' => '2026-07-02',
                'registration_end_date' => '2026-07-31',
                'learning_start_date' => '2026-08-01',
                'learning_end_date' => '2026-10-31',
                'days' => [3, 6],
                'start_time' => '19:00',
                'end_time' => '20:00',
                'room' => $room->name,
                'max_students' => 1,
                'notes' => 'Paket private 25 pertemuan.',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.schedule-templates.index'))
            ->assertSessionHasNoErrors();

        $template = ScheduleTemplate::where('batch_name', 'Conversation Private - August 2026')->firstOrFail();
        $student = User::factory()->create();

        ClassSchedule::create([
            'user_id' => $student->id,
            'program_id' => $program->id,
            'schedule_template_id' => $template->id,
            'class_room_id' => $room->id,
            'tutor_id' => $tutor->id,
            'class_type' => 'Private',
            'private_package' => 'Conversation',
            'class_date' => '2026-08-05',
            'session_name' => 'Pertemuan 1',
            'start_time' => '19:00',
            'end_time' => '20:00',
            'room' => $room->name,
            'max_students' => 1,
            'notes' => 'Paket private 25 pertemuan.',
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.schedule-templates.update', $template), [
                'program_id' => $program->id,
                'tutor_id' => $newTutor->id,
                'class_room_id' => $newRoom->id,
                'class_type' => 'Private',
                'private_package' => 'Conversation',
                'level' => null,
                'batch_name' => 'Conversation Private - August 2026 Updated',
                'registration_start_date' => '2026-07-02',
                'registration_end_date' => '2026-07-31',
                'learning_start_date' => '2026-08-01',
                'learning_end_date' => '2026-10-31',
                'days' => [3, 6],
                'start_time' => '18:30',
                'end_time' => '19:30',
                'room' => $newRoom->name,
                'max_students' => 1,
                'notes' => 'Jadwal private diperbarui.',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.schedule-templates.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('class_schedules', [
            'user_id' => $student->id,
            'schedule_template_id' => $template->id,
            'tutor_id' => $newTutor->id,
            'class_room_id' => $newRoom->id,
            'session_name' => 'Conversation Private - August 2026 Updated',
            'start_time' => '18:30',
            'end_time' => '19:30',
            'room' => $newRoom->name,
            'notes' => 'Jadwal private diperbarui.',
        ]);
    }

    public function test_payment_upload_rejects_invalid_file_format(): void
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
                'payment_proof' => UploadedFile::fake()->create('bukti.txt', 10, 'text/plain'),
            ])
            ->assertSessionHasErrors('payment_proof');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'payment_status' => 'belum_upload',
            'payment_proof_path' => null,
        ]);
    }

    public function test_invoice_can_only_be_opened_after_admin_accepts_payment(): void
    {
        $user = User::factory()->create([
            'payment_status' => 'menunggu_verifikasi',
        ]);
        $program = $this->createProgram('English for Teens', 'Bahasa Inggris');

        $user->update([
            'program' => (string) $program->id,
            'class_type' => 'Reguler',
        ]);

        ProgramEnrollment::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'class_type' => 'Reguler',
            'type' => 'new',
            'enrolled_at' => now(),
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-31',
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->get(route('programs.invoice'))
            ->assertRedirect(route('student.status'))
            ->assertSessionHasErrors('invoice');

        $user->update(['payment_status' => 'diterima']);

        $this->actingAs($user)
            ->get(route('programs.invoice'))
            ->assertOk()
            ->assertSee('Invoice')
            ->assertSee('English for Teens');
    }

    private function createProgram(string $name, string $category): Program
    {
        $programCategory = $this->createProgramCategory($category);

        return Program::create([
            'program_category_id' => $programCategory->id,
            'name' => $name,
            'description' => "Program {$name}",
            'category' => $category,
            'quota' => 8,
            'price' => 750000,
            'private_price' => $category === 'Bahasa Inggris' ? 2499000 : null,
            'conversation_price' => $category === 'Bahasa Inggris' ? 1200000 : null,
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

    private function createClassRoom(string $name, string $category = 'English', int $capacity = 8): ClassRoom
    {
        return ClassRoom::firstOrCreate(
            ['name' => $name],
            [
                'category' => $category,
                'capacity' => $capacity,
                'is_active' => true,
            ]
        );
    }

    private function createTutor(Program $program, string $name): Tutor
    {
        return Tutor::create([
            'program_id' => $program->id,
            'name' => $name,
            'email' => Str::slug($name) . '@cell.local',
            'phone' => '081200000000',
            'is_active' => true,
        ]);
    }

    private function createScheduleTemplate(Program $program, ClassRoom $room, ?Tutor $tutor = null): ScheduleTemplate
    {
        return ScheduleTemplate::create([
            'program_id' => $program->id,
            'tutor_id' => $tutor?->id,
            'class_room_id' => $room->id,
            'class_type' => 'Reguler',
            'batch_name' => 'Batch Test 2026',
            'registration_start_date' => '2026-07-02',
            'registration_end_date' => '2026-07-31',
            'learning_start_date' => '2026-08-01',
            'learning_end_date' => '2026-08-31',
            'days' => [1, 3],
            'start_time' => '15:00',
            'end_time' => '16:00',
            'room' => $room->name,
            'max_students' => 8,
            'notes' => 'Jadwal belajar batch berjalan 2 kali seminggu.',
            'is_active' => true,
        ]);
    }
}
