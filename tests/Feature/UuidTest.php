<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use RuntimeException;
use stdClass;
use Tests\TestCase;

final class UuidTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_persists_users_and_their_tokens_with_uuid_keys(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test');

        $this->assertTrue(Str::isUuid($user->id, version: 7));
        $this->assertTrue(Str::isUuid($token->accessToken->getKey(), version: 7));
        $this->assertModelExists($user);
        $this->assertModelExists($token->accessToken);
        $this->assertSame($user->id, $token->accessToken->tokenable_id);
        $owner = $token->accessToken->tokenable;
        $this->assertNotNull($owner);
        $this->assertSame($user->id, $owner->getKey());
        $this->assertSame($token->accessToken->getKey(), PersonalAccessToken::findToken($token->plainTextToken)?->getKey());
    }

    #[TestWith(['not-a-uuid|secret'])]
    #[TestWith(['123|secret'])]
    #[TestWith(['|secret'])]
    #[TestWith(['019a0000-0000-7000-8000-000000000000|secret'])]
    public function test_returns_401_for_a_bearer_token_with_an_invalid_or_unknown_id(string $token): void
    {
        $response = $this->withToken($token)->getJson('/api/user');

        $response->assertUnauthorized();
    }

    public function test_stores_a_uuid_user_reference_in_sessions(): void
    {
        $user = User::factory()->create();

        DB::table('sessions')->insert([
            'id' => 'session-id',
            'user_id' => $user->id,
            'payload' => '',
            'last_activity' => 0,
        ]);

        $this->assertDatabaseHas('sessions', ['id' => 'session-id', 'user_id' => $user->id]);
    }

    public function test_queues_releases_and_deletes_jobs_with_uuid_keys(): void
    {
        $queue = Queue::connection('database');

        $id = $queue->pushRaw('{}');

        $this->assertTrue(Str::isUuid($id, version: 7));
        $job = $queue->pop();
        $this->assertNotNull($job);
        $this->assertSame($id, $job->getJobId());
        $job->release();
        $retriedJob = $queue->pop();
        $this->assertNotNull($retriedJob);
        $this->assertTrue(Str::isUuid($retriedJob->getJobId(), version: 7));
        $this->assertSame(2, $retriedJob->attempts());
        $retriedJob->delete();
        $this->assertDatabaseEmpty('jobs');
    }

    public function test_stores_and_retrieves_job_batches_with_uuid_keys(): void
    {
        $batch = Bus::batch([])->dispatch();

        $storedBatch = Bus::findBatch($batch->id);

        $this->assertTrue(Str::isUuid($batch->id));
        $this->assertNotNull($storedBatch);
        $this->assertSame($batch->id, $storedBatch->id);
    }

    public function test_logs_and_forgets_failed_jobs_with_uuid_keys(): void
    {
        $failedJobs = resolve('queue.failer');
        $uuid = '019a0000-0000-7000-8000-000000000000';

        $id = $failedJobs->log('database', 'default', json_encode(['uuid' => $uuid], JSON_THROW_ON_ERROR), new RuntimeException('Test failure'));

        $this->assertSame($uuid, $id);
        $this->assertDatabaseHas('failed_jobs', ['uuid' => $uuid]);
        $this->assertTrue(Str::isUuid(DB::table('failed_jobs')->sole()->id, version: 7));
        $failedJob = $failedJobs->find($id);
        $this->assertInstanceOf(stdClass::class, $failedJob);
        $this->assertSame($uuid, $failedJob->id);
        $failedJobs->forget($id);
        $this->assertDatabaseEmpty('failed_jobs');
    }
}
