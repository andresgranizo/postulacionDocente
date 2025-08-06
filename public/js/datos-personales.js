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
        let modalCarga;

        function mostrarSpinner() {
            if (!modalCarga) {
                const modalElement = document.getElementById('modalCargaDatos');
                modalCarga = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
            }
            modalCarga.show();
        }

        function ocultarSpinner() {
            if (modalCarga) {
                modalCarga.hide();
            }
        }

        const tipo = $('#tipo_documento').val();
        const valor = $(this).val().trim();

        if (tipo === 'cedula' && valor.length === 10) {
            // Validaciones y consulta para cédula
            $('#msj_cedula').text('🔍 Buscando...').css('color', 'black');
            $('#apellidos_nombres').val('').prop('readonly', false);
            $('#msj_cne').remove();

            $.ajax({
                beforeSend: function () {

                },
                url: 'api/validar-cedula',
                method: 'POST',
                data: { cedula: valor },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

                success: function (res) {
                    mostrarSpinner();
                    toastr.success(res.message || "Cédula válida", '✅ Éxito');
                    $('#btn_guardar_formulario').prop('disabled', false);
                },
                error: function (xhr) {
                    if (xhr.status === 409 && xhr.responseJSON) {
                        toastr.warning(xhr.responseJSON.message, "⚠️ Atención");
                        $('#btn_guardar_formulario').prop('disabled', true);
                    } else {
                        toastr.error("Error al validar la cédula.", "❌ Error");
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

                    $.ajax({
                        url: '/api/titulos/consultar',
                        method: 'POST',
                        data: { cedula: valor },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success(response) {
                            ocultarSpinner();
                            console.log('RESPUESTA TITULOS:', response);

                            if (Array.isArray(response.titulos)) {

                                const titulosReducidos = response.titulos.map(t => ({
                                    titulo: t.titulo,
                                    institucion: t.institucion
                                }));
                                mostrarTitulos(response.titulos);
                            } else {

                                $('#input-titulos').val('');
                                mostrarTitulos([]);
                            }
                        },

                        error() {
                            toastr.error("❌ Error al consultar los títulos", "Error");

                            mostrarTitulos([]);
                        }
                    });

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


$.get('/catalogo/provincias', function (data) {
    provinciasData = data;

    data.forEach(function (provincia) {
        $('#provincia_id').append(
            $('<option>', {
                value: provincia.id,
                text: provincia.nombre
            })
        );

        $('#provincias_movilizacion').append(
            $('<option>', {
                value: provincia.id,
                text: provincia.nombre,
                'data-zona': provincia.zona
            })
        );
    });
});

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

    const selected = $(this).val();

    if (selected.includes('all')) {
        $('#provincias_movilizacion option').each(function () {
            if ($(this).val() !== 'all') {
                $(this).prop('selected', true);
            } else {
                $(this).prop('selected', false);
            }
        });

        $('#provincias_movilizacion').trigger('change.select2');
    }

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
    "positionClass": "toast-top-right",
    "timeOut": "10000"

};

$('#correo').on('blur', function () {
    const correo = $(this).val();

    if (correo.length > 0) {
        $.ajax({
            url: '/api/validar-correo',
            method: 'POST',
            data: {
                correo: correo,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.valido) {
                    $('#mensaje-correo')
                        .removeClass('text-danger')
                        .addClass('text-success')
                        .removeClass('d-none')
                        .text(response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 409) {
                    const respuesta = xhr.responseJSON;
                    $('#mensaje-correo')
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .removeClass('d-none')
                        .text(respuesta.message);
                } else {
                    $('#mensaje-correo')
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .removeClass('d-none')
                        .text('Error al validar el correo, recuerde que no debe contener caracteres especiales como ñ, acentos o espacios.');
                }
            }
        });
    } else {
        $('#mensaje-correo').addClass('d-none').text('');
    }
});

function mostrarTitulos(titulos) {
    const contenedor = document.getElementById('contenedor_titulos');
    const hiddenContainer = document.getElementById('titulos-hidden');

    contenedor.innerHTML = '';
    hiddenContainer.innerHTML = '';

    if (titulos.length > 0) {
        document.getElementById('seccion_titulos').classList.remove('d-none');

        titulos.forEach((titulo, index) => {
            const div = document.createElement('div');
            div.className = 'alert alert-secondary';

            div.innerHTML = `
                <strong>Título:</strong> ${titulo.titulo} <br>
                <strong>Institución:</strong> ${titulo.institucion} <br>
            `;

            contenedor.appendChild(div);

            const inputTitulo = document.createElement('input');
            inputTitulo.type = 'hidden';
            inputTitulo.name = `titulos[${index}][titulo]`;
            inputTitulo.value = titulo.titulo;

            const inputInstitucion = document.createElement('input');
            inputInstitucion.type = 'hidden';
            inputInstitucion.name = `titulos[${index}][institucion]`;
            inputInstitucion.value = titulo.institucion;

            hiddenContainer.appendChild(inputTitulo);
            hiddenContainer.appendChild(inputInstitucion);
        });
    } else {
        document.getElementById('seccion_titulos').classList.add('d-none');
    }
}
