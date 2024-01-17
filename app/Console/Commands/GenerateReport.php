<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\Student;
use App\Models\Question;
use App\Models\StudentResponse;
use App\Models\Assessment;

class GenerateReport extends Command
{
    const FILE_PATHS = [
        'students' => 'data/students.json',
        'assessments' => 'data/assessments.json',
        'student-responses' => 'data/student-responses.json',
        'questions' => 'data/questions.json',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generate-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate student report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Please enter the following');
        do {
            $studentId = $this->ask('Student ID:');
        } while (!$studentId);

        do {
            $reportType = $this->ask('Report to generate (1 for Diagnostic, 2 for Progress, 3 for Feedback)');
        } while (!in_array($reportType, ['1', '2', '3']));

        $data = $this->loadData(['students', 'assessments', 'student-responses', 'questions']);
        $studentsCollection = $this->createCollection($data['students'] ?? [], Student::class);
        $assessmentCollection = $this->createCollection($data['assessments'] ?? [], Assessment::class);
        $questionsCollection = $this->createCollection($data['questions'] ?? [], Question::class);
        $studentResponsesCollection = $this->createCollection($data['student-responses'] ?? [], StudentResponse::class);

        switch ($reportType) {
            case 1:
                $this->generateDiagnosticReport($studentId, $studentResponsesCollection, $assessmentCollection, $questionsCollection, $studentsCollection);
                break;
            case 2:
                $this->generateProgressReport($studentId, $studentResponsesCollection, $studentsCollection);
                break;
            case 3:
                $this->generateFeedbackReport($studentId, $studentResponsesCollection, $assessmentCollection, $questionsCollection, $studentsCollection);
                break;
            default:
                break;
        }
    }

    private function getStudentResponses($studentId, $studentResponsesCollection)
    {
        return $studentResponsesCollection
            ->filter(function ($item) use ($studentId) {
                return $item['student']['id'] == $studentId;
            });
    }

    private function displayRecentAssesmentSection($studentId, $recentstudentResponse, $assessmentCollection, $studentsCollection)
    {
        $student = $studentsCollection->firstWhere('id', $studentId);
        $assessment = $assessmentCollection->firstWhere('id', $recentstudentResponse->assessmentId);

        if ($student && $assessment) {
            $this->info("{$student->firstName} {$student->lastName} recently completed {$assessment->name} assessment on {$recentstudentResponse->getCompletedDate()}");
        }
        else {
            $this->info("Warning: Student or assessment details not found");
        }
    }


    private function generateDiagnosticReport($studentId, $studentResponsesCollection, $assessmentCollection, $questionsCollection, $studentsCollection)
    {
        $recentstudentResponse = $this->getStudentResponses($studentId, $studentResponsesCollection)->sortByDesc('completed')->first();

        if (!$recentstudentResponse) {
            $this->info("No assessment found for student {$studentId}");
            return;
        } else {
            $this->displayRecentAssesmentSection($studentId, $recentstudentResponse, $assessmentCollection, $studentsCollection);
            $this->info("He got {$recentstudentResponse->results['rawScore']} questions right out of " . count($recentstudentResponse->responses) . ". Details by strand given below: \n");
        }

        $detailedAssesment = [];
        foreach ($recentstudentResponse->responses as $response) {
            $question = $questionsCollection->firstWhere('id', $response['questionId']);

            if (!$question) continue;

            // Initialize strand data if not present
            $strand = $question->strand;
            if (!isset($detailedAssesment[$strand])) {
                $detailedAssesment[$strand] = ['count' => 0, 'correct' => 0];
            }

            $detailedAssesment[$strand]['count']++;

            if ($question->config['key'] == $response['response']) {
                $detailedAssesment[$strand]['correct']++;
            }
        }

        foreach ($detailedAssesment as $strand => $assesment) {
            $this->info("{$strand}: {$assesment['correct']} out of {$assesment['count']} correct");
        }
    }

