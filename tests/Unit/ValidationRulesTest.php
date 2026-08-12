<?php

declare(strict_types=1);

namespace Tests\Unit;

use Core\ValidationRules;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ValidationRulesTest extends TestCase
{
    #[Test]
    public function itPassesWhenEveryRuleIsSatisfied(): void
    {
        $rules = new ValidationRules(
            ['title' => 'A title', 'status' => 'pending', 'expiration_date' => '2026-12-31'],
            ['title' => 'required|string|max:100', 'status' => 'in:pending,completed', 'expiration_date' => 'date']
        );

        $this->assertTrue($rules->verify());
        $this->assertSame([], $rules->errors());
    }

    #[Test]
    public function itCollectsEveryFailureForAField(): void
    {
        $rules = new ValidationRules(['name' => ''], ['name' => 'required|min:3']);

        $this->assertFalse($rules->verify());
        $this->assertSame([
            'The field name is required',
            'The field name must be at least 3 characters',
        ], $rules->errors()['name']);
    }

    #[Test]
    public function itValidatesEmails(): void
    {
        $rules = new ValidationRules(['email' => 'nope'], ['email' => 'email']);

        $this->assertFalse($rules->verify());
        $this->assertSame(['The field email must be a valid email'], $rules->errors()['email']);
    }

    #[Test]
    public function itValidatesAllowedValues(): void
    {
        $rules = new ValidationRules(['status' => 'archived'], ['status' => 'in:pending,completed']);

        $this->assertFalse($rules->verify());
        $this->assertSame(
            ['The field status must be one of: pending, completed'],
            $rules->errors()['status']
        );
    }

    #[Test]
    public function itIgnoresUnknownRules(): void
    {
        // Only required|string|email|min|max|in|date are implemented; anything
        // else is silently skipped rather than throwing.
        $rules = new ValidationRules(['age' => 'not a number'], ['age' => 'integer|between:1,10']);

        $this->assertTrue($rules->verify());
    }

    #[Test]
    public function itHandlesMissingFieldsWithoutRaisingDeprecations(): void
    {
        // With display_errors on (the dev server's default) a deprecation here
        // gets printed before the JSON body and breaks the response.
        $rules = new ValidationRules([], [
            'title' => 'required|string|max:200',
            'password' => 'required|min:7',
            'expiration_date' => 'required|date',
        ]);

        $this->assertFalse($rules->verify());
        $this->assertSame(['title', 'password', 'expiration_date'], array_keys($rules->errors()));
    }

    #[Test]
    public function validatedReturnsTheWholeInputNotOnlyRuledKeys(): void
    {
        $rules = new ValidationRules(
            ['title' => 'A title', 'is_admin' => true],
            ['title' => 'required']
        );

        $this->assertTrue($rules->verify());
        $this->assertSame(['title' => 'A title', 'is_admin' => true], $rules->validated());
    }
}
