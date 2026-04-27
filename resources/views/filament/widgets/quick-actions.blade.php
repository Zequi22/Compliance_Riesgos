<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-x-3 py-1">
                <span class="text-base font-semibold text-gray-900 dark:text-white">
                    Accesos Directos
                </span>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 py-2">
            {{-- Registro General --}}
            <a href="/admin/risks"
                class="group flex items-center gap-5 p-5 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#3D3D3D] transition-all duration-300 hover:ring-2 hover:ring-orange-500 hover:border-transparent hover:shadow-lg">
                <div class="flex-shrink-0 p-3 rounded-lg bg-orange-50 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                    <x-filament::icon icon="heroicon-o-table-cells" class="h-7 w-7" />
                </div>
                <div class="flex-grow">
                    <h3 class="font-bold text-gray-900 dark:text-white leading-tight">Registro General</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Inventario completo de riesgos</p>
                </div>
            </a>

            {{-- Identificar Riesgo --}}
            <a href="/admin/risks/create"
                class="group flex items-center gap-5 p-5 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#3D3D3D] transition-all duration-300 hover:ring-2 hover:ring-orange-500 hover:border-transparent hover:shadow-lg">
                <div class="flex-shrink-0 p-3 rounded-lg bg-orange-50 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                    <x-filament::icon icon="heroicon-o-plus-circle" class="h-7 w-7" />
                </div>
                <div class="flex-grow">
                    <h3 class="font-bold text-gray-900 dark:text-white leading-tight">Identificar Riesgo</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Registrar nueva amenaza</p>
                </div>
            </a>

            {{-- Heatmap --}}
            <a href="/admin/heatmap"
                class="group flex items-center gap-5 p-5 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#3D3D3D] transition-all duration-300 hover:ring-2 hover:ring-orange-500 hover:border-transparent hover:shadow-lg">
                <div class="flex-shrink-0 p-3 rounded-lg bg-orange-50 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                    <x-filament::icon icon="heroicon-o-map" class="h-7 w-7" />
                </div>
                <div class="flex-grow">
                    <h3 class="font-bold text-gray-900 dark:text-white leading-tight">Heatmap</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Mapa de calor del nivel inherente</p>
                </div>
            </a>
            {{-- Evidencias  --}}
            <a href="/admin/evidences"
                class="group flex items-center gap-5 p-5 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#3D3D3D] transition-all duration-300 hover:ring-2 hover:ring-orange-500 hover:border-transparent hover:shadow-lg">
                <div class="flex-shrink-0 p-3 rounded-lg bg-orange-50 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                    <x-filament::icon icon="heroicon-o-document-text" class="h-7 w-7" />
                </div>
                <div class="flex-grow">
                    <h3 class="font-bold text-gray-900 dark:text-white leading-tight">Evidencias</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Subir evidencias</p>
                </div>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>