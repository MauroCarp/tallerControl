<?php

namespace App\Filament\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class CustomLogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // Redirigí al login del panel actual
        return redirect('/admin/login'); // Cambiá esto si estás en otro panel
    }
}
