<x-layouts::app :title="__('Dashboard')">

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">

        @php
            $inactivosCount = \App\Models\User::role('inactivo')->count();
            $promocionesAplicadasCount = \App\Models\SolicitudPromocion::where('estado', 'aprobada')->count();
            $atestadosPendientesCount = collect([
                \App\Models\CredencialCapacitacion::class,
                \App\Models\CredencialProyeccionSocial::class,
                \App\Models\CredencialEspecializacion::class,
                \App\Models\CredencialInvestigacion::class,
                \App\Models\CredencialSeguimiento::class,
            ])->sum(fn ($modelo) => $modelo::where('estado', 'pendiente')->count());
        @endphp

        <div class="flex flex-wrap gap-4">
            <div class="w-full max-w-sm rounded-xl border border-outline bg-white p-5 shadow-sm dark:border-outline-dark dark:bg-zinc-800">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#960000]/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="#960000" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Cuentas pendientes de aprobación</p>
                        <p class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ $inactivosCount }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                    Usuarios registrados que aún no han sido aprobados como docentes.
                </p>
                <a href="{{ route('manage.users') }}" wire:navigate
                    class="mt-4 inline-block text-sm font-medium text-[#960000] hover:underline">
                    Ver usuarios &rarr;
                </a>
            </div>

            <div class="w-full max-w-sm rounded-xl border border-outline bg-white p-5 shadow-sm dark:border-outline-dark dark:bg-zinc-800">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#960000]/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="#960000" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Promociones aplicadas</p>
                        <p class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ $promocionesAplicadasCount }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                    Solicitudes de promoción escalafonaria aprobadas.
                </p>
                <a href="{{ route('promociones') }}" wire:navigate
                    class="mt-4 inline-block text-sm font-medium text-[#960000] hover:underline">
                    Ver promociones &rarr;
                </a>
            </div>

            <div class="w-full max-w-sm rounded-xl border border-outline bg-white p-5 shadow-sm dark:border-outline-dark dark:bg-zinc-800">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#960000]/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="#960000" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Atestados pendientes de aprobar</p>
                        <p class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ $atestadosPendientesCount }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                    Credenciales de docentes a la espera de revisión.
                </p>
                <a href="{{ route('credenciales.revision') }}" wire:navigate
                    class="mt-4 inline-block text-sm font-medium text-[#960000] hover:underline">
                    Ver atestados &rarr;
                </a>
            </div>
        </div>

    </div>
</x-layouts::app>
