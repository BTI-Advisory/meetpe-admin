<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function getView(): string
    {
        return 'filament.pages.auth.login';
    }
}
