
function actualizarCamposPorTipo() {
    const tipo = $('#tipo_documento').val();
    const $input = $('#numero_identificacion');
    const $label = $('#label_numero_identificacion');

    $('#campo_apellidos_nombres').addClass('d-none');
    $('#mensaje_llenado_previo').addClass('d-none');

    // Reset campos
    $('#campo_fecha_nacimiento').addClass('d-none');
    $('#campo_pais_nacionalidad').addClass('d-none');
    $('#campo_codigo_dactilar').addClass('d-none');
    $('#btn_buscar_cedula').hide();

    if (tipo === 'cedula') {
        $label.text('Cédula de Identidad *');
        $input.attr('name', 'cedula');
        $('#btn_buscar_cedula').show();
        $('#campo_codigo_dactilar').removeClass('d-none');
    } else if (tipo === 'pasaporte') {
        $label.text('Número de Pasaporte *');
        $input.attr('name', 'cedula');
        $('#campo_fecha_nacimiento').removeClass('d-none');
        $('#campo_pais_nacionalidad').removeClass('d-none');
        $('#mensaje_llenado_previo').removeClass('d-none');
    }

    consultarVisaSiAplica();
}

function cargarPaises() {
    $.get('api/paises', function (data) {
        const $select = $('#pais_nacionalidad');
        $select.empty().append('<option value="">Seleccione un país</option>');

        data.forEach(pais => {
            const option = $('<option>', {
                value: pais.Id,
                text: pais.Gentilicio
            });
            $select.append(option);
        });
    });
}

function esCedulaEcuatoriana(numero) {
    return /^\d{10}$/.test(numero);
}

function consultarVisaSiAplica() {
    const tipo = $('#tipo_documento').val();
    if (tipo !== 'pasaporte') return;

    const pasaporte = $('#numero_identificacion').val().trim();
    const fechaNacimiento = $('#fecha_nacimiento').val().trim();
    const idNacionalidad = $('#pais_nacionalidad').val();

    if (pasaporte && fechaNacimiento && idNacionalidad) {
        clearTimeout(timerConsultaVisa);
        timerConsultaVisa = setTimeout(() => {
            consultarVisa(pasaporte, idNacionalidad, fechaNacimiento);
        }, 500);
    }
}

function consultarVisa(pasaporte, nacionalidad, fechaNacimiento) {
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

    toastr.clear();
    if (esCedulaEcuatoriana(pasaporte)) {
        toastr.error('⚠️ Este número parece una cédula ecuatoriana. Por favor, verifique el tipo de identificación.');
        $('#apellidos_nombres').val('');
        $('#campo_apellidos_nombres').addClass('d-none');
        $('#mensaje_llenado_previo').removeClass('d-none');
        return;
    }

    $.ajax({
        url: '/api/visa/consultar',
        method: 'POST',
        data: { pasaporte, idNacionalidad: nacionalidad, fechaNacimiento },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        beforeSend: function () {
            mostrarSpinner(); // ✅ se muestra el spinner antes de iniciar
        },
        success: function (data) {
            ocultarSpinner();
            if (data && data.nombre) {
                $('#apellidos_nombres').val(data.nombre);
                $('#campo_apellidos_nombres').removeClass('d-none');
                $('#mensaje_llenado_previo').addClass('d-none');
            }
            else {
                toastr.warning('⚠️ Verifique los datos ingresados. No se encontraron resultados.');
                $('#apellidos_nombres').val('');
                $('#campo_apellidos_nombres').addClass('d-none');
                $('#mensaje_llenado_previo').removeClass('d-none');
            }
        },
        error: function () {
            toastr.error('❌ Error al consultar la visa. Intente más tarde.');
            $('#apellidos_nombres').val('');
            $('#campo_apellidos_nombres').addClass('d-none');
            $('#mensaje_llenado_previo').removeClass('d-none');
        }
    });
}

let timerConsultaVisa;

$(document).ready(function () {
    actualizarCamposPorTipo();
    cargarPaises();

    $('#tipo_documento').on('change', actualizarCamposPorTipo);

    $('#numero_identificacion, #pais_nacionalidad, #fecha_nacimiento').on('input change', consultarVisaSiAplica);
});
