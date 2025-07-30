<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-light">
        <h5 class="fw-bold mb-0">Información relevante</h5>
    </div>
    <div class="card-body">
        @php
            $preguntas = [
                'exp_tecnico_superior' => '¿Cuenta con al menos 5 años de experiencia como <strong>Técnico Superior</strong>?',
                'exp_tecnologo_superior' => '¿Cuenta con al menos 4 años como <strong>Tecnólogo Superior</strong>?',
                'exp_tercer_nivel' => '¿Cuenta con al menos 2 años con título de <strong>Tercer Nivel</strong>?',
                'capacitacion_100h' => '¿Tiene al menos 100 horas de capacitación en los últimos 5 años?',
                'exp_gestion_educativa' => '¿Tiene al menos 3 años de experiencia en gestión educativa superior o equivalente?',
                'exp_docencia_investigacion' => '¿Tiene al menos 3 años en docencia o investigación en educación superior?',
            ];
        @endphp

        @foreach ($preguntas as $name => $texto)
            <div class="mb-4">
                <label class="form-label d-block">{!! $texto !!} <span class="text-danger">*</span></label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="{{ $name }}" id="{{ $name }}_si" value="SI" required>
                    <label class="form-check-label" for="{{ $name }}_si">Sí</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="{{ $name }}" id="{{ $name }}_no" value="NO">
                    <label class="form-check-label" for="{{ $name }}_no">No</label>
                </div>
                @if (str_contains($name, 'exp_'))
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="{{ $name }}" id="{{ $name }}_na" value="NO_APLICA">
                        <label class="form-check-label" for="{{ $name }}_na">No aplica</label>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
