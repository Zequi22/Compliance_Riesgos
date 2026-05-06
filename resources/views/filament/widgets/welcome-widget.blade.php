<x-filament-widgets::widget>
    @php
        $user = $this->getUser();
        $greeting = $this->getGreeting();
    @endphp

    <div class="relative overflow-hidden rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#3D3D3D] p-6 shadow-sm">
        {{-- Decorative background elements --}}
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-orange-500/10 blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-blue-500/5 blur-3xl"></div>

        <div class="relative flex flex-col md:flex-row items-center gap-6">
            <div class="flex-shrink-0">
                <div class="h-20 w-20 rounded-full border-4 border-orange-500/20 p-1">
                    <img 
                        src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=E67E22&background=FFF7ED' }}" 
                        alt="{{ $user->name }}"
                        class="h-full w-full rounded-full object-cover shadow-sm"
                    >
                </div>
            </div>

            <div class="flex-grow text-center md:text-left">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    {{ $greeting }}, <span class="text-orange-600 dark:text-orange-500">{{ $user->name }}</span>
                </h1>
                
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                    Bienvenido de nuevo al panel de gestión de riesgos. 
                    @if($user->organizationalUnit)
                        Estás en el área de <span class="font-semibold text-gray-900 dark:text-white">{{ $user->organizationalUnit->name }}</span>.
                    @endif
                </p>

                <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4">
                    <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 dark:bg-white/5 text-sm text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-white/10">
                        <x-filament::icon icon="heroicon-m-calendar" class="h-4 w-4" />
                        <span>{{ now()->translatedFormat('l, d \d\e F') }}</span>
                    </div>

                    <form action="{{ filament()->getLogoutUrl() }}" method="post" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-950/30 text-sm font-medium text-orange-600 dark:text-orange-400 hover:bg-orange-600 hover:text-white transition-colors duration-300 border border-orange-200 dark:border-orange-900/50">
                            <x-filament::icon icon="heroicon-m-arrow-left-on-rectangle" class="h-4 w-4" />
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-filament-widgets::widget>
