<hr class="my-4">
<h5 class="mt-4">Datos Personales</h5>

{{-- Tipo de identificación --}}
<div class="mb-3">
    <label for="tipo_documento" class="form-label">Tipo de Identificación *</label>
    <select name="tipo_documento" id="tipo_documento" class="form-select" required>
        <option value="cedula">CÉDULA DE IDENTIDAD</option>
        <option value="pasaporte">PASAPORTE</option>
        <option value="refugiado">CARNÉ DE REFUGIADO</option>
    </select>
</div>

<div class="mb-3">
    <label for="numero_identificacion" class="form-label">Cédula de Identidad *</label>
    <div class="input-group">
        <input type="text" id="numero_identificacion" name="cedula" class="form-control"
            placeholder="Ingrese su cédula" value="{{ old('cedula') }}">
        <button type="button" id="btn_buscar_cedula" class="btn btn-secondary" disabled>
            Buscar
        </button>
    </div>
    <div id="msj_cedula" class="form-text"></div>
</div>

<div class="mb-3">
    <label for="apellidos_nombres" class="form-label">Apellidos y Nombres *</label>
    <input type="text" id="apellidos_nombres" name="nombres_apellidos" class="form-control" readonly
        value="{{ old('nombres_apellidos') }}">
</div>

<div class="mb-3">
    <label for="codigo_dactilar" class="form-label">Código Dactilar *</label>
    <input type="text" id="codigo_dactilar" name="codigo_dactilar" class="form-control"
        value="{{ old('codigo_dactilar') }}" style="text-transform: uppercase;">
</div>

<p><strong>
        Recuerda verificar que tu correo sea válido ya que el sistema solo permite el registro una sola vez.
    </strong></p>

<div class="mb-3">
    <label for="correo_electronico_principal" class="form-label">Correo Electrónico *</label>
    <input type="email" id="correo_electronico_principal" name="correo" class="form-control"
        value="{{ old('correo') }}" pattern="^[^\s,]+@[^\s,]+\.[^\s,]+$"
        title="El correo no debe contener espacios ni comas."
        oninvalid="this.setCustomValidity('El correo no debe contener espacios ni comas.')"
        oninput="this.setCustomValidity('')">
</div>

<div class="mb-3">
    <label for="telefono" class="form-label">Teléfono Celular *</label>
    <input type="text" id="telefono" name="telefono" class="form-control" value="{{ old('telefono') }}"
        maxlength="10" pattern="[0-9]{10}" title="El número debe tener 10 dígitos."
        oninvalid="this.setCustomValidity('Ingresa un número válido de 10 dígitos.')"
        oninput="this.setCustomValidity('')">
</div>

<div class="mb-3">
    <label for="provincia_id" class="form-label">Provincia de Residencia *</label>
    <select id="provincia_id" name="provincia_id" class="form-select" required>
        <option value="">Seleccione una provincia</option>
        <!-- Opciones se llenan por JS -->
    </select>
</div>

<div class="mb-3">
    <label for="canton_id" class="form-label">Cantón de Residencia *</label>
    <select id="canton_id" name="canton_id" class="form-select" required>
        <option value="">Seleccione primero un cantón</option>
        <!-- Opciones se llenan por JS -->
    </select>
</div>

<div class="mb-3">
    <label for="disponibilidad_movilizacion" class="form-label">¿Tiene disponibilidad de movilización? *</label>
    <select id="disponibilidad_movilizacion" name="disponibilidad_movilizacion" class="form-select" required>
        <option value="">Seleccione una opción</option>
        <option value="si">Sí</option>
        <option value="no">No</option>
    </select>
</div>

<div class="mb-3" id="contenedor_provincias" style="display: none;">
    <label for="provincias_movilizacion" class="form-label">Seleccione las provincias *</label>
    <select id="provincias_movilizacion" name="provincias_movilizacion[]" class="form-select select2" multiple>
    </select>

</div>

<input type="hidden" id="zonas_movilizacion" name="zonas_movilizacion">


<script src="{{ asset('js/datos-personales.js') }}"></script>
<script>
    window.registrationConsultarCedulaUrl = "{{ route('registration.consultarCedula') }}";
    window.registrationConsultarCneUrl = "{{ route('registration.consultarCne') }}";
</script>
