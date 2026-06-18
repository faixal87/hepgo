<?php

namespace Tests\Feature;

use App\Models\VisitorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_public_visitor_is_counted_once_per_day_for_same_ip_and_browser(): void
    {
        $server = [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 Portal Visitor Test',
            'REMOTE_ADDR' => '203.0.113.10',
        ];

        $this->withServerVariables($server)->get('/')->assertOk();
        $this->withServerVariables($server)->get('/')->assertOk();

        $this->assertSame(1, VisitorLog::query()->count());

        $log = VisitorLog::query()->firstOrFail();

        $this->assertSame(2, $log->visit_count);
        $this->assertNotSame('203.0.113.10', $log->ip_hash);
        $this->assertSame('/', $log->first_path);
        $this->assertSame('/', $log->last_path);
    }

    public function test_bot_user_agent_is_not_counted_as_valid_visitor(): void
    {
        $this
            ->withServerVariables([
                'HTTP_USER_AGENT' => 'ExampleBot/1.0',
                'REMOTE_ADDR' => '203.0.113.11',
            ])
            ->get('/')
            ->assertOk();

        $this->assertDatabaseCount('visitor_logs', 0);
    }
}
