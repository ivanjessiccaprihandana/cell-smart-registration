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
    private const TEST_DURATION_SECONDS = 10;

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

        if ($latestAttempt) {
            session()->forget('placement_test_started_at');
        }

        $placementStartedAt = null;
        $remainingSeconds = self::TEST_DURATION_SECONDS;

        if (!$latestAttempt && $questions->isNotEmpty()) {
            $placementStartedAt = session('placement_test_started_at');

            if (!$placementStartedAt) {
                $placementStartedAt = now()->timestamp;
                session(['placement_test_started_at' => $placementStartedAt]);
            }

            $remainingSeconds = max(0, self::TEST_DURATION_SECONDS - (now()->timestamp - (int) $placementStartedAt));
        }

        return view('placement-test', [
            'questions' => $questions,
            'latestAttempt' => $latestAttempt,
            'placementStartedAt' => $placementStartedAt,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    public function store(Request $request)
    {
        $paymentRedirect = $this->ensurePaymentCompleted();
        if ($paymentRedirect) {
            return $paymentRedirect;
        }

        if (PlacementTestAttempt::where('user_id', Auth::id())->exists()) {
            return redirect()
                ->route('placement-test')
                ->withErrors(['placement_test' => 'Placement test sudah pernah dikerjakan. Hubungi admin jika perlu mengulang test.']);
        }

        $questions = PlacementQuestion::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            return back()->withErrors(['placement_test' => 'Belum ada soal aktif. Silakan hubungi admin.']);
        }

        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'started_at' => ['nullable', 'integer'],
        ]);

        $startedAt = (int) session('placement_test_started_at', $validated['started_at'] ?? now()->timestamp);
        $submittedAnswers = $validated['answers'] ?? [];
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
        $durationSeconds = min(self::TEST_DURATION_SECONDS, max(0, now()->timestamp - $startedAt));

        $attempt = PlacementTestAttempt::create([
            'user_id' => Auth::id(),
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'score_percentage' => $scorePercentage,
            'level' => $placementLevel['level'],
            'recommended_program' => $placementLevel['level'],
            'answers' => $answerRows,
            'duration_seconds' => $durationSeconds,
        ]);

        session()->forget('placement_test_started_at');

        return redirect()
            ->route('student.schedule')
            ->with('placement_attempt_id', $attempt->id)
            ->with('success', 'Placement test berhasil dikirim. Silakan konsultasi jadwal belajar dengan admin.');
    }

    private function placementLevel(int $scorePercentage): array
    {
        return match (true) {
            $scorePercentage >= 90 => ['level' => 'Advanced'],
            $scorePercentage >= 75 => ['level' => 'Upper-Intermediate'],
            $scorePercentage >= 60 => ['level' => 'Intermediate'],
            $scorePercentage >= 45 => ['level' => 'Pre-Intermediate'],
            $scorePercentage >= 30 => ['level' => 'Elementary'],
            default => ['level' => 'Beginner'],
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
