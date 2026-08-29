<x-layouts::app :title="__('Dashboard')">

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">

        @php
            $inactivosCount = \App\Models\User::role('inactivo')->count();
        @endphp

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

    </div>
</x-layouts::app>
