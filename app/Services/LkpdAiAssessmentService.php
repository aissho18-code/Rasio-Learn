<?php

namespace App\Services;

class LkpdAiAssessmentService
{
    public function assess(array $questions, array $answers): array
    {
        $graded = [];

        foreach ($questions as $question) {
            $studentAnswer = trim((string) ($answers[$question->id] ?? ''));
            $rubric = trim((string) ($question->rubrik_jawaban ?? ''));

            $answerKey = $this->extractAnswerKey($rubric);
            $isCorrect = $this->matchesAnswer($studentAnswer, $answerKey);

            $feedback = $isCorrect
                ? 'Jawaban sesuai dengan kunci atau kriteria jawaban.'
                : ($question->pembahasan
                    ? trim($question->pembahasan)
                    : 'Jawaban belum sesuai dengan kunci atau kriteria jawaban.');

            $graded[(string) $question->id] = [
                'is_correct' => $isCorrect,
                'feedback' => $feedback,
            ];
        }

        return $graded;
    }

    private function extractAnswerKey(string $rubric): string
    {
        $parts = preg_split('/\s*\/\s*/', $rubric, 2);

        return trim($parts[0] ?? $rubric);
    }

    private function matchesAnswer(string $studentAnswer, string $answerKey): bool
    {
        if ($studentAnswer === '' || $answerKey === '') {
            return false;
        }

        $student = $this->normalize($studentAnswer);
        $key = $this->normalize($answerKey);

        if ($student === $key) {
            return true;
        }

        $parts = preg_split('/\s+dan\s+/i', $key);

        if (count($parts) > 1) {
            foreach ($parts as $part) {
                $part = trim($part);

                if ($part !== '' && !str_contains($student, $part)) {
                    return false;
                }
            }

            return true;
        }

        return str_contains($student, $key);
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);

        $text = str_replace(
            ['→', '＝', '：', '−', '–', '—'],
            [' ', '=', ':', '-', '-', '-'],
            $text
        );

        $text = preg_replace('/[.,;!?]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }
}
