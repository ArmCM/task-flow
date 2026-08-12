<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Policies\TaskPolicy;
use Core\Request;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TaskPolicyTest extends TestCase
{
    #[Test]
    public function viewAnyReturnsAnOwnerFilterForTheQuery(): void
    {
        $policy = new TaskPolicy();

        $this->assertSame(['user_id' => 7], $policy->viewAny($this->requestForUser(7)));
    }

    #[Test]
    public function itAllowsTheOwner(): void
    {
        $policy = new TaskPolicy();

        $this->assertTrue($policy->view(['user_id' => 7], $this->requestForUser(7)));
        $this->assertTrue($policy->update(['user_id' => 7], $this->requestForUser(7)));
        $this->assertTrue($policy->delete(['user_id' => 7], $this->requestForUser(7)));
    }

    #[Test]
    public function itDeniesEveryoneElse(): void
    {
        $policy = new TaskPolicy();

        $this->assertFalse($policy->view(['user_id' => 8], $this->requestForUser(7)));
        $this->assertFalse($policy->update(['user_id' => 8], $this->requestForUser(7)));
        $this->assertFalse($policy->delete(['user_id' => 8], $this->requestForUser(7)));
    }

    #[Test]
    public function itDeniesWhenThereIsNoAuthenticatedUser(): void
    {
        $policy = new TaskPolicy();

        $request = new Request(server: [], get: [], post: [], inputJson: [], files: []);

        $this->assertFalse($policy->view(['user_id' => 7], $request));
    }

    #[Test]
    public function itComparesOwnershipStrictly(): void
    {
        // Documented on purpose: the check is ===, so a string user_id (what a
        // driver returning stringified columns would give) is denied.
        $policy = new TaskPolicy();

        $this->assertFalse($policy->view(['user_id' => '7'], $this->requestForUser(7)));
    }

    private function requestForUser(int $id): Request
    {
        $request = new Request(server: [], get: [], post: [], inputJson: [], files: []);

        $request->setUser(['user_id' => $id, 'email' => 'test@example.com']);

        return $request;
    }
}
