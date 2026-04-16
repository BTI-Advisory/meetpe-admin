<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use App\Models\UserRoles;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAdminUser extends CreateRecord
{
    protected static string $resource = AdminUserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = \App\Models\User::create([
            'name'                => $data['name'],
            'email'               => $data['email'],
            'password'            => $data['password'],
            'user_type'           => 'admin',
            'is_verified_account' => true,
            'otp_code'            => rand(1000, 9999),
            'fcm_token'           => '',
            'profile_path'        => '',
            'phone_number'        => '',
        ]);

        UserRoles::create([
            'user_id' => $user->id,
            'role_id' => 3,
        ]);

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
