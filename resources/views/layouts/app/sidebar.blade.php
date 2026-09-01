<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Menu')" class="grid">
                @if (auth()->user()->hasAnyRole(['admin', 'comite']))
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                        wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                @endif
                @if (auth()->user()->can('account.details'))
                    @can('account.details')
                        <flux:sidebar.item icon="home" :href="route('account.details')"
                            :current="request()->routeIs('account.details')" wire:navigate>
                            {{ __('Datos de cuenta') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('manage.users')
                        <flux:sidebar.item icon="user" :href="route('manage.users')"
                            :current="request()->routeIs('manage.users')" wire:navigate>
                            {{ __('Administrar Usuarios') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('manage.periodos')
                        <flux:sidebar.item icon="calendar" :href="route('manage.periodos')"
                            :current="request()->routeIs('manage.periodos')" wire:navigate>
                            {{ __('Periodos de Evaluación') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('manage.evaluaciones')
                        <flux:sidebar.item icon="clipboard-document-check" :href="route('manage.evaluaciones')"
                            :current="request()->routeIs('manage.evaluaciones', 'evaluaciones.cuestionario')" wire:navigate>
                            {{ __('Evaluaciones') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('manage.users')
                        <flux:sidebar.item icon="clipboard-document-list" :href="route('credenciales.revision')"
                            :current="request()->routeIs('credenciales.revision', 'credenciales')" wire:navigate>
                            {{ __('Revisión de Credenciales') }}
                        </flux:sidebar.item>
                    @endcan
                    @cannot('manage.users')
                        @can('fill.credenciales')
                            <flux:sidebar.item icon="academic-cap" :href="route('credenciales')"
                                :current="request()->routeIs('credenciales')" wire:navigate>
                                {{ __('Mis Credenciales') }}
                            </flux:sidebar.item>
                        @endcan
                        @can('fill.credenciales')
                            <flux:sidebar.item icon="document-chart-bar" :href="route('formulario.show', ['docenteId' => auth()->id()])"
                                :current="request()->routeIs('formulario.show')" wire:navigate>
                                {{ __('Mi Formulario') }}
                            </flux:sidebar.item>
                        @endcan
                        @can('fill.cuestionario.auto')
                            <flux:sidebar.item icon="pencil-square" :href="route('evaluaciones.cuestionario')"
                                :current="request()->routeIs('evaluaciones.cuestionario')" wire:navigate>
                                {{ __('Mi Autoevaluación') }}
                            </flux:sidebar.item>
                        @endcan
                    @endcannot
                    @can('manage.reportes')
                        <flux:sidebar.item icon="chart-bar" :href="route('reportes')"
                            :current="request()->routeIs('reportes')" wire:navigate>
                            {{ __('Reportes') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('manage.promociones')
                        <flux:sidebar.item icon="arrow-trending-up" :href="route('promociones')"
                            :current="request()->routeIs('promociones')" wire:navigate>
                            {{ __('Promociones') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('reportes.promocion')
                        <flux:sidebar.item icon="document-chart-bar" :href="route('reportes.promocion')"
                            :current="request()->routeIs('reportes.promocion')" wire:navigate>
                            {{ __('Docentes Aptos para Promoción') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('reportes.atestados')
                        <flux:sidebar.item icon="table-cells" :href="route('reportes.atestados')"
                            :current="request()->routeIs('reportes.atestados')" wire:navigate>
                            {{ __('Listado de Atestados') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('manage.catalogos')
                        <flux:sidebar.item icon="rectangle-stack" :href="route('manage.catalogos')"
                            :current="request()->routeIs('manage.catalogos')" wire:navigate>
                            {{ __('Catálogos') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('manage.auditoria')
                        <flux:sidebar.item icon="clock" :href="route('manage.auditoria')"
                            :current="request()->routeIs('manage.auditoria')" wire:navigate>
                            {{ __('Bitácora') }}
                        </flux:sidebar.item>
                    @endcan
                @endif
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />



        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