    private function generateProgressReport($studentId, $studentResponsesCollection, $studentsCollection)
    {
        $studentResponses = $this->getStudentResponses($studentId, $studentResponsesCollection)->sortBy('completed');

        if (!$studentResponses->count()) {
            $this->info("No assessment found for student {$studentId}");
            return;
        }

        $student = $studentsCollection->firstWhere('id', $studentId);
        if ($student) {
            $this->info("{$student->firstName} {$student->lastName} has completed Numeracy assessment {$studentResponses->count()} times in total. Date and raw score given below: \n");
        }
        
        foreach ($studentResponses as $studentResponse) {
            $this->info("Date: {$studentResponse->getAssignedDate()}, Raw Score: {$studentResponse->results['rawScore']} out of " . count($studentResponse->responses));
        }

        $laststudentResponse = $studentResponses->sortByDesc('completed')->first();
        $firststudentResponse = $studentResponses->sortBy('completed')->first();
        $difference = $laststudentResponse->results['rawScore'] - $firststudentResponse->results['rawScore'];
        if ($student) {
            if ($difference < 0) {
                $difference = $difference * -1;
                $this->info("\n{$student->firstName} {$student->lastName} got {$difference} less correct in the recent completed assessment than the oldest");
            }
            else {
                $this->info("\n{$student->firstName} {$student->lastName} got {$difference} more correct in the recent completed assessment than the oldest");
            }
        }
    }

    private function generateFeedbackReport($studentId, $studentResponsesCollection, $assessmentCollection, $questionsCollection, $studentsCollection)
    {
        $recentstudentResponse = $this->getStudentResponses($studentId, $studentResponsesCollection)->sortByDesc('completed')->first();

        if (!$recentstudentResponse) {
            $this->info("No assessment found for student {$studentId}");
            return;
        } else {
            $this->displayRecentAssesmentSection($studentId, $recentstudentResponse, $assessmentCollection, $studentsCollection);
            $this->info("He got {$recentstudentResponse->results['rawScore']} questions right out of " . count($recentstudentResponse->responses) . ". Feedback for wrong answers given below: \n");
        }

        foreach ($recentstudentResponse->responses as $response) {
            $question = $questionsCollection->firstWhere('id', $response['questionId']);

            if (!$question) continue;

            if ($question->config['key'] != $response['response']) {
                $this->info("Question: {$question->stem}");

                $optionsCollection = collect($question->config['options']);
                $studentAnswer = $optionsCollection->firstWhere('id', $response['response']);
                $this->info("Your answer: {$studentAnswer['label']} with value {$studentAnswer['value']}");

                $correctAnswer = $optionsCollection->firstWhere('id', $question->config['key']);
                $this->info("Right answer: {$correctAnswer['label']} with value {$correctAnswer['value']}");

                $this->info("Hint: {$question->config['hint']} \n");
            }
        }
    }


    public function createCollection(array $data, string $className): object
    {
        $collection = collect();
        foreach ($data as $item) {
            if (!$collection->contains(function ($collectionItem) use ($item) {
                return $collectionItem->getId() === $item['id'];
            })) {
                // If the ID does not exist, add the new Student object to the collection
                $collection->push(new $className($item));
            }
        }

        return $collection;
    }

    public function loadData(array $files): array
    {
        $data = [];
        foreach ($files as $file) {
            try {
                $recs = $this->readJsonData($file);
                $data[$file] = $recs;
            } catch (\Exception $e) {
                $data[$file] = [];
            }
        }

        return $data;
    }

    public function readJsonData(string $inputType)
    {
        // return if $inputType is not valid
        if (!array_key_exists($inputType, self::FILE_PATHS)) {
            $this->error('Invalid input type');
            return;
        }

        // check if cache exists
        if (Cache::has($inputType)) {
            return Cache::get($inputType);
        }

        // Check if the file exists and not a json file
        $filePath = self::FILE_PATHS[$inputType];
        if (!Storage::exists($filePath) || !Storage::mimeType($filePath) === 'application/json') {
            $this->error('Invalid file:' . $filePath . ' or file is not a json file');
            return;
        }

        // Read the file
        $jsonContent = Storage::get($filePath);

        // Decode the JSON content
        $data = json_decode($jsonContent, true);

        // cache the data
        Cache::put($inputType, $data, now()->addMinutes(60));

        return $data;
    }
}
