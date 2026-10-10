<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Mail\ReactivacionSolicitadaMail;
use App\Support\AuthThrottle;
use Illuminate\Support\Facades\Mail;

new #[Layout('layouts::auth')] #[Title('Solicitar reactivación de cuenta')] class extends Component {

    public string $email = '';
    public bool $enviado = false;

    public function solicitar(): void
    {
        $this->validate([
            'email' => 'required|email',
        ], [], [
            'email' => 'correo',
        ]);

        $usuario = User::where('email', $this->email)->first();

        if ($usuario && ! $usuario->is_active) {
            // Limpia el contador de intentos de login de este email+IP: la
            // solicitud llega desde el mismo navegador que usará para volver
            // a iniciar sesión una vez que el administrador reactive la cuenta.
            AuthThrottle::clear($usuario->email, request()->ip());

            // Se notifica al primer administrador registrado en el sistema.
            $admin = User::role('admin')->orderBy('id')->first();

            if ($admin) {
                try {
                    Mail::to($admin->email)->send(new ReactivacionSolicitadaMail($usuario));
                } catch (\Throwable) {
                    // No bloquea el flujo si el correo falla.
                }
            }
        }

        // Mensaje genérico siempre, sin importar si la cuenta existe o está
        // activa, para no revelar si un correo está o no registrado.
        $this->enviado = true;
    }
};
?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Reactivar cuenta')"
        :description="__('Si tu cuenta fue deshabilitada, solicita aquí que un administrador la reactive')" />

    @if ($enviado)
        <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-sm text-green-800">
            Si el correo corresponde a una cuenta deshabilitada, hemos notificado al administrador para que revise tu solicitud.
        </div>
        <div class="text-center text-sm">
            <flux:link :href="route('login')" wire:navigate>{{ __('Volver a iniciar sesión') }}</flux:link>
        </div>
    @else
        <form wire:submit="solicitar" class="flex flex-col gap-6">
            <flux:input
                wire:model="email"
                label="{{ __('Correo electrónico') }}"
                type="email"
                required
                autofocus
                placeholder="email@example.com"
            />

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Solicitar reactivación') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>{{ __('O, vuelve a') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('iniciar sesión') }}</flux:link>
        </div>
    @endif
</div>
