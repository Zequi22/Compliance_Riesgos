<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Filtros --}}
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl p-6 border border-gray-200 dark:border-white/5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div class="md:col-span-1">
                    <label class="fi-fo-field-label block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Fecha Desde</label>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="date"
                            wire:model.live="date_from"
                            class="font-medium" />
                    </x-filament::input.wrapper>
                </div>
                <div class="md:col-span-1">
                    <label class="fi-fo-field-label block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Fecha Hasta</label>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="date"
                            wire:model.live="date_to"
                            class="font-medium" />
                    </x-filament::input.wrapper>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <x-filament::button
                        wire:click="$refresh"
                        icon="heroicon-m-arrow-path"
                        color="primary"
                        class="shadow-md text-white">
                        Actualizar Informe
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- KPI Grid Vitaminado --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
            $statCards = [
            ['label' => 'Riesgos Totales', 'value' => $kpis['total_risks'], 'color' => 'blue', 'icon' => 'heroicon-s-shield-check'],
            ['label' => 'Críticos / Altos', 'value' => $kpis['critical_risks'], 'color' => 'danger', 'icon' => 'heroicon-s-exclamation-triangle'],
            ['label' => 'Rev. Vencidas', 'value' => $kpis['overdue_reviews'], 'color' => 'warning', 'icon' => 'heroicon-s-clock'],
            ['label' => 'Acc. Venc./Bloq.', 'value' => $kpis['overdue_blocked_actions'], 'color' => 'orange', 'icon' => 'heroicon-s-no-symbol'],
            ['label' => 'Ctrl. Insuf.', 'value' => $kpis['insufficient_controls'], 'color' => 'rose', 'icon' => 'heroicon-s-shield-exclamation'],
            ['label' => 'Evid. Pend.', 'value' => $kpis['pending_evidences'], 'color' => 'purple', 'icon' => 'heroicon-s-document-magnifying-glass'],
            ];
            @endphp

            @foreach($statCards as $card)
            @php
            $colors = match($card['color']) {
            'blue' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
            'danger' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
            'warning' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
            'orange' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20',
            'rose' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
            'purple' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20',
            default => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20',
            };
            @endphp
            <div class="relative overflow-hidden group shadow-sm rounded-2xl p-5 border {{ $colors }} transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                <div class="flex flex-col items-center relative z-10">
                    <x-filament::icon
                        :icon="$card['icon']"
                        class="h-8 w-8 mb-3 transition-transform group-hover:scale-110" />
                    <div class="text-3xl font-black tracking-tight leading-none mb-1">{{ $card['value'] }}</div>
                    <div class="text-[9px] uppercase tracking-widest font-bold opacity-80 text-center">{{ $card['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Secciones de Reporte --}}
        <div class="space-y-12">

            {{-- 1. TOP RIESGOS --}}
            <div>
                <div class="flex items-center justify-between mb-4 border-l-4 border-red-500 pl-4 py-1">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white tracking-tight">1. TOP RIESGOS POR CRITICIDAD</h3>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Ordenados por impacto residual/inherente</p>
                    </div>
                    <x-filament::badge color="danger" size="md" class="px-3 rounded-full">{{ $topRisks->count() }} RIESGOS</x-filament::badge>
                </div>
                <div class="fi-ta-content overflow-x-auto ring-1 ring-gray-950/5 dark:ring-white/10 rounded-2xl bg-white dark:bg-gray-900 shadow-sm">
                    <table class="fi-ta-table w-full text-start divide-y divide-gray-200 dark:divide-white/5">
                        <thead class="bg-gray-50/50 dark:bg-white/5 text-[10px] uppercase font-bold text-gray-500 dark:text-gray-400 tracking-wider">
                            <tr>
                                <th class="px-3 py-4 text-start whitespace-nowrap">#</th>
                                <th class="px-4 py-4 text-start min-w-[200px]">Riesgo</th>
                                <th class="px-4 py-4 text-start">Área / Proceso</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Criticidad</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Score</th>
                                <th class="px-4 py-4 text-center">Tratamiento</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Estado</th>
                                <th class="px-4 py-4 text-start">Responsable</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5 text-xs text-gray-600 dark:text-gray-300">
                            @forelse($topRisks as $risk)
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition-colors">
                                <td class="px-3 py-4 text-gray-400 font-mono text-[9px]">{{ $loop->iteration }}</td>
                                <td class="px-4 py-4 font-bold text-gray-900 dark:text-white leading-relaxed">{{ $risk->name }}</td>
                                <td class="px-4 py-4 font-medium opacity-70">{{ $risk->organizationalUnit?->name ?? '-' }}</td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @php
                                    $color = match($risk->criticality) {
                                    'Crítico' => 'danger',
                                    'Alto' => 'orange',
                                    'Medio' => 'warning',
                                    'Bajo' => 'info',
                                    'Muy Bajo' => 'success',
                                    default => 'gray',
                                    };
                                    @endphp
                                    <x-filament::badge :color="$color" size="md" class="font-bold border dark:border-white/10">{{ $risk->criticality }}</x-filament::badge>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 font-black text-sm">
                                        @php
                                        $res = $risk->assessments->where('type','residual')->sortByDesc('assessed_at')->first();
                                        $inh = $risk->assessments->where('type','inherent')->sortByDesc('assessed_at')->first();
                                        echo $res?->score ?? $inh?->score ?? '-';
                                        @endphp
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center ">
                                    @php
                                    $color = match($risk->treatment) {
                                    'aceptar' => 'info',
                                    'evitar' => 'danger',
                                    'reducir' => 'warning',
                                    'transferir' => 'success',
                                    default => 'gray',
                                    };
                                    @endphp
                                    <x-filament::badge :color="$color" size="md" class="font-bold border dark:border-white/10">{{ ucfirst($risk->treatment ?? '—') }}</x-filament::badge>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @php
                                    $color = match($risk->status) {
                                    'Identificado', 'Evaluado' => 'info',
                                    'En tratamiento' => 'warning',
                                    'En seguimiento' => 'primary',
                                    'Cerrado / Revisado' => 'success',
                                    default => 'gray',
                                    };
                                    @endphp
                                    <x-filament::badge :color="$color" size="md" class="font-bold border dark:border-white/10">{{ strtoupper($risk->status) }}</x-filament::badge>
                                </td>
                                <td class="px-4 py-4 font-medium">{{ $risk->responsable?->full_name ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-3 py-10 text-center italic text-gray-400">Sin datos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. RIESGOS VENCIDOS --}}
            <div>
                <div class="flex items-center justify-between mb-4 border-l-4 border-amber-500 pl-4 py-1">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white tracking-tight">2. RIESGOS SIN REVISIÓN (VENCIDOS)</h3>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Requieren atención inmediata del responsable</p>
                    </div>
                    <x-filament::badge color="warning" size="md" class="px-3 rounded-full">{{ $overdueRisks->count() }} ALERTAS</x-filament::badge>
                </div>
                <div class="fi-ta-content overflow-x-auto ring-1 ring-gray-950/5 dark:ring-white/10 rounded-2xl bg-white dark:bg-gray-900 shadow-sm">
                    <table class="fi-ta-table w-full text-start divide-y divide-gray-200 dark:divide-white/5 text-xs text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50/50 dark:bg-white/5 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                            <tr>
                                <th class="px-4 py-4 text-start min-w-[200px]">Riesgo</th>
                                <th class="px-4 py-4 text-start">Área / Proceso</th>
                                <th class="px-4 py-4 text-start">Responsable</th>
                                <th class="px-4 py-4 text-start whitespace-nowrap">Fecha de Revisión</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Dias Vencidos</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5 uppercase font-medium">
                            @forelse($overdueRisks as $risk)
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-500/5 transition-colors">
                                <td class="px-4 py-4 font-bold text-gray-900 dark:text-white leading-relaxed">{{ $risk->name }}</td>
                                <td class="px-4 py-4 opacity-70">{{ $risk->organizationalUnit?->name ?? '—' }}</td>
                                <td class="px-4 py-4 opacity-70">{{ $risk->responsable?->full_name ?? '—' }}</td>
                                <td class="px-4 py-4 font-black whitespace-nowrap">
                                    <span class="text-danger-600 bg-danger-50 dark:bg-danger-500/10 px-2 py-1 rounded">
                                        {{ $risk->next_review_at?->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center font-black text-danger-700 whitespace-nowrap">
                                    {{ $risk->next_review_at ? round(abs(now()->diffInDays($risk->next_review_at))) : '0' }}d
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @php
                                    $color = match($risk->status) {
                                    'Identificado', 'Evaluado' => 'info',
                                    'En tratamiento' => 'warning',
                                    'En seguimiento' => 'primary',
                                    'Cerrado / Revisado' => 'success',
                                    default => 'gray',
                                    };
                                    @endphp
                                    <x-filament::badge :color="$color" size="sm">{{ strtoupper($risk->status) }}</x-filament::badge>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center italic text-gray-400">TODO AL DÍA</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 3. ACCIONES VENCIDAS --}}
            <div>
                <div class="flex items-center justify-between mb-4 border-l-4 border-green-600 pl-4 py-1">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white tracking-tight">3. ACCIONES VENCIDAS Y BLOQUEADAS</h3>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Planes de tratamiento retrasados o con impedimentos</p>
                    </div>
                    <x-filament::badge color="success" size="md" class="px-3 rounded-full">{{ $overdueActions->count() }} ACCIONES</x-filament::badge>
                </div>
                <div class="fi-ta-content overflow-x-auto ring-1 ring-gray-950/5 dark:ring-white/10 rounded-2xl bg-white dark:bg-gray-900 shadow-sm">
                    <table class="fi-ta-table w-full text-start divide-y divide-gray-200 dark:divide-white/5 text-xs text-gray-600 dark:text-gray-300 font-medium">
                        <thead class="bg-gray-50/50 dark:bg-white/5 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                            <tr>
                                <th class="px-4 py-4 text-start min-w-[200px]">Acción</th>
                                <th class="px-4 py-4 text-start">Riesgo</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Estado</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Prioridad</th>
                                <th class="px-4 py-4 text-start whitespace-nowrap">Límite</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">% Avance</th>
                                <th class="px-4 py-4 text-start">Responsable</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5 uppercase">
                            @forelse($overdueActions as $action)
                            <tr class="hover:bg-orange-50/30 dark:hover:bg-orange-500/5 transition-colors">
                                <td class="px-4 py-4 font-bold text-gray-900 dark:text-white leading-relaxed">{{ $action->title }}</td>
                                <td class="px-4 py-4 opacity-70 text-xs">{{ $action->risk?->name ?? '—' }}</td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <x-filament::badge :color="$action->status === 'bloqueada' ? 'danger' : 'warning'" size="md" class="font-black border dark:border-white/10">
                                        {{ $action->status }}
                                    </x-filament::badge>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @php
                                    $prioColor = match($action->priority) {
                                    'alta' => 'danger',
                                    'media' => 'warning',
                                    'baja' => 'info',
                                    default => 'gray',
                                    };
                                    @endphp
                                    <x-filament::badge :color="$prioColor" size="sm" class="uppercase">{{ $action->priority ?? '—' }}</x-filament::badge>
                                </td>
                                <td class="px-4 py-4 font-black whitespace-nowrap">
                                    <span class="text-danger-600 bg-danger-50 dark:bg-danger-500/10 px-2 py-1 rounded">
                                        {{ $action->commitment_date?->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap font-black">
                                    {{ $action->progress ?? 0 }}%
                                </td>
                                <td class="px-4 py-4 opacity-70 text-xs">{{ $action->responsable?->full_name ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center italic text-gray-400 uppercase">SIN BLOQUEOS</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. CONTROLES INSUFICIENTES --}}
            <div>
                <div class="flex items-center justify-between mb-4 border-l-4 border-rose-300 pl-4 py-1">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white tracking-tight">4. CONTROLES INSUFICIENTES</h3>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Controles con efectividad insuficiente en riesgos relevantes</p>
                    </div>
                    <x-filament::badge color="danger" size="md" class="px-3 rounded-full">{{ $insufficientControls->count() }} ALERTAS</x-filament::badge>
                </div>
                <div class="fi-ta-content overflow-x-auto ring-1 ring-gray-950/5 dark:ring-white/10 rounded-2xl bg-white dark:bg-gray-900 shadow-sm">
                    <table class="fi-ta-table w-full text-start divide-y divide-gray-200 dark:divide-white/5 text-xs text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50/50 dark:bg-white/5 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                            <tr>
                                <th class="px-4 py-4 text-start min-w-[200px]">Control</th>
                                <th class="px-4 py-4 text-start">Riesgo Asociado</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Criticidad</th>
                                <th class="px-4 py-4 text-start">Área</th>
                                <th class="px-4 py-4 text-start">Tipo</th>
                                <th class="px-4 py-4 text-start">Frecuencia</th>
                                <th class="px-4 py-4 text-start">Responsable</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5 uppercase font-medium">
                            @forelse($insufficientControls as $control)
                            <tr class="hover:bg-rose-50/30 dark:hover:bg-rose-500/5 transition-colors">
                                <td class="px-4 py-4 font-bold text-gray-900 dark:text-white leading-relaxed">{{ $control->title }}</td>
                                <td class="px-4 py-4 opacity-70 text-xs">{{ $control->risk?->name ?? '—' }}</td>
                                <td class="px-4 py-4 text-center">
                                    @php
                                    $critColor = match($control->risk?->criticality) {
                                    'Crítico' => 'danger',
                                    'Alto' => 'orange',
                                    'Medio' => 'warning',
                                    default => 'gray',
                                    };
                                    @endphp
                                    <x-filament::badge :color="$critColor" size="sm">{{ $control->risk?->criticality ?? '—' }}</x-filament::badge>
                                </td>
                                <td class="px-4 py-4 opacity-70">{{ $control->risk?->organizationalUnit?->name ?? '—' }}</td>
                                <td class="px-4 py-4 opacity-70 text-[10px]">{{ $control->type ?? '—' }}</td>
                                <td class="px-4 py-4 opacity-70 text-[10px]">{{ $control->frequency ?? '—' }}</td>
                                <td class="px-4 py-4 opacity-70 text-xs">{{ $control->responsable?->full_name ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center italic text-gray-400">TODO EN ORDEN</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 5. EVIDENCIAS PENDIENTES --}}
            <div>
                <div class="flex items-center justify-between mb-4 border-l-4 border-purple-500 pl-4 py-1">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white tracking-tight">5. EVIDENCIAS PENDIENTES DE VALIDAR</h3>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Documentos subidos esperando verificación</p>
                    </div>
                    <x-filament::badge color="purple" size="md" class="px-3 rounded-full">{{ $pendingEvidences->count() }} PENDIENTES</x-filament::badge>
                </div>
                <div class="fi-ta-content overflow-x-auto ring-1 ring-gray-950/5 dark:ring-white/10 rounded-2xl bg-white dark:bg-gray-900 shadow-sm">
                    <table class="fi-ta-table w-full text-start divide-y divide-gray-200 dark:divide-white/5 text-xs text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50/50 dark:bg-white/5 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                            <tr>
                                <th class="px-4 py-4 text-start min-w-[200px]">Documento</th>
                                <th class="px-4 py-4 text-start">Riesgo</th>
                                <th class="px-4 py-4 text-start">Tipo</th>
                                <th class="px-4 py-4 text-start">Subido Por</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Fecha</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5 uppercase font-medium">
                            @forelse($pendingEvidences as $doc)
                            <tr class="hover:bg-purple-50/30 dark:hover:bg-purple-500/5 transition-colors">
                                <td class="px-4 py-4 font-bold text-gray-900 dark:text-white leading-relaxed">{{ $doc->title }}</td>
                                <td class="px-4 py-4 opacity-70 text-xs">{{ $doc->risk?->name ?? '—' }}</td>
                                <td class="px-4 py-4 opacity-70 text-[10px]">{{ $doc->document_type ?? '—' }}</td>
                                <td class="px-4 py-4 opacity-70 text-xs">{{ $doc->uploadedBy?->full_name ?? '—' }}</td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">{{ $doc->created_at?->format('d/m/Y') }}</td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @php
                                    $docStatusColor = match($doc->status) {
                                    'validada' => 'success',
                                    'rechazada' => 'danger',
                                    default => 'warning',
                                    };
                                    @endphp
                                    <x-filament::badge :color="$docStatusColor" size="sm">{{ strtoupper($doc->status ?? 'PENDIENTE') }}</x-filament::badge>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center italic text-gray-400">SIN PENDIENTES</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 6. RESUMEN POR UNIDAD --}}
            <div>
                <div class="flex items-center justify-between mb-4 border-l-4 border-blue-600 pl-4 py-1">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white tracking-tight">6. RESUMEN POR UNIDAD ORGANIZATIVA / PROCESO</h3>
                    </div>
                    <x-filament::badge color="info" size="md" class="px-3 rounded-full">{{ count($units) }} UNIDADES</x-filament::badge>
                </div>
                <div class="fi-ta-content overflow-x-auto ring-1 ring-gray-950/5 dark:ring-white/10 rounded-2xl bg-white dark:bg-gray-900 shadow-sm">
                    <table class="fi-ta-table w-full text-start divide-y divide-gray-200 dark:divide-white/5 text-xs text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50/50 dark:bg-white/5 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                            <tr>
                                <th class="px-4 py-4 text-start min-w-[200px]">Unidad</th>
                                <th class="px-4 py-4 text-start">Tipo</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Riesgos</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Crít./Altos</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Rev. Venc.</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Acc. Venc.</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Acc. Bloq.</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Ctrl. Insuf.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5 font-medium">
                            @foreach($units as $u)
                            <tr class="hover:bg-blue-50/20 dark:hover:bg-blue-500/5 transition-all">
                                <td class="px-4 py-4 font-black text-gray-900 dark:text-white leading-tight">{{ $u['name'] }}</td>
                                <td class="px-4 py-4 opacity-70 text-[10px]">{{ strtoupper($u['type']) }}</td>
                                <td class="px-4 py-4 text-center font-bold">{{ $u['total_risks'] }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="bg-red-50 dark:bg-red-500/10 text-red-600 px-3 py-1 rounded-full font-black">
                                        {{ $u['critical_risks'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center font-bold {{ $u['overdue_reviews'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                    {{ $u['overdue_reviews'] }}
                                </td>
                                <td class="px-4 py-4 text-center text-warning-600 font-black">
                                    <span class="bg-amber-50 dark:bg-amber-500/10 text-amber-600 px-3 py-1 rounded-full font-black">
                                        {{ $u['overdue_actions'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center font-bold {{ $u['blocked_actions'] > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $u['blocked_actions'] }}
                                </td>
                                <td class="px-4 py-4 text-center font-bold {{ $u['insufficient_controls'] > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                                    {{ $u['insufficient_controls'] }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>