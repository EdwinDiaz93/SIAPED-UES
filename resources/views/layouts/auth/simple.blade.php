<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-neutral-50 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div
            class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10"
            style="--color-accent:#960000; --color-accent-content:#960000; --color-accent-foreground:#ffffff; --color-primary:#960000; --color-on-primary:#ffffff;"
        >
            <div class="flex w-full max-w-md flex-col gap-2 rounded-2xl border border-neutral-200 bg-white p-8 shadow-lg">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 font-medium" wire:navigate>
                    <span class="flex h-32 w-32 items-center justify-center rounded-full bg-white p-3 shadow-md ring-1 ring-[#960000]/15">
                        <img src="{{ asset('images/logo-ues.jpg') }}" alt="Universidad de El Salvador" class="h-full w-full object-contain">
                    </span>
                    <span class="text-sm font-semibold tracking-wide text-[#960000]">
                        {{ config('app.name', 'SIAPED-UES') }}
                    </span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
