<?php

declare(strict_types=1);

use WendellAdriel\Lift\Tests\Datasets\Post;
use WendellAdriel\Lift\Tests\Datasets\Role;
use WendellAdriel\Lift\Tests\Datasets\User;

it('loads BelongsTo relation', function () {
    $user = User::create([
        'name' => fake()->name,
        'email' => fake()->unique()->safeEmail,
        'password' => 's3Cr3T@!!!',
    ]);

    $post = Post::create([
        'title' => fake()->sentence,
        'content' => fake()->paragraph,
    ]);

    $post->user()->associate($user);
    $post->save();
    expect($post->user->id)->toBe($user->id);

    $post = Post::query()->find($post->id);
    expect($post->user->id)->toBe($user->id);

    $postWithoutUser = Post::create([
        'title' => fake()->sentence,
        'content' => fake()->paragraph,
    ]);

    expect($postWithoutUser->user)->toBeNull();

    $postWithoutUser = Post::query()->find($postWithoutUser->id);
    expect($postWithoutUser->user)->toBeNull();
});

it('loads BelongsToMany relation', function () {
    $user = User::create([
        'name' => fake()->name,
        'email' => fake()->unique()->safeEmail,
        'password' => 's3Cr3T@!!!',
    ]);

    $role = Role::create();
    $user->roles()->attach($role);

    expect($user->roles)->toHaveCount(1)
        ->and($user->roles->first()->id)->toBe($role->id)
        ->and($role->users)->toHaveCount(1)
        ->and($role->users->first()->id)->toBe($user->id);

    $user = User::query()->find($user->id);
    expect($user->roles)->toHaveCount(1)
        ->and($user->roles->first()->id)->toBe($role->id);

    $role = Role::query()->find($role->id);
    expect($role->users)->toHaveCount(1)
        ->and($role->users->first()->id)->toBe($user->id);

    $userWithoutRoles = User::create([
        'name' => fake()->name,
        'email' => fake()->unique()->safeEmail,
        'password' => 's3Cr3T@!!!',
    ]);
    expect($userWithoutRoles->roles)->toHaveCount(0);

    $userWithoutRoles = User::query()->find($userWithoutRoles->id);
    expect($userWithoutRoles->roles)->toHaveCount(0);

    $roleWithoutUsers = Role::create();
    expect($roleWithoutUsers->users)->toHaveCount(0);

    $roleWithoutUsers = Role::query()->find($roleWithoutUsers->id);
    expect($roleWithoutUsers->users)->toHaveCount(0);
});

it('loads HasMany relation', function () {
    $user = User::create([
        'name' => fake()->name,
        'email' => fake()->unique()->safeEmail,
        'password' => 's3Cr3T@!!!',
    ]);

    $post = Post::create([
        'title' => fake()->sentence,
        'content' => fake()->paragraph,
    ]);
    $user->posts()->save($post);

    expect($user->posts)->toHaveCount(1)
        ->and($user->posts->first()->id)->toBe($post->id)
        ->and($post->user->id)->toBe($user->id);

    $user = User::query()->find($user->id);
    expect($user->posts)->toHaveCount(1)
        ->and($user->posts->first()->id)->toBe($post->id);

    $post = Post::query()->find($post->id);
    expect($post->user->id)->toBe($user->id);

    $userWithoutPosts = User::create([
        'name' => fake()->name,
        'email' => fake()->unique()->safeEmail,
        'password' => 's3Cr3T@!!!',
    ]);

    expect($userWithoutPosts->posts)->toHaveCount(0);
});
