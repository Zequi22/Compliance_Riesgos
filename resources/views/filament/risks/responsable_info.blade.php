@php
    $responsable = null;
    if (filled($get('responsable_id'))) {
        $responsable = \App\Models\User::find($get('responsable_id'));
    }
@endphp
@if($responsable)
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label class="font-semibold">Nombre</label>
            <input type="text" wire:model.lazy="data.responsable_nombre" value="{{ $responsable->name }}" class="w-full border rounded px-2 py-1" />
        </div>
        <div>
            <label class="font-semibold">Apellidos</label>
            <input type="text" wire:model.lazy="data.responsable_apellidos" value="{{ $responsable->last_name }}" class="w-full border rounded px-2 py-1" />
        </div>
        <div>
            <label class="font-semibold">Área</label>
            <input type="text" wire:model.lazy="data.responsable_area" value="{{ $responsable->area }}" class="w-full border rounded px-2 py-1" />
        </div>
        <div>
            <label class="font-semibold">Equipo</label>
            <input type="text" wire:model.lazy="data.responsable_equipo" value="{{ $responsable->team }}" class="w-full border rounded px-2 py-1" />
        </div>
        <div>
            <label class="font-semibold">Departamento</label>
            <input type="text" wire:model.lazy="data.responsable_departamento" value="{{ $responsable->department }}" class="w-full border rounded px-2 py-1" />
        </div>
    </div>
@endif
