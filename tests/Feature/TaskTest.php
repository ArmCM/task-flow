<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\ApiTestCase;
use Tests\Support\TestDatabase;

final class TaskTest extends ApiTestCase
{
    #[Test]
    public function itListsOnlyTheTasksOfTheAuthenticatedUser(): void
    {
        $otherUserId = TestDatabase::insertUser('Other', 'other@example.com', 'secret123');

        TestDatabase::insertTask(['title' => 'Mine', 'user_id' => $this->userId]);
        TestDatabase::insertTask(['title' => 'Theirs', 'user_id' => $otherUserId]);

        $response = $this->api->get('/tasks');

        $this->assertSame(200, $response->status);
        $this->assertSame(['Mine'], array_column($response->data(), 'title'));
    }

    #[Test]
    public function itFiltersByStatus(): void
    {
        TestDatabase::insertTask(['title' => 'Pending one', 'user_id' => $this->userId]);
        TestDatabase::insertTask(['title' => 'Done one', 'status' => 'completed', 'user_id' => $this->userId]);

        $response = $this->api->get('/tasks', ['status' => 'completed']);

        $this->assertSame(200, $response->status);
        $this->assertSame(['Done one'], array_column($response->data(), 'title'));
    }

    #[Test]
    public function itPaginatesTheList(): void
    {
        foreach (range(1, 5) as $number) {
            TestDatabase::insertTask(['title' => "Task $number", 'user_id' => $this->userId]);
        }

        $response = $this->api->get('/tasks', ['page' => 1, 'per_page' => 2]);

        $this->assertSame(200, $response->status);
        $this->assertCount(2, $response->data());

        $meta = $response->meta();

        $this->assertSame(1, $meta['current_page']);
        $this->assertSame(2, $meta['per_page']);
    }

    #[Test]
    public function itShowsATaskOwnedByTheUser(): void
    {
        $id = TestDatabase::insertTask(['title' => 'Readable', 'user_id' => $this->userId]);

        $response = $this->api->get("/tasks/$id");

        $this->assertSame(200, $response->status);
        $this->assertSame('Readable', $response->data()['title']);
        $this->assertSame($this->userId, $response->data()['user_id']);
    }

    #[Test]
    public function itForbidsReadingATaskOwnedBySomeoneElse(): void
    {
        $otherUserId = TestDatabase::insertUser('Other', 'other@example.com', 'secret123');
        $id = TestDatabase::insertTask(['user_id' => $otherUserId]);

        $response = $this->api->get("/tasks/$id");

        $this->assertSame(403, $response->status);
        $this->assertSame('You cannot view this task', $response->message());
    }

    #[Test]
    public function itReturns404ForATaskThatDoesNotExist(): void
    {
        $response = $this->api->get('/tasks/99999');

        $this->assertSame(404, $response->status);
        $this->assertSame('Resource not found', $response->message());
    }

    #[Test]
    public function itCreatesATask(): void
    {
        $response = $this->api->post('/tasks', [
            'title' => 'Write tests',
            'description' => 'Cover the API end to end',
            'expiration_date' => '2026-12-31',
        ]);

        $this->assertSame(201, $response->status);
        $this->assertSame('Task created successfully', $response->message());

        $stored = TestDatabase::connection()
            ->query('SELECT title, description, status, user_id FROM tasks')
            ->fetchAll();

        $this->assertSame([[
            'title' => 'Write tests',
            'description' => 'Cover the API end to end',
            'status' => 'pending',
            'user_id' => $this->userId,
        ]], $stored);
    }

    #[Test]
    public function itIgnoresAUserIdSentInTheBodyWhenCreating(): void
    {
        $otherUserId = TestDatabase::insertUser('Other', 'other@example.com', 'secret123');

        $response = $this->api->post('/tasks', [
            'title' => 'Planted',
            'description' => 'Trying to create a task for someone else',
            'expiration_date' => '2026-12-31',
            'user_id' => $otherUserId,
        ]);

        $this->assertSame(201, $response->status);

        $owner = TestDatabase::connection()->query('SELECT user_id FROM tasks')->fetchColumn();

        $this->assertSame($this->userId, (int) $owner);
    }

