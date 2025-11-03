<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh min-w-dvw font-sans antialiased bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" class="bg-base-100 lg:bg-inherit">

            {{-- BRAND --}}
            <x-app-brand class="px-5 pt-4" />

            {{-- MENU --}}
            <x-menu activate-by-route>

                {{-- User Info --}}
                @if(session('logged_in'))
                    <x-menu-separator />

                    <div class="px-5 py-3 bg-base-200 rounded-lg mx-2 mb-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold">{{ session('username') }}</div>
                                <div class="text-xs text-base-content/60">Logged in</div>
                            </div>
                            <x-button
                                icon="o-power"
                                class="btn-circle btn-ghost btn-sm"
                                tooltip-left="Logout"
                                no-wire-navigate
                                link="/logout"
                            />
                        </div>
                    </div>

                    <x-menu-separator />
                @endif

                <x-menu-item title="Beranda" icon="o-home" link="{{ route('welcome') }}" exact />
                <x-menu-item title="Kasir" icon="o-calculator" link="{{ route('kasir') }}" exact />
                <x-menu-item title="Layanan" icon="o-squares-2x2" link="{{ route('layanan') }}" exact />
                <x-menu-item title="Jenis Pakaian" icon="o-tag" link="{{ route('jenis-pakaian') }}" exact />
                <x-menu-item title="Pelanggan" icon="o-users" link="{{ route('pelanggan') }}" exact />
                <x-menu-item title="Transaksi" icon="o-shopping-cart" link="{{ route('transaksi') }}" exact />

                <x-menu-separator />

                <x-menu-item title="Pengaturan" icon="o-cog-6-tooth" link="{{ route('pengaturan') }}" exact />

                {{-- <x-menu-sub title="Settings" icon="o-cog-6-tooth">
                    <x-menu-item title="Wifi" icon="o-wifi" link="####" />
                    <x-menu-item title="Archives" icon="o-archive-box" link="####" />
                </x-menu-sub> --}}
            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
</body>
</html>
