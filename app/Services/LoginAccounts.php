<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Throwable;

class LoginAccounts
{
    public function ready(): bool
    {
        try {
            return Schema::hasColumns('users', ['id', 'name', 'email', 'password', 'role', 'remember_token']);
        } catch (Throwable) {
            return false;
        }
    }

    public function destination(?string $role): ?string
    {
        return match (strtolower(trim($role ?? ''))) {
            'admin', 'administrator' => '/dashboard',
            'director' => '/director',
            'staff', 'staff caregiver', 'caregiver' => '/dashboard/staff',
            'volunteer' => '/#events',
            default => null,
        };
    }
}