    #[Test]
    public function itRejectsAnInvalidTask(): void
    {
        $response = $this->api->post('/tasks', [
            'title' => '',
            'description' => str_repeat('x', 201),
            'expiration_date' => 'not-a-date',
        ]);

        $this->assertSame(422, $response->status);
        $this->assertSame(['title', 'description', 'expiration_date'], array_keys($response->errors()));
        $this->assertSame(0, (int) TestDatabase::connection()->query('SELECT COUNT(*) FROM tasks')->fetchColumn());
    }

    #[Test]
    public function itUpdatesATask(): void
    {
        $id = TestDatabase::insertTask(['title' => 'Before', 'user_id' => $this->userId]);

        $response = $this->api->patch("/tasks/$id", [
            'title' => 'After',
            'description' => 'Updated description',
            'status' => 'completed',
            'expiration_date' => '2027-01-15',
        ]);

        $this->assertSame(200, $response->status);

        $task = TestDatabase::findTask($id);

        $this->assertSame('After', $task['title']);
        $this->assertSame('completed', $task['status']);
        $this->assertSame('2027-01-15', $task['expiration_date']);
        $this->assertSame($this->userId, (int) $task['user_id']);
    }

    #[Test]
    public function itDoesNotLetAnUpdateReassignOwnership(): void
    {
        $otherUserId = TestDatabase::insertUser('Other', 'other@example.com', 'secret123');
        $id = TestDatabase::insertTask(['user_id' => $this->userId]);

        $response = $this->api->patch("/tasks/$id", [
            'title' => 'Given away',
            'description' => 'Trying to hand the task to someone else',
            'status' => 'pending',
            'expiration_date' => '2027-01-15',
            'user_id' => $otherUserId,
        ]);

        $this->assertSame(200, $response->status);
        $this->assertSame($this->userId, (int) TestDatabase::findTask($id)['user_id']);
    }

    #[Test]
    public function itRejectsAnInvalidStatusOnUpdate(): void
    {
        $id = TestDatabase::insertTask(['user_id' => $this->userId]);

        $response = $this->api->patch("/tasks/$id", [
            'title' => 'Whatever',
            'description' => 'Whatever',
            'status' => 'archived',
            'expiration_date' => '2027-01-15',
        ]);

        $this->assertSame(422, $response->status);
        $this->assertArrayHasKey('status', $response->errors());
        $this->assertSame('pending', TestDatabase::findTask($id)['status']);
    }

    #[Test]
    public function itForbidsUpdatingATaskOwnedBySomeoneElse(): void
    {
        $otherUserId = TestDatabase::insertUser('Other', 'other@example.com', 'secret123');
        $id = TestDatabase::insertTask(['title' => 'Untouched', 'user_id' => $otherUserId]);

        $response = $this->api->patch("/tasks/$id", [
            'title' => 'Hijacked',
            'description' => 'Hijacked',
            'status' => 'completed',
            'expiration_date' => '2027-01-15',
        ]);

        $this->assertSame(403, $response->status);
        $this->assertSame('Untouched', TestDatabase::findTask($id)['title']);
    }

    #[Test]
    public function itDeletesATask(): void
    {
        $id = TestDatabase::insertTask(['user_id' => $this->userId]);

        $response = $this->api->delete("/tasks/$id");

        $this->assertSame(204, $response->status);
        $this->assertFalse(TestDatabase::findTask($id));
    }

    #[Test]
    public function itForbidsDeletingATaskOwnedBySomeoneElse(): void
    {
        $otherUserId = TestDatabase::insertUser('Other', 'other@example.com', 'secret123');
        $id = TestDatabase::insertTask(['user_id' => $otherUserId]);

        $response = $this->api->delete("/tasks/$id");

        $this->assertSame(403, $response->status);
        $this->assertNotFalse(TestDatabase::findTask($id));
    }
}
