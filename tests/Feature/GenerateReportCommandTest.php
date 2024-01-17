<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;


class GenerateReportCommandTest extends TestCase
{
    /** @test */
    public function check_generate_report_command_exists()
    {
        $commands = Artisan::all();
        $this->assertArrayHasKey('command:generate-report', $commands);
    }
}
