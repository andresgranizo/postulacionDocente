function toggleBuscarSegunTipo() {
    const tipo = $('#tipo_documento').val();

    if (tipo === 'cedula') {
        $('#btn_buscar_cedula').show();
        $('#numero_identificacion').prop('readonly', false);
        $('#btn_buscar_cedula').prop('disabled', $('#numero_identificacion').val().length !== 10);
    } else {
        $('#btn_buscar_cedula').hide();
        $('#numero_identificacion').prop('readonly', false);
        $('#msj_cedula').text('');
        $('#apellidos_nombres').val('').prop('readonly', false);
    }
}

$(document).ready(function () {
    toggleBuscarSegunTipo();

    let provinciasData = []; // Aquí guardaremos provincias con zona para usarlas luego

    // Inicializar Select2
    $('#provincias_movilizacion').select2({
        placeholder: 'Seleccione una o más provincias',
        width: '100%',
        dropdownParent: $('#contenedor_provincias')
    });

   $('#tipo_documento').on('change', function () {
        toggleBuscarSegunTipo();
        consultarVisaSiAplica(); // Por si ya hay datos ingresados
    });

    $('#numero_identificacion, #fecha_nacimiento, #pais_nacionalidad').on('input change', consultarVisaSiAplica);

    $('#numero_identificacion').on('input', function () {
        const tipo = $('#tipo_documento').val();
        const valor = $(this).val().trim();

        if (tipo === 'cedula' && valor.length === 10) {
            // Validaciones y consulta para cédula
            $('#msj_cedula').text('🔍 Buscando...').css('color', 'black');
            $('#apellidos_nombres').val('').prop('readonly', false);
            $('#msj_cne').remove();

            $.ajax({
                url: 'api/validar-cedula',
                method: 'POST',
                data: { cedula: valor },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    if (res.existe) {
                        toastr.warning("Esta cédula ya tiene un registro previo.", "⚠️ Atención");
                        $('#btn_guardar_formulario').prop('disabled', true);
                    } else {
                        toastr.success(res.message || "Cédula válida", '✅ Éxito');
                        $('#btn_guardar_formulario').prop('disabled', false);
                    }
                }
            });

            $.ajax({
                url: window.registrationConsultarCedulaUrl,
                method: 'POST',
                data: { cedula: valor },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success(data) {
                    if (data.error) {
                        $('#msj_cedula').css('color', 'red').text('❌ ' + data.message);
                        return;
                    }

                    $('#apellidos_nombres').val(data.nombre).prop('readonly', true);
                    $('#campo_apellidos_nombres').removeClass('d-none');
                    $('#msj_cedula').css('color', 'green').text('✅ Datos encontrados');

                    // Edad
                    const partes = data.fecha_nacimiento.split('/');
                    const fechaNacimiento = new Date(partes[2], partes[1] - 1, partes[0]);
                    const hoy = new Date();
                    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
                    if (hoy.getMonth() < fechaNacimiento.getMonth() ||
                        (hoy.getMonth() === fechaNacimiento.getMonth() && hoy.getDate() < fechaNacimiento.getDate())) {
                        edad--;
                    }

                    if (edad < 18) {
                        $('#msj_cedula').removeClass('text-success').addClass('text-danger')
                            .html('❌ La persona es menor de edad.');
                        $('input, select, button').prop('disabled', true);
                        return;
                    }

                    $.ajax({
                        url: window.registrationConsultarCneUrl,
                        method: 'POST',
                        data: { cedula: valor },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success(cneData) {
                            const clase = cneData.habilitado?.toUpperCase() === 'SI' ? 'text-success' : 'text-danger';
                            const mensaje = clase === 'text-success'
                                ? '✅ Sin impedimento para ejercer cargo público'
                                : '❌ La persona tiene impedimento para ejercer cargo público';

                            if (clase === 'text-danger') {
                                $('input, select, button').prop('disabled', true);
                            }

                            $('#msj_cne').remove();
                            $('<div id="msj_cne" class="form-text ' + clase + '">' + mensaje + '</div>')
                                .insertAfter('#msj_cedula');
                        },
                        error() {
                            $('<div id="msj_cne" class="form-text text-danger">❌ Error al consultar CNE</div>')
                                .insertAfter('#msj_cedula');
                        }
                    });
                },
                error() {
                    $('#msj_cedula').css('color', 'red').text('❌ Error en la consulta');
                }
            });
        }

        if (tipo === 'pasaporte' && valor.length > 3) {
            $.ajax({
                url: 'api/validar-pasaporte',
                method: 'POST',
                data: { pasaporte: valor },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    if (res.existe) {
                        toastr.warning(res.message, '⚠️ Atención');
                        $('#btn_guardar_formulario').prop('disabled', true);
                    } else {
                        toastr.success(res.message, '✅ Válido');
                        $('#btn_guardar_formulario').prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || "Error al validar pasaporte.";
                    toastr.error(msg, "Error");
                    $('#btn_guardar_formulario').prop('disabled', true);
                }
            });
        }
    });
});



    // Cargar provincias de residencia
    $.get('/catalogo/provincias', function (data) {
        provinciasData = data;

        data.forEach(function (provincia) {
            // Para residencia
            $('#provincia_id').append(
                $('<option>', {
                    value: provincia.id,
                    text: provincia.nombre
                })
            );

            // Para multiselect movilización
            $('#provincias_movilizacion').append(
                $('<option>', {
                    value: provincia.id,
                    text: provincia.nombre,
                    'data-zona': provincia.zona
                })
            );
        });
    });

    // Cargar cantones según provincia
    $('#provincia_id').on('change', function () {
        const provinciaId = $(this).val();
        $('#canton_id').empty().append('<option value="">Seleccione un cantón</option>');
        if (provinciaId) {
            $.get('/catalogo/cantones/' + provinciaId, function (data) {
                data.forEach(function (canton) {
                    $('#canton_id').append(
                        $('<option>', {
                            value: canton.id,
                            text: canton.nombre
                        })
                    );
                });
            });
        }
    });


    $.get('/catalogo/zonas', function (data) {
        data.forEach(function (zona) {
            $('#zona_id').append(
                $('<option>', {
                    value: zona.id,
                    text: zona.nombre
                })
            );
        });
    });


    $('#disponibilidad_movilizacion').on('change', function () {
        const valor = $(this).val();
        if (valor === 'si') {
            $('#contenedor_provincias').show();
            $('#zonas_movilizacion').val('');
        } else {
            $('#contenedor_provincias').hide();
            $('#provincias_movilizacion').val([]).trigger('change');

            const provinciaResidenciaId = $('#provincia_id').val();
            const provinciaSeleccionada = provinciasData.find(p => p.id == provinciaResidenciaId);

            if (provinciaSeleccionada) {
                $('#zonas_movilizacion').val(provinciaSeleccionada.zona || '0');
            } else {
                $('#zonas_movilizacion').val('0');
            }
        }
    });


    $('#provincias_movilizacion').on('change', function () {
        const zonas = new Set();
        $('#provincias_movilizacion option:selected').each(function () {
            const zona = $(this).data('zona');
            if (zona) zonas.add(zona);
        });

        const zonasTexto = Array.from(zonas).sort().join(',');
        $('#zonas_movilizacion').val(zonasTexto !== '' ? zonasTexto : '0');
    });



toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right", // Cambia si se traslapa con algo
    "timeOut": "10000"

};
