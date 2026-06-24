<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $questions = $this->questions();
        $now = now();

        foreach ($questions as $index => $question) {
            DB::table('placement_questions')->updateOrInsert(
                ['question_text' => $question['question_text']],
                [
                    'section' => $question['section'],
                    'level' => $question['level'],
                    'options' => json_encode($question['options']),
                    'correct_option' => $question['correct_option'],
                    'explanation' => $question['explanation'],
                    'is_active' => true,
                    'sort_order' => 100 + $index,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('placement_questions')
            ->whereIn('question_text', collect($this->questions())->pluck('question_text')->all())
            ->delete();
    }

    private function questions(): array
    {
        return [
            [
                'section' => 'Grammar',
                'level' => 'Beginner',
                'question_text' => 'Choose the correct sentence.',
                'options' => ['She are a student.', 'She is a student.', 'She am a student.', 'She be a student.'],
                'correct_option' => 1,
                'explanation' => 'Use "is" with she, he, and it.',
            ],
            [
                'section' => 'Vocabulary',
                'level' => 'Beginner',
                'question_text' => 'What is the opposite of "hot"?',
                'options' => ['Cold', 'Big', 'Fast', 'Short'],
                'correct_option' => 0,
                'explanation' => 'The opposite of hot is cold.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Beginner',
                'question_text' => 'I ___ from Indonesia.',
                'options' => ['is', 'are', 'am', 'be'],
                'correct_option' => 2,
                'explanation' => 'Use "am" with I.',
            ],
            [
                'section' => 'Functional English',
                'level' => 'Beginner',
                'question_text' => 'Which expression is used to greet someone in the morning?',
                'options' => ['Good night', 'Good morning', 'Goodbye', 'See you'],
                'correct_option' => 1,
                'explanation' => 'Good morning is used as a morning greeting.',
            ],
            [
                'section' => 'Reading',
                'level' => 'Beginner',
                'question_text' => 'Read: "Tom has a cat. The cat is black." What color is the cat?',
                'options' => ['White', 'Brown', 'Black', 'Orange'],
                'correct_option' => 2,
                'explanation' => 'The text says the cat is black.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Beginner',
                'question_text' => 'There ___ two books on the table.',
                'options' => ['is', 'are', 'am', 'be'],
                'correct_option' => 1,
                'explanation' => 'Use "are" for plural nouns.',
            ],

            [
                'section' => 'Grammar',
                'level' => 'Elementary',
                'question_text' => 'She usually ___ breakfast at 7 a.m.',
                'options' => ['have', 'has', 'having', 'had'],
                'correct_option' => 1,
                'explanation' => 'Use "has" for third person singular in simple present.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Elementary',
                'question_text' => 'They ___ football yesterday.',
                'options' => ['play', 'plays', 'played', 'playing'],
                'correct_option' => 2,
                'explanation' => 'Use simple past for yesterday.',
            ],
            [
                'section' => 'Vocabulary',
                'level' => 'Elementary',
                'question_text' => 'A person who teaches students is a ___.',
                'options' => ['doctor', 'teacher', 'driver', 'farmer'],
                'correct_option' => 1,
                'explanation' => 'A teacher teaches students.',
            ],
            [
                'section' => 'Functional English',
                'level' => 'Elementary',
                'question_text' => 'Choose the best response: "Thank you."',
                'options' => ['I am fine.', 'You are welcome.', 'Good morning.', 'Nice to meet you.'],
                'correct_option' => 1,
                'explanation' => 'You are welcome is a response to thank you.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Elementary',
                'question_text' => 'My brother is taller ___ me.',
                'options' => ['as', 'than', 'from', 'to'],
                'correct_option' => 1,
                'explanation' => 'Use "than" in comparative sentences.',
            ],
            [
                'section' => 'Reading',
                'level' => 'Elementary',
                'question_text' => 'Read: "Anna goes to school by bus because her house is far." Why does Anna take the bus?',
                'options' => ['Her house is far.', 'She likes walking.', 'She has no school.', 'The bus is new.'],
                'correct_option' => 0,
                'explanation' => 'The text says her house is far.',
            ],

            [
                'section' => 'Grammar',
                'level' => 'Pre-Intermediate',
                'question_text' => 'I have lived here ___ 2020.',
                'options' => ['for', 'since', 'during', 'from'],
                'correct_option' => 1,
                'explanation' => 'Use "since" with a starting point in time.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Pre-Intermediate',
                'question_text' => 'If it rains, we ___ at home.',
                'options' => ['stay', 'stayed', 'will stay', 'would stay'],
                'correct_option' => 2,
                'explanation' => 'First conditional uses if + present, will + verb.',
            ],
            [
                'section' => 'Vocabulary',
                'level' => 'Pre-Intermediate',
                'question_text' => 'The word "purchase" is closest in meaning to ___.',
                'options' => ['sell', 'buy', 'borrow', 'keep'],
                'correct_option' => 1,
                'explanation' => 'Purchase means buy.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Pre-Intermediate',
                'question_text' => 'This book was ___ by a famous author.',
                'options' => ['write', 'wrote', 'written', 'writing'],
                'correct_option' => 2,
                'explanation' => 'Passive voice uses be + past participle.',
            ],
            [
                'section' => 'Functional English',
                'level' => 'Pre-Intermediate',
                'question_text' => 'Choose the polite request.',
                'options' => ['Give me your pen.', 'Could I borrow your pen?', 'I take your pen.', 'You must give pen.'],
                'correct_option' => 1,
                'explanation' => 'Could I borrow your pen? is polite.',
            ],
            [
                'section' => 'Reading',
                'level' => 'Pre-Intermediate',
                'question_text' => 'Read: "The meeting was postponed because the manager was sick." What happened to the meeting?',
                'options' => ['It started early.', 'It was canceled forever.', 'It was delayed.', 'It was finished.'],
                'correct_option' => 2,
                'explanation' => 'Postponed means delayed to a later time.',
            ],

            [
                'section' => 'Grammar',
                'level' => 'Intermediate',
                'question_text' => 'By the time I arrived, the movie ___.',
                'options' => ['starts', 'started', 'had started', 'has started'],
                'correct_option' => 2,
                'explanation' => 'Past perfect shows an action before another past action.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Intermediate',
                'question_text' => 'She suggested ___ earlier to avoid traffic.',
                'options' => ['leave', 'to leave', 'leaving', 'left'],
                'correct_option' => 2,
                'explanation' => 'Suggest is followed by a gerund.',
            ],
            [
                'section' => 'Vocabulary',
                'level' => 'Intermediate',
                'question_text' => 'The word "reliable" means ___.',
                'options' => ['easy to trust', 'very expensive', 'hard to find', 'quick to forget'],
                'correct_option' => 0,
                'explanation' => 'Reliable means trustworthy or dependable.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Intermediate',
                'question_text' => 'The report ___ before the deadline.',
                'options' => ['must submit', 'must be submitted', 'must submitted', 'must be submit'],
                'correct_option' => 1,
                'explanation' => 'Passive modal form is modal + be + past participle.',
            ],
            [
                'section' => 'Functional English',
                'level' => 'Intermediate',
                'question_text' => 'Choose the best way to express an opinion.',
                'options' => ['In my opinion, online learning is useful.', 'You wrong about learning.', 'I am opinion online.', 'Learning is because useful.'],
                'correct_option' => 0,
                'explanation' => 'In my opinion is a natural phrase for giving opinions.',
            ],
            [
                'section' => 'Reading',
                'level' => 'Intermediate',
                'question_text' => 'Read: "Although the product was expensive, customers bought it because of its high quality." Why did customers buy it?',
                'options' => ['It was cheap.', 'It was high quality.', 'It was old.', 'It was difficult to use.'],
                'correct_option' => 1,
                'explanation' => 'The reason given is its high quality.',
            ],

            [
                'section' => 'Grammar',
                'level' => 'Upper-Intermediate',
                'question_text' => 'Had I known about the problem, I ___ you earlier.',
                'options' => ['will call', 'would call', 'would have called', 'called'],
                'correct_option' => 2,
                'explanation' => 'This is an inverted third conditional structure.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Upper-Intermediate',
                'question_text' => 'The proposal, ___ was submitted last week, has been approved.',
                'options' => ['who', 'where', 'which', 'whose'],
                'correct_option' => 2,
                'explanation' => 'Use "which" for a non-defining relative clause about a thing.',
            ],
            [
                'section' => 'Vocabulary',
                'level' => 'Upper-Intermediate',
                'question_text' => 'The phrase "to put off" means ___.',
                'options' => ['to postpone', 'to continue', 'to accept', 'to explain'],
                'correct_option' => 0,
                'explanation' => 'Put off means postpone.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Upper-Intermediate',
                'question_text' => 'He denied ___ the confidential file.',
                'options' => ['to open', 'open', 'opening', 'opened'],
                'correct_option' => 2,
                'explanation' => 'Deny is followed by a gerund.',
            ],
            [
                'section' => 'Functional English',
                'level' => 'Upper-Intermediate',
                'question_text' => 'Choose the best phrase for a formal email closing.',
                'options' => ['See ya', 'Cheers bro', 'Yours sincerely', 'Later'],
                'correct_option' => 2,
                'explanation' => 'Yours sincerely is appropriate in formal emails.',
            ],
            [
                'section' => 'Reading',
                'level' => 'Upper-Intermediate',
                'question_text' => 'Read: "The policy was implemented to reduce unnecessary expenses." What was the purpose of the policy?',
                'options' => ['To increase costs', 'To reduce expenses', 'To hire more staff', 'To delay work'],
                'correct_option' => 1,
                'explanation' => 'The text says the policy aimed to reduce expenses.',
            ],

            [
                'section' => 'Grammar',
                'level' => 'Advanced',
                'question_text' => 'No sooner had the lecture begun ___ the fire alarm rang.',
                'options' => ['when', 'than', 'then', 'that'],
                'correct_option' => 1,
                'explanation' => 'The fixed structure is no sooner ... than.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Advanced',
                'question_text' => 'The findings are believed ___ significant implications.',
                'options' => ['have', 'having', 'to have', 'had'],
                'correct_option' => 2,
                'explanation' => 'Use passive reporting: are believed to have.',
            ],
            [
                'section' => 'Vocabulary',
                'level' => 'Advanced',
                'question_text' => 'The word "ambiguous" means ___.',
                'options' => ['clear and direct', 'open to more than one interpretation', 'simple to solve', 'impossible to read'],
                'correct_option' => 1,
                'explanation' => 'Ambiguous means unclear or having more than one meaning.',
            ],
            [
                'section' => 'Grammar',
                'level' => 'Advanced',
                'question_text' => 'Were it not for his support, the project ___.',
                'options' => ['will fail', 'would have failed', 'fails', 'has failed'],
                'correct_option' => 1,
                'explanation' => 'This is an inverted conditional meaning if it had not been for his support.',
            ],
            [
                'section' => 'Functional English',
                'level' => 'Advanced',
                'question_text' => 'Choose the sentence that sounds most diplomatic.',
                'options' => ['Your idea is bad.', 'This will never work.', 'I see your point, but we may need to consider another approach.', 'You do not understand.'],
                'correct_option' => 2,
                'explanation' => 'The correct option expresses disagreement politely.',
            ],
            [
                'section' => 'Reading',
                'level' => 'Advanced',
                'question_text' => 'Read: "Despite initial skepticism, the initiative gained support after early results proved promising." What changed people\'s attitude?',
                'options' => ['The cost increased.', 'The early results were promising.', 'The initiative was canceled.', 'People ignored the results.'],
                'correct_option' => 1,
                'explanation' => 'Support grew because early results were promising.',
            ],
        ];
    }
};
