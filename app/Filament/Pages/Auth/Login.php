<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Enums\Status;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Validation\ValidationException;

class Login extends BasePage
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();

        } elseif ($user->latestStatus->status === Status::SUSPENDED) {
            $this->logOutAndThrowException($user->latestStatus->status);

        } elseif (in_array($user->client?->latestStatus->status, [Status::SUSPENDED, Status::EXPIRED])) {
            $this->logOutAndThrowException($user->client?->latestStatus->status);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    private function logOutAndThrowException(Status $status): never
    {
        Filament::auth()->logout();
        throw ValidationException::withMessages([
            'data.email' => $status->errorMessage(),
        ]);
    }
}
