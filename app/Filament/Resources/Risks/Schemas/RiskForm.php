<?php

namespace App\Filament\Resources\Risks\Schemas;

use App\Filament\Resources\Risks\Schemas\Tabs\ControlesAccionesTab;
use App\Filament\Resources\Risks\Schemas\Tabs\EvidenciasAnalisisTab;
use App\Filament\Resources\Risks\Schemas\Tabs\IdentificacionTab;
use App\Filament\Resources\Risks\Schemas\Tabs\PlanValoracionTab;
use App\Filament\Resources\Risks\Schemas\Tabs\SeguimientoHistorialTab;
use Filament\Schemas\Schema;

/**
 * RiskForm — Orquestador del formulario de riesgo.
 *
 * Este archivo solo ensambla las 5 pestañas; cada una vive en su
 * propio archivo dentro de la carpeta Tabs/ con su lógica aislada.
 *
 *  Tabs/IdentificacionTab.php      → datos generales y responsable
 *  Tabs/PlanValoracionTab.php      → resumen ejecutivo y valoración de controles
 *  Tabs/EvidenciasAnalisisTab.php  → documentos adjuntos y evaluaciones numéricas
 *  Tabs/ControlesAccionesTab.php   → controles activos y plan de mejora
 *  Tabs/SeguimientoHistorialTab.php → indicadores KRI e historial de estados
 */
class RiskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Tabs::make('Navegación del Riesgo')
                ->columnSpanFull()
                ->tabs([
                    IdentificacionTab::make(),
                    PlanValoracionTab::make(),
                    EvidenciasAnalisisTab::make(),
                    ControlesAccionesTab::make(),
                    SeguimientoHistorialTab::make(),
                ]),
        ]);
    }
}
