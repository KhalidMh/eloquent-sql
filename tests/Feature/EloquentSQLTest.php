<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use KhalidMh\EloquentSQL\EloquentSQL;
use KhalidMh\EloquentSQL\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a new User instance
    User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'remember_token' => null,
        'created_at' => '2021-01-01 00:00:00',
        'updated_at' => '2021-01-01 15:15:15',
    ]);

    // Retrieve the first user from the database
    $this->user = User::firstOrFail();
});

it('generates an SQL insert query string for the model', function () {
    // Set the model for EloquentSQL
    $sql = EloquentSQL::set($this->user)->toQuery();

    // Expected SQL query
    $expectedSql = 'INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `created_at`, `updated_at`) VALUES (1, "John Doe", "john@example.com", NULL, "2021-01-01 00:00:00", "2021-01-01 15:15:15");';

    // Assert that the generated SQL query matches the expected SQL query
    expect($sql)->toBeString()->toBe($expectedSql);
});

it('includes hidden columns in the query', function () {
    // Set the model for EloquentSQL
    $sql = EloquentSQL::set($this->user)->includeHidden()->toQuery();

    // Expected SQL query
    $expectedSql = 'INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (1, "John Doe", "john@example.com", NULL, "password", NULL, "2021-01-01 00:00:00", "2021-01-01 15:15:15");';

    // Assert that the generated SQL query matches the expected SQL query
    expect($sql)->toBeString()->toBe($expectedSql);
});

it('excludes specified columns from the query', function () {
    // Set the model for EloquentSQL
    $sql = EloquentSQL::set($this->user)->except(['remember_token', 'created_at'])->toQuery();

    // Expected SQL query
    $expectedSql = 'INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `updated_at`) VALUES (1, "John Doe", "john@example.com", NULL, "2021-01-01 15:15:15");';

    // Assert that the generated SQL query matches the expected SQL query
    expect($sql)->toBeString()->toBe($expectedSql);
});

it('includes only specified columns in the query', function () {
    // Set the model for EloquentSQL
    $sql = EloquentSQL::set($this->user)->only(['name', 'email'])->toQuery();

    // Expected SQL query
    $expectedSql = 'INSERT INTO `users` (`name`, `email`) VALUES ("John Doe", "john@example.com");';

    // Assert that the generated SQL query matches the expected SQL query
    expect($sql)->toBeString()->toBe($expectedSql);
});
