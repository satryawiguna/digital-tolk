<?php

namespace Tests\Unit;

use DTApi\Helpers\TeHelper;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use DTApi\Models\User;

class UserRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_can_create_user(): void
    {
        $requestData = [
            'role' => 'customer',
            'name' => 'Satrya Wiguna',
            'company_id' => 1,
            'department_id' => 1,
            'email' => 'satrya@example.com',
            'dob_or_orgid' => '1980-01-01',
            'phone' => '123456789',
            'mobile' => '987654321'
        ];

        $user = new User();

        $result = $user->createOrUpdate(null, $requestData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($requestData['name'], $result->name);

        $this->assertDatabaseHas('users', ['email' => 'satrya@example.com']);
    }

    public function test_it_can_update_user(): void
    {
        $existingUser = factory(User::class)->create();

        $requestData = [
            'role' => 'customer',
            'name' => 'Satrya Wiguna',
            'company_id' => 1,
            'department_id' => 1,
            'email' => 'satrya@example.com',
            'dob_or_orgid' => '1980-01-01',
            'phone' => '123456789',
            'mobile' => '987654321'
        ];

        $user = new User();

        $result = $user->createOrUpdate($existingUser->id, $requestData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($requestData['name'], $result->name);

        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }
}
