<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ficha de Riesgo - <?php echo $risk->name; ?></title>
    <link rel="stylesheet" href="<?php echo base_path('resources/css/filament/admin/riskcardPDF.css'); ?>">
    <style>
        body.pdf-body {
            background-color: white !important;
            color: black !important;
            margin: 0;
            padding: 0;
        }
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
    </style>
</head>

<body class="pdf-body">
    <div class="risk-fiche-pdf">
    <div class="card-header">
        <?php
        $logoPath = public_path('images/logo.png');
        $logoSrc = '';
        if (file_exists($logoPath)) {
            $mime = function_exists('mime_content_type') ? mime_content_type($logoPath) : 'image/png';
            $data = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:' . $mime . ';base64,' . $data;
        }
        ?>
        <?php if (!empty($logoSrc)): ?>
            <img src="<?php echo $logoSrc; ?>" alt="Logo" class="card-logo">
        <?php endif; ?>
        <h1>Ficha de Gestión de Riesgos</h1>
        <div class="generated">ID: #<?php echo $risk->id; ?> | Generado el <?php echo date('d/m/Y H:i'); ?></div>
    </div>

    <h2 class="card-title"><?php echo $risk->name; ?></h2>

    <div class="card-section">
        <h2>Información General</h2>
        <table class="card-table">
            <tr>
                <td class="label">Descripción</td>
                <td class="value"><?php echo $risk->description ?: 'N/D'; ?></td>
            </tr>
            <tr>
                <td class="label">Criticidad</td>
                <td class="value">
                    <?php echo $risk->criticality; ?>
                </td>
            </tr>
            <tr>
                <td class="label">Tratamiento Adoptado</td>
                <td class="value">
                    <?php echo ucfirst($risk->treatment ?: 'Sin definir'); ?>
                </td>
            </tr>
            <tr>
                <td class="label">Plan de Acción</td>
                <td class="value">
                    <?php
                    $totalActions = $risk->actions->count();
                    $openActions = $risk->actions->whereIn('status', ['todo', 'doing'])->count();
                    ?>
                    <?php echo $totalActions; ?> Acciones (<?php echo $openActions; ?> abiertas)
                </td>
            </tr>
            <tr>
                <td class="label">Estado</td>
                <td class="value">
                    <?php 
                    $statusTranslations = [
                        'identificado' => 'IDENTIFICADO',
                        'evaluado' => 'EVALUADO',
                        'tratamiento' => 'EN TRATAMIENTO',
                        'seguimiento' => 'EN SEGUIMIENTO',
                        'cerrado' => 'CERRADO',
                    ];
                    $status = strtolower($risk->status);
                    echo $statusTranslations[$status] ?? strtoupper($status); 
                    ?>
                </td>
            </tr>
            <tr>
                <td class="label">Responsable</td>
                <td class="value"><?php echo $risk->responsable ? ($risk->responsable->name . ' ' . $risk->responsable->last_name) : 'No asignado'; ?></td>
            </tr>
            <tr>
                <td class="label">Cargo</td>
                <td class="value"><?php echo $risk->responsable?->job_title ?: 'N/D'; ?></td>
            </tr>
            <tr>
                <td class="label">Área / Proceso</td>
                <td class="value"><?php echo $risk->organizationalUnit?->name ?: 'No definido'; ?></td>
            </tr>
            <tr>
                <td class="label">Marco Penal Asociado</td>
                <td class="value"><?php echo $risk->type_crime ?: 'N/A'; ?></td>
            </tr>
            <tr>
                <td class="label">Próxima Revisión</td>
                <td class="value"><?php echo $risk->next_review_at ? $risk->next_review_at->format('d/m/Y') : 'No programada'; ?></td>
            </tr>
        </table>
    </div>

    <?php if ($risk->indicators->count() > 0): ?>
        <div class="card-section">
            <h2>Indicadores Clave de Riesgo (KRIs)</h2>
            <table class="section-table">
                <thead>
                    <tr>
                        <th>Indicador</th>
                        <th>Meta</th>
                        <th>Actual</th>
                        <th>Tolerancia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($risk->indicators as $indicator): ?>
                        <tr>
                            <td><?php echo $indicator->name; ?></td>
                            <td><?php echo $indicator->target_value; ?></td>
                            <td><?php echo $indicator->current_value; ?></td>
                            <td><?php echo $indicator->tolerance_level; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($risk->assessments->count() > 0): ?>
        <div class="card-section">
            <h2>Evaluación Inherente y Residual</h2>
            <table class="section-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Prob.</th>
                        <th>Imp.</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($risk->assessments as $assessment): ?>
                        <tr>
                            <td><?php echo $assessment->assessed_at->format('d/m/Y'); ?></td>
                            <td><?php 
                                $typeMap = [
                                    'inherent' => 'Inherente',
                                    'residual' => 'Residual',
                                ];
                                echo $typeMap[strtolower($assessment->type)] ?? ucfirst($assessment->type); 
                            ?></td>
                            <td><?php echo $assessment->probability; ?></td>
                            <td><?php echo $assessment->impact; ?></td>
                            <td><strong><?php echo $assessment->score; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($risk->controls->count() > 0): ?>
        <div class="card-section">
            <h2>Controles</h2>
            <table class="section-table">
                <thead>
                    <tr>
                        <th>Control</th>
                        <th>Tipo / Frec.</th>
                        <th>Responsable</th>
                        <th>Efectividad</th>
                        <th>Fecha Límite</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($risk->controls as $control): ?>
                        <tr>
                            <td><?php echo $control->title; ?></td>
                            <td><?php echo $control->type; ?> / <?php echo $control->frequency; ?></td>
                            <td><?php echo $control->responsable?->name ?: 'N/D'; ?></td>
                            <td><?php echo $control->effectiveness; ?></td>
                            <td><?php echo $control->due_date ? $control->due_date->format('d/m/Y') : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($risk->actions->count() > 0): ?>
        <div class="card-section">
            <h2>Planes de Acción</h2>
            <table class="section-table">
                <thead>
                    <tr>
                        <th>Acción</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>% Avance</th>
                        <th>Vencida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($risk->actions as $action): ?>
                        <tr>
                            <td><?php echo $action->title; ?></td>
                            <td><?php echo $action->status ? ucfirst(str_replace('_', ' ', $action->status)) : '-'; ?></td>
                            <td><?php echo $action->priority ? ucfirst($action->priority) : '-'; ?></td>
                            <td>
                                <?php echo $action->start_date ? $action->start_date->format('d/m/Y') : '-'; ?> 
                            </td>
                            <td>    
                                <?php echo $action->due_date ? $action->due_date->format('d/m/Y') : '-'; ?>
                            </td>
                            <td><?php echo $action->progress ?? 0; ?>%</td>
                            <td><?php echo $action->isOverdue() ? 'Sí' : 'No'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($risk->documents->count() > 0): ?>
        <div class="card-section">
            <h2>Documentos</h2>
            <table class="section-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Fecha Doc.</th>
                        <th>Estado</th>
                        <th>Asociado a</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($risk->documents as $doc): ?>
                        <?php
                        $asociado = 'Riesgo (General)';
                        if ($doc->control_id) $asociado = 'Control: ' . \Illuminate\Support\Str::limit(optional($doc->control)->title, 30);
                        elseif ($doc->action_id) $asociado = 'Acción: ' . \Illuminate\Support\Str::limit(optional($doc->action)->title, 30);
                        ?>
                        <tr>
                            <td><?php echo $doc->title; ?></td>
                            <td><?php echo ucfirst($doc->document_type ?: $doc->classification); ?></td>
                            <td><?php echo $doc->document_date ? $doc->document_date->format('d/m/Y') : $doc->uploaded_at->format('d/m/Y'); ?></td>
                            <td><?php echo strtoupper($doc->status); ?></td>
                            <td><?php echo $asociado; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($risk->statusHistories && $risk->statusHistories->count() > 0): ?>
        <div class="card-section">
            <h2>Historial de Eventos Clave</h2>
            <table class="section-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Evento</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($risk->statusHistories->sortByDesc('created_at')->take(5) as $history): ?>
                        <tr>
                            <td><?php echo $history->created_at->format('d/m/Y H:i'); ?></td>
                            <td>Cambio de estado: <?php echo $history->old_status; ?> &rarr; <?php echo $history->new_status; ?></td>
                            <td><?php echo $history->user?->name ?: 'Sistema'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="card-section">
        <p style="font-size: 0.8rem; color: #666; text-align: center; margin-top: 50px;">
            Ficha Técnica de Riesgo - Generado por el Sistema de Evaluación de Riesgos y Cumplimiento.
        </p>
    </div>
</body>

</html>