<x-filament-panels::page>
    <x-filament::section>
        <div class="heatmap-container">
            <div></div>
            <?php
            $impactLabels = [1 => 'Mínimo', 2 => 'Bajo', 3 => 'Moderado', 4 => 'Alto', 5 => 'Máximo'];
            $probLabels   = [1 => 'Muy baja', 2 => 'Baja', 3 => 'Media', 4 => 'Alta', 5 => 'Muy alta'];
            ?>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="heatmap-label">{{ $i }} - {{ $impactLabels[$i] }}</div>
            <?php endfor; ?>
            <?php $matrix = $this->getMatrixData() ?>
            <?php for ($p = 5; $p >= 1; $p--): ?>
                <div class="heatmap-label">
                    <?php echo $p; ?> - <?php echo $probLabels[$p]; ?>
                </div>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php
                    //comprobamos que si en la posicion de la matrix hay algo este lo muestre en caso de null ponemos por defecto el valor 0
                    $count = isset($matrix[$p][$i]) ? $matrix[$p][$i]->total : 0;
                    $score = $p * $i;

                    // clase por defecto
                    $riskClass = 'bg-very-low';
                    //segun el score 
                    if ($score >= 15) {
                        $riskClass = 'bg-critical';
                    } elseif ($score >= 10) {
                        $riskClass = 'bg-high';
                    } elseif ($score >= 5) {
                        $riskClass = 'bg-medium';
                    } elseif ($score >= 3) {
                        $riskClass = 'bg-low';
                    }
                    ?>
                    <div wire:click="$set('score', <?php echo $score; ?>)"
                        x-on:click="$dispatch('open-modal', { id: 'risk-modal' })"
                        class="heatmap-cell <?php echo $riskClass; ?>">
                        <span class="cell-count"><?php echo $count; ?></span>
                        <span class="cell-score">Score <?php echo $score; ?></span>
                    </div>
                <?php endfor; ?>
            <?php endfor; ?>
        </div>
    </x-filament::section>
    <x-filament::modal id="risk-modal" width="7xl" x-on:modal-closed="$wire.resetScore()">
        <x-slot name="heading">
            <div class="flex items-center gap-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                <div class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
                    <x-heroicon-o-chart-bar class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Riesgos con puntuación: <span class="text-primary-600 dark:text-primary-400"><?= $this->score; ?></span>
                </h2>
            </div>
        </x-slot>
        <?php
        $risks = $this->getRiskByScore();
        if ($risks->isEmpty()): ?>
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <x-heroicon-o-face-smile class="w-12 h-12 mb-3 opacity-80" />
                <p class="text-lg font-medium">No hay riesgos registrados con este score</p>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mt-2">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800/60 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Riesgo</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Tipo de Evaluación</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Delito</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Responsable</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Fecha</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900/40">
                        <?php foreach ($risks as $evaluacion): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors duration-200 group">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                                        <span class="group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"><?php echo $evaluacion->risk?->name ?? 'N/A'; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                    $type = $evaluacion->type;
                                    if ($type === 'inherent') {
                                        $type = 'Inneherente';
                                        $color = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800';
                                    } elseif ($type === 'residual') {
                                        $type = 'Residual';
                                        $color = 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900/30 dark:text-fuchsia-400 border border-fuchsia-200 dark:border-fuchsia-800';
                                    }
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full <?php echo $color; ?>">
                                        <?php echo ucfirst($type); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">

                                    <span class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        <?php echo $evaluacion->risk?->type_crime ?? 'N/A'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold text-xs ring-2 ring-white dark:ring-gray-800">
                                            <?php
                                            $respName = $evaluacion->risk?->responsable?->name ?? 'S';
                                            echo strtoupper(substr($respName, 0, 1));
                                            ?>
                                        </div>
                                        <span class="font-medium"><?php echo $evaluacion->risk?->responsable?->name ?? 'Sin asignar'; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center gap-1.5">
                                        <x-heroicon-o-calendar class="w-4 h-4" />
                                        <?php echo $evaluacion->assessed_at?->format('d/m/Y') ?? 'N/A'; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <?php if ($evaluacion->risk): ?>
                                        <x-filament::button
                                            tag="a"
                                            href="<?php echo e(route('filament.admin.resources.risks.edit', $evaluacion->risk)); ?>"
                                            color="primary"
                                            size="sm"
                                            icon="heroicon-m-arrow-top-right-on-square"
                                            class="shadow-sm hover:shadow-md transition-all">
                                            Ver Riesgo
                                        </x-filament::button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </x-filament::modal>
</x-filament-panels::page>