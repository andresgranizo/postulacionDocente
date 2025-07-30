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

    // Eventos del tipo de identificación
    $('#tipo_documento').on('change', toggleBuscarSegunTipo);

    $('#numero_identificacion').on('input', function () {
        const v = $(this).val().trim();
        $('#btn_buscar_cedula').prop('disabled', v.length !== 10);
    });

    $('#btn_buscar_cedula').click(function () {
        const cedula = $('#numero_identificacion').val().trim();
        $('#msj_cedula').css('color', 'black').text('🔍 Buscando...');
        $('#msj_cne').remove();
        $('#apellidos_nombres').val('').prop('readonly', false);

        $.ajax({
            url: window.registrationConsultarCedulaUrl,
            method: 'POST',
            data: { cedula },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success(data) {
                if (data.error) {
                    $('#msj_cedula').css('color', 'red').text('❌ ' + data.message);
                    return;
                }

                $('#apellidos_nombres').val(data.nombre).prop('readonly', true);
                $('#msj_cedula').css('color', 'green').text('✅ Datos encontrados');

                // Validar edad
                let esMenorEdad = false;
                if (data.fecha_nacimiento) {
                    const partes = data.fecha_nacimiento.split('/');
                    const dia = parseInt(partes[0], 10);
                    const mes = parseInt(partes[1], 10) - 1;
                    const anio = parseInt(partes[2], 10);
                    const fechaNacimiento = new Date(anio, mes, dia);
                    const hoy = new Date();
                    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
                    const m = hoy.getMonth() - fechaNacimiento.getMonth();
                    if (m < 0 || (m === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                        edad--;
                    }
                    if (edad < 18) {
                        esMenorEdad = true;
                    }
                }

                if (esMenorEdad) {
                    $('#msj_cedula')
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .html('❌ La persona es menor de edad y no puede continuar con la postulación.');
                    $('input, select, button').prop('disabled', true);
                    return;
                }

                // Si no es menor de edad, consultar CNE
                $.ajax({
                    url: window.registrationConsultarCneUrl,
                    method: 'POST',
                    data: { cedula },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success(cneData) {
                        let mensajeCNE = '';
                        let claseCNE = '';
                        if (cneData.habilitado && cneData.habilitado.toUpperCase() === 'SI') {
                            mensajeCNE = '✅ Sin impedimento para ejercer cargo público';
                            claseCNE = 'text-success';
                        } else {
                            mensajeCNE = '❌ La persona tiene impedimento para ejercer cargo público';
                            claseCNE = 'text-danger';
                            $('input, select, button').prop('disabled', true);
                        }
                        $('#msj_cne').remove();
                        $('<div id="msj_cne" class="form-text ' + claseCNE + '">' + mensajeCNE + '</div>')
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
    // Cargar zonas (catálogo de zonas si lo usas)
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
});
