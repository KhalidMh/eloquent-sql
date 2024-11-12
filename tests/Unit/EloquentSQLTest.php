<?php

use KhalidMh\EloquentSQL\EloquentSQL;
use KhalidMh\EloquentSQL\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a new User instance
    User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'remember_token' => null,
    ]);

    // Retrieve the first user from the database
    $this->user = User::first();
});

it('generates an SQL insert query string for the model', function () {
    // Set the model for EloquentSQL
    $eloquentSQL = EloquentSQL::setModel($this->user);

    // Generate the SQL query
    $sql = $eloquentSQL->toQuery();

    // Expected SQL query
    $expectedSql = 'INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (1, "John Doe", "john@example.com", NULL, "password", NULL, "' . $this->user->created_at->format('Y-m-d H:i:s') . '", "' . $this->user->updated_at->format('Y-m-d H:i:s') . '");';

    // Assert that the generated SQL query matches the expected SQL query
    expect($sql)->toBe($expectedSql);
});

it('excludes specified columns from the query', function () {
    // Set the model for EloquentSQL
    $eloquentSQL = EloquentSQL::setModel($this->user);

    // Exclude the 'remember_token' and 'created_at' columns
    $eloquentSQL->except(['remember_token', 'created_at']);

    // Generate the SQL query
    $sql = $eloquentSQL->toQuery();

    // Expected SQL query
    $expectedSql = 'INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `updated_at`) VALUES (1, "John Doe", "john@example.com", NULL, "password", "' . $this->user->updated_at->format('Y-m-d H:i:s') . '");';

    // Assert that the generated SQL query matches the expected SQL query
    expect($sql)->toBe($expectedSql);
});

it('includes only specified columns in the query', function () {
    // Set the model for EloquentSQL
    $eloquentSQL = EloquentSQL::setModel($this->user);

    // Include only the 'name' and 'email' columns
    $eloquentSQL->only(['name', 'email']);

    // Generate the SQL query
    $sql = $eloquentSQL->toQuery();

    // Expected SQL query
    $expectedSql = 'INSERT INTO `users` (`name`, `email`) VALUES ("John Doe", "john@example.com");';

    // Assert that the generated SQL query matches the expected SQL query
    expect($sql)->toBe($expectedSql);
});
