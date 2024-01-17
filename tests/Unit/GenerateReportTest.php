<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Console\Commands\GenerateReport;
use App\Models\StudentResponse;
use App\Models\Student;

class GenerateReportTest extends TestCase
{
    protected $generateReport;
    protected $studentResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->generateReport = new GenerateReport();

        $studentResponseData = [
            'id' => '1',
            'assessmentId' => '1',
            'assigned' => '01/12/2022 12:00:00',
            'started' => '02/12/2022 12:00:00',
            'completed' => '03/12/2022 12:00:00',
            'student' => [
                'id' => 'student1',
                'yearLevel' => 3
            ],
            'responses' => [
            ],
            'results' => [
            ],
        ];
        $this->studentResponse = new StudentResponse($studentResponseData);
    }

    public function test_create_collection_success(): void
    {
        $data = [
            [
                'id' => '1',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'yearLevel' => '10',
            ],
        ];

        $collection = $this->generateReport->createCollection($data, Student::class);
        $this->assertInstanceOf('Illuminate\Support\Collection', $collection);
    }

    public function test_get_completed_date_success(): void
    {
        $formattedDate = $this->studentResponse->getCompletedDate();
        $this->assertEquals('3rd December 2022 12:00 PM', $formattedDate);
    }

    public function test_get_completed_date_fail(): void
    {
        $formattedDate = $this->studentResponse->getCompletedDate();
        $this->assertNotEquals('3rd Dec 2022 12:00 PM', $formattedDate);
    }

    public function test_get_assigned_date_success(): void
    {
        $formattedDate = $this->studentResponse->getAssignedDate();
        $this->assertEquals('1st December 2022', $formattedDate);
    }

    public function test_get_assigned_date_fail(): void
    {
        $formattedDate = $this->studentResponse->getAssignedDate();
        $this->assertNotEquals('1st Dec 2022', $formattedDate);
    }
}
