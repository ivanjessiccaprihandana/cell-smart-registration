<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\PlacementQuestion;
use App\Models\PlacementTestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PlacementTestController extends Controller
{
    public function index()
    {
        $paymentRedirect = $this->ensurePaymentCompleted();
        if ($paymentRedirect) {
            return $paymentRedirect;
        }

        $questions = PlacementQuestion::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $latestAttempt = PlacementTestAttempt::where('user_id', Auth::id())
            ->latest()
            ->first();

        return view('placement-test', [
            'questions' => $questions,
            'latestAttempt' => $latestAttempt,
        ]);
    }

    public function store(Request $request)
    {
        $paymentRedirect = $this->ensurePaymentCompleted();
        if ($paymentRedirect) {
            return $paymentRedirect;
        }

        $questions = PlacementQuestion::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            return back()->withErrors(['placement_test' => 'Belum ada soal aktif. Silakan hubungi admin.']);
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'started_at' => ['nullable', 'integer'],
        ], [
            'answers.required' => 'Silakan jawab minimal satu soal terlebih dahulu.',
        ]);

        $submittedAnswers = $validated['answers'];
        $answerRows = [];
        $correctAnswers = 0;

        foreach ($questions as $question) {
            $selected = isset($submittedAnswers[$question->id]) ? (int) $submittedAnswers[$question->id] : null;
            $isCorrect = $selected !== null && $selected === $question->correct_option;

            if ($isCorrect) {
                $correctAnswers++;
            }

            $answerRows[] = [
                'question_id' => $question->id,
                'section' => $question->section,
                'level' => $question->level,
                'question_text' => $question->question_text,
                'options' => $question->options,
                'selected_option' => $selected,
                'correct_option' => $question->correct_option,
                'is_correct' => $isCorrect,
            ];
        }

        $totalQuestions = $questions->count();
        $scorePercentage = (int) round(($correctAnswers / $totalQuestions) * 100);
        $placementLevel = $this->placementLevel($scorePercentage);
        $durationSeconds = isset($validated['started_at'])
            ? max(0, now()->timestamp - (int) $validated['started_at'])
            : null;

        $attempt = PlacementTestAttempt::create([
            'user_id' => Auth::id(),
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'score_percentage' => $scorePercentage,
            'level' => $placementLevel['level'],
            'recommended_program' => $placementLevel['program'],
            'answers' => $answerRows,
            'duration_seconds' => $durationSeconds,
        ]);

        return redirect()
            ->route('student.schedule')
            ->with('placement_attempt_id', $attempt->id)
            ->with('success', 'Placement test berhasil dikirim. Silakan konsultasi jadwal belajar dengan admin.');
    }

    private function placementLevel(int $scorePercentage): array
    {
        return match (true) {
            $scorePercentage >= 90 => ['level' => 'Advanced', 'program' => 'Advanced English / Test Preparation'],
            $scorePercentage >= 75 => ['level' => 'Upper-Intermediate', 'program' => 'TOEIC / TOEFL Preparation'],
            $scorePercentage >= 60 => ['level' => 'Intermediate', 'program' => 'Conversation & Test Prep'],
            $scorePercentage >= 45 => ['level' => 'Pre-Intermediate', 'program' => 'English Conversation'],
            $scorePercentage >= 30 => ['level' => 'Elementary', 'program' => 'English for Teens Basic'],
            default => ['level' => 'Beginner', 'program' => 'English Basic Class'],
        };
    }

    private function ensurePaymentCompleted(): ?\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        if (!$user->program) {
            return redirect()
                ->route('programs.index')
                ->withErrors(['program' => 'Silakan pilih program terlebih dahulu sebelum mengerjakan placement test.']);
        }

        if ($user->payment_status !== 'diterima') {
            return redirect()
                ->route('student.status')
                ->withErrors(['placement_test' => 'Placement test baru bisa dikerjakan setelah pembayaran disetujui admin.']);
        }

        $program = Program::find($user->program);

        if ($program && !$this->programRequiresPlacementTest($program)) {
            return redirect()
                ->route('student.schedule')
                ->with('success', 'Program ini tidak memerlukan placement test. Silakan lanjut konsultasi jadwal.');
        }

        return null;
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
}
