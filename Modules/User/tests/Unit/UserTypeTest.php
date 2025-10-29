<?php

namespace Modules\User\Tests\Unit;

use Modules\User\Enums\UserType;
use PHPUnit\Framework\TestCase;

class UserTypeTest extends TestCase
{
    public function test_user_type_enum_values(): void
    {
        $this->assertEquals('super-admin', UserType::SuperAdmin->value);
        $this->assertEquals('admin', UserType::Admin->value);
        $this->assertEquals('student', UserType::Student->value);
        $this->assertEquals('driver', UserType::Driver->value);
    }

    public function test_user_type_labels(): void
    {
        $this->assertEquals('Super Administrador', UserType::SuperAdmin->label());
        $this->assertEquals('Administrador', UserType::Admin->label());
        $this->assertEquals('Estudante', UserType::Student->label());
        $this->assertEquals('Motorista', UserType::Driver->label());
    }

    public function test_can_create_admin_users(): void
    {
        $this->assertTrue(UserType::SuperAdmin->canCreateAdminUsers());
        $this->assertTrue(UserType::Admin->canCreateAdminUsers());
        $this->assertFalse(UserType::Student->canCreateAdminUsers());
        $this->assertFalse(UserType::Driver->canCreateAdminUsers());
    }

    public function test_public_registration_types(): void
    {
        $publicTypes = UserType::publicRegistrationTypes();
        $this->assertCount(1, $publicTypes);
        $this->assertContains(UserType::Student, $publicTypes);
    }

    public function test_admin_only_types(): void
    {
        $adminTypes = UserType::adminOnlyTypes();
        $this->assertCount(2, $adminTypes);
        $this->assertContains(UserType::Admin, $adminTypes);
        $this->assertContains(UserType::Driver, $adminTypes);
    }
}
