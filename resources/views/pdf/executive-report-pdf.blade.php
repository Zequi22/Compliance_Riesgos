<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Ejecutivo — {{ $date_from ?? 'Resumen' }} / {{ $date_to ?? 'Hoy' }}</title>
    <style>
        @php echo file_get_contents(base_path('resources/css/filament/admin/executiveReportPDF.css')); @endphp
        body { margin: 0; padding: 0; background: #fff; }
        @page { size: A4 portrait; margin: 0; }
    </style>
</head>
<body>

<div class="er-pdf-wrapper">

<?php
/* LOGO */
$logoPath = public_path('images/LogoOnDark.png');
$logoSrc  = '';
if (file_exists($logoPath)) {
    $mime    = function_exists('mime_content_type') ? mime_content_type($logoPath) : 'image/png';
    $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
}

/* HELPERS */
$critClass = fn(string $c): string => match($c) {
    'Crítico'  => 'er-badge b-red',
    'Alto'     => 'er-badge b-orange',
    'Medio'    => 'er-badge b-amber',
    'Bajo'     => 'er-badge b-blue',
    'Muy Bajo' => 'er-badge b-green',
    default    => 'er-badge b-gray',
};
$statusClass = fn(string $s): string => match($s) {
    'bloqueada'   => 'er-badge b-red',
    'en_curso'    => 'er-badge b-blue',
    'en_revision' => 'er-badge b-amber',
    'cerrada'     => 'er-badge b-green',
    default       => 'er-badge b-gray',
};
$priorClass = fn(string $p): string => match($p) {
    'alta'  => 'er-badge b-red',
    'media' => 'er-badge b-amber',
    'baja'  => 'er-badge b-blue',
    default => 'er-badge b-gray',
};
?>

<!-- CABECERA -->
<div class="er-header">
    <div>
        <?php if (!empty($logoSrc)): ?><img src="<?php echo $logoSrc; ?>" alt="Logo"><?php endif; ?>
        <h1>Informe Ejecutivo de Gestión de Riesgos</h1>
        <div class="er-subtitle">Sistema de Evaluación de Riesgos y Cumplimiento</div>
    </div>
    <div class="er-meta">
        <div>Generado: <?php echo $generatedAt->format('d/m/Y H:i'); ?></div>
        <div class="er-period-badge">
            Periodo: <?php echo $date_from ? date('d/m/Y', strtotime($date_from)) : 'Inicio'; ?>
            — <?php echo $date_to ? date('d/m/Y', strtotime($date_to)) : 'Hoy'; ?>
        </div>
    </div>
</div>

<!-- KPIs -->
<div class="er-kpi-row">
    <div class="er-kpi er-kpi-blue"><div class="er-kpi-label">Riesgos Totales</div><div class="er-kpi-value"><?php echo $kpis['total_risks']; ?></div></div>
    <div class="er-kpi er-kpi-red"><div class="er-kpi-label">Críticos / Altos</div><div class="er-kpi-value"><?php echo $kpis['critical_risks']; ?></div></div>
    <div class="er-kpi er-kpi-amber"><div class="er-kpi-label">Rev. Vencidas</div><div class="er-kpi-value"><?php echo $kpis['overdue_reviews']; ?></div></div>
    <div class="er-kpi er-kpi-orange"><div class="er-kpi-label">Acc. Venc./Bloq.</div><div class="er-kpi-value"><?php echo $kpis['overdue_blocked_actions']; ?></div></div>
    <div class="er-kpi er-kpi-rose"><div class="er-kpi-label">Ctrl. Insuficientes</div><div class="er-kpi-value"><?php echo $kpis['insufficient_controls']; ?></div></div>
    <div class="er-kpi er-kpi-purple"><div class="er-kpi-label">Evid. Pendientes</div><div class="er-kpi-value"><?php echo $kpis['pending_evidences']; ?></div></div>
</div>

<!-- 1. TOP RIESGOS -->
<div class="er-section">
    <div class="er-section-header sh-red">
        <span>1. Top Riesgos por Criticidad</span>
        <span class="er-count"><?php echo $topRisks->count(); ?> riesgos</span>
    </div>
    <?php if ($topRisks->isEmpty()): ?>
        <div class="er-empty">Sin riesgos evaluados en el periodo.</div>
    <?php else: ?>
    <table class="er-table">
        <thead><tr>
            <th>#</th><th>Riesgo</th><th>Área / Proceso</th><th>Criticidad</th>
            <th class="tc">Score</th><th>Tratamiento</th><th>Estado</th><th>Responsable</th>
        </tr></thead>
        <tbody>
        <?php foreach ($topRisks as $i => $risk):
            $res   = $risk->assessments->where('type','residual')->sortByDesc('assessed_at')->first();
            $inh   = $risk->assessments->where('type','inherent')->sortByDesc('assessed_at')->first();
            $score = $res?->score ?? $inh?->score ?? '—';
        ?>
        <tr>
            <td class="muted"><?php echo $i + 1; ?></td>
            <td class="bold"><?php echo $risk->name; ?></td>
            <td class="muted"><?php echo $risk->organizationalUnit?->name ?? '—'; ?></td>
            <td><span class="<?php echo $critClass($risk->criticality); ?>"><?php echo $risk->criticality; ?></span></td>
            <td class="tc bold"><?php echo $score; ?></td>
            <td class="muted"><?php echo ucfirst($risk->treatment ?? '—'); ?></td>
            <td class="muted"><?php echo $risk->status; ?></td>
            <td class="muted"><?php echo $risk->responsable ? $risk->responsable->name . ' ' . $risk->responsable->last_name : '—'; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- 2. RIESGOS VENCIDOS -->
<div class="er-section">
    <div class="er-section-header sh-amber">
        <span>2. Riesgos Sin Revisión (Vencidos)</span>
        <span class="er-count"><?php echo $overdueRisks->count(); ?> riesgos</span>
    </div>
    <?php if ($overdueRisks->isEmpty()): ?>
        <div class="er-empty">✓ Sin riesgos con revisión vencida.</div>
    <?php else: ?>
    <table class="er-table">
        <thead><tr>
            <th>Riesgo</th><th>Área</th><th>Responsable</th>
            <th>Próxima Revisión</th><th class="tc">Días Vencido</th><th>Estado</th>
        </tr></thead>
        <tbody>
        <?php foreach ($overdueRisks as $risk):
            $dias = $risk->next_review_at ? round(abs(now()->diffInDays($risk->next_review_at))) : '—';
        ?>
        <tr>
            <td class="bold"><?php echo $risk->name; ?></td>
            <td class="muted"><?php echo $risk->organizationalUnit?->name ?? '—'; ?></td>
            <td class="muted"><?php echo $risk->responsable ? $risk->responsable->name . ' ' . $risk->responsable->last_name : '—'; ?></td>
            <td class="danger"><?php echo $risk->next_review_at?->format('d/m/Y') ?? '—'; ?></td>
            <td class="tc danger"><?php echo $dias; ?>d</td>
            <td class="muted"><?php echo $risk->status; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- 3. ACCIONES VENCIDAS Y BLOQUEADAS -->
<div class="er-section">
    <div class="er-section-header sh-orange">
        <span>3. Acciones Vencidas y Bloqueadas</span>
        <span class="er-count"><?php echo $overdueActions->count(); ?> acciones</span>
    </div>
    <?php if ($overdueActions->isEmpty()): ?>
        <div class="er-empty">✓ Sin acciones vencidas ni bloqueadas.</div>
    <?php else: ?>
    <table class="er-table">
        <thead><tr>
            <th>Acción</th><th>Riesgo</th><th>Estado</th><th>Prioridad</th>
            <th>Fecha Compromiso</th><th class="tc">% Avance</th><th>Responsable</th>
        </tr></thead>
        <tbody>
        <?php foreach ($overdueActions as $action): ?>
        <tr>
            <td class="bold"><?php echo $action->title; ?></td>
            <td class="muted"><?php echo $action->risk?->name ?? '—'; ?></td>
            <td><span class="<?php echo $statusClass($action->status); ?>"><?php echo ucfirst(str_replace('_', ' ', $action->status)); ?></span></td>
            <td><span class="<?php echo $priorClass($action->priority ?? ''); ?>"><?php echo ucfirst($action->priority ?? '—'); ?></span></td>
            <td class="danger"><?php echo $action->commitment_date?->format('d/m/Y') ?? '—'; ?></td>
            <td class="tc bold"><?php echo $action->progress ?? 0; ?>%</td>
            <td class="muted"><?php echo $action->responsable ? $action->responsable->name . ' ' . $action->responsable->last_name : '—'; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- 4. CONTROLES INSUFICIENTES -->
<div class="er-section">
    <div class="er-section-header sh-rose">
        <span>4. Controles Insuficientes en Riesgos Críticos / Altos</span>
        <span class="er-count"><?php echo $insufficientControls->count(); ?> controles</span>
    </div>
    <?php if ($insufficientControls->isEmpty()): ?>
        <div class="er-empty">✓ Sin controles insuficientes en riesgos críticos.</div>
    <?php else: ?>
    <table class="er-table">
        <thead><tr>
            <th>Control</th><th>Riesgo Asociado</th><th>Criticidad</th>
            <th>Área</th><th>Tipo</th><th>Frecuencia</th><th>Responsable</th>
        </tr></thead>
        <tbody>
        <?php foreach ($insufficientControls as $control):
            $cr = $control->risk?->criticality ?? '—';
        ?>
        <tr>
            <td class="bold"><?php echo $control->title; ?></td>
            <td class="muted"><?php echo $control->risk?->name ?? '—'; ?></td>
            <td><span class="<?php echo $critClass($cr); ?>"><?php echo $cr; ?></span></td>
            <td class="muted"><?php echo $control->risk?->organizationalUnit?->name ?? '—'; ?></td>
            <td class="muted"><?php echo $control->type ?? '—'; ?></td>
            <td class="muted"><?php echo $control->frequency ?? '—'; ?></td>
            <td class="muted"><?php echo $control->responsable?->name ?? '—'; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- 5. EVIDENCIAS PENDIENTES -->
<div class="er-section">
    <div class="er-section-header sh-purple">
        <span>5. Evidencias Pendientes de Validar</span>
        <span class="er-count"><?php echo $pendingEvidences->count(); ?> documentos</span>
    </div>
    <?php if ($pendingEvidences->isEmpty()): ?>
        <div class="er-empty">✓ Sin evidencias pendientes de validación.</div>
    <?php else: ?>
    <table class="er-table">
        <thead><tr>
            <th>Documento</th><th>Riesgo</th><th>Tipo</th>
            <th>Subido Por</th><th>Fecha</th><th>Estado</th>
        </tr></thead>
        <tbody>
        <?php foreach ($pendingEvidences as $doc): ?>
        <tr>
            <td class="bold"><?php echo $doc->title; ?></td>
            <td class="muted"><?php echo $doc->risk?->name ?? '—'; ?></td>
            <td class="muted"><?php echo ucfirst($doc->document_type ?? $doc->classification ?? '—'); ?></td>
            <td class="muted"><?php echo $doc->uploadedBy?->name ?? '—'; ?></td>
            <td class="muted"><?php echo $doc->uploaded_at?->format('d/m/Y') ?? $doc->created_at?->format('d/m/Y') ?? '—'; ?></td>
            <td><span class="er-badge b-amber"><?php echo strtoupper($doc->status ?? 'PENDIENTE'); ?></span></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- 6. RESUMEN POR UNIDAD -->
<div class="er-section">
    <div class="er-section-header sh-blue">
        <span>6. Resumen por Unidad Organizativa / Proceso</span>
        <span class="er-count"><?php echo $units->count(); ?> unidades</span>
    </div>
    <?php if ($units->isEmpty()): ?>
        <div class="er-empty">Sin unidades organizativas con riesgos registrados.</div>
    <?php else: ?>
    <table class="er-table">
        <thead><tr>
            <th>Unidad / Proceso</th><th>Tipo</th>
            <th class="tc">Total</th><th class="tc">Crít./Altos</th>
            <th class="tc">Rev. Venc.</th><th class="tc">Acc. Venc.</th>
            <th class="tc">Acc. Bloq.</th><th class="tc">Ctrl. Insuf.</th>
        </tr></thead>
        <tbody>
        <?php foreach ($units as $u): ?>
        <tr>
            <td class="bold"><?php echo $u['name']; ?></td>
            <td class="muted" style="text-transform:capitalize"><?php echo $u['type']; ?></td>
            <td class="tc bold"><?php echo $u['total_risks']; ?></td>
            <td class="tc <?php echo $u['critical_risks'] > 0 ? 'danger' : 'zero'; ?>"><?php echo $u['critical_risks']; ?></td>
            <td class="tc <?php echo $u['overdue_reviews'] > 0 ? 'warn' : 'zero'; ?>"><?php echo $u['overdue_reviews']; ?></td>
            <td class="tc <?php echo $u['overdue_actions'] > 0 ? 'warn' : 'zero'; ?>"><?php echo $u['overdue_actions']; ?></td>
            <td class="tc <?php echo $u['blocked_actions'] > 0 ? 'danger' : 'zero'; ?>"><?php echo $u['blocked_actions']; ?></td>
            <td class="tc <?php echo $u['insufficient_controls'] > 0 ? 'danger' : 'zero'; ?>"><?php echo $u['insufficient_controls']; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- FOOTER -->
<div class="er-footer">
    Informe Ejecutivo de Gestión de Riesgos &mdash; Confidencial &mdash;
    Generado el <?php echo $generatedAt->format('d/m/Y H:i'); ?> — Sistema de Evaluación de Riesgos y Cumplimiento.
</div>

</div><!-- /.er-pdf-wrapper -->
</body>
</html>
