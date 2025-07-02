<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Formulario de Actualización - Segundo Periodo 2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-wrapper {
            max-width: 700px;
            margin: 50px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        .form-wrapper h1 {
            font-size: 24px;
            font-weight: bold;
        }

        .form-wrapper p {
            font-size: 14px;
        }

        .form-wrapper .alert {
            font-size: 14px;
        }

        .form-wrapper label {
            font-weight: bold;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>

<body>

    <div class="form-wrapper">

        <h1 class="mb-3">Formulario de Actualización de Correo 2do Periodo 2025</h1>

        <p>
            El presente formulario tiene como finalidad el tratamiento de datos personales, específicamente la
            actualización de correo electrónico por petición del titular para el proceso: <strong>Registro Nacional del
                Segundo Período 2025</strong>, en concordancia con la Ley de Protección de Datos Personales.
        </p>

        <p>
            El nuevo correo se actualizará en un plazo máximo de 24 horas, luego de este tiempo <strong>NO</strong>
            llegará ninguna notificación de confirmación a tu correo personal. Deberás ingresar a la opción:
            <strong>recuperar contraseña</strong> y continuar con los pasos que serán enviados al correo que
            registraste.
        </p>

        <h6 class="mt-4">Datos que se recolecta:</h6>
        <p>
            En los portales y sistemas en ambiente web de la Secretaría de Educación Superior, Ciencia, Tecnología e
            Innovación SENESCYT, acorde a la funcionalidad que accede el ciudadano en los diferentes portales, se
            recolecta la siguiente información:
        </p>

        <p><strong>Formulario de Actualización de Correo 2do Periodo 2025</strong></p>

        <p class="text-muted mb-4">La SENESCYT no comparte información sobre datos personales de los ciudadanos a
            terceros.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form id="registration-form" method="POST" action="{{ route('registration.store') }}">
            @csrf
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
                <div id="msj_cedula" class="form-text text-danger"></div>
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

            <p> <strong>Recuerda verificar que tu correo sea válido ya que el sistema solo permite el registro una sola
                    vez. </strong></p>
            <div class="mb-3">
                <label for="correo_electronico_principal" class="form-label">Correo Electrónico *</label>
                <input type="text" id="correo_electronico_principal" name="correo" class="form-control"
                    value="{{ old('correo') }}" pattern="^[^\s,]+@[^\s,]+\.[^\s,]+$"
                    title="El correo no debe contener espacios ni comas."
                    oninvalid="this.setCustomValidity('El correo no debe contener espacios ni comas.')"
                    oninput="this.setCustomValidity('')">
            </div>

            <div class="mb-3">
              <label for="fecha_expiracion" class="form-label">Fecha de Expiración de la Cédula *</label>
              <input type="date"
                     id="fecha_expiracion"
                     name="fecha_expiracion"
                     class="form-control"
                     value="{{ old('fecha_expiracion') }}"
                     lang="es"
                     placeholder="aaaa-mm-dd">
          </div>


            <!-- Botón para ver política -->
            <div class="mb-3">
                <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#politicaPrivacidad" aria-expanded="false" aria-controls="politicaPrivacidad">
                    Leer Política de Privacidad y Términos de Uso
                </button>

                <div class="collapse mt-3" id="politicaPrivacidad">
                    <div class="card card-body" style="max-height: 300px; overflow-y: scroll; font-size: 0.95rem;">
                        <h5>I. POLÍTICA DE PRIVACIDAD</h5>

                        <p><strong>Alcance:</strong><br>
                            Esta política de protección y tratamiento de datos de carácter personal se aplica a todos
                            los datos personales que se recolecten, almacenen, manejen y usen en el acceso a los
                            portales y sistemas web que tiene habilitado la Secretaría de Educación Superior, Ciencia,
                            Tecnología e Innovación.</p>

                        <p>Esta política es de acceso público, motivo por el cual cualquier persona puede acceder a la
                            información publicada en relación con el Tratamiento de Datos Personales y la protección de
                            su información contenida en las Bases de Datos.</p>

                        <p><strong>Datos que se recolectan:</strong><br>
                            En los portales y sistemas web de la SENESCYT, según la funcionalidad accedida, se recolecta
                            la siguiente información:</p>

                        <ul>
                            <li><strong>PORTAL WEB</strong></li>
                            <li><strong>DATOS QUE SE RECOLECTAN</strong></li>
                        </ul>

                        <p>La SENESCYT no comparte información sobre datos personales de los ciudadanos a terceros.</p>

                        <p><strong>Finalidad:</strong></p>
                        <ul>
                            <li>Mejorar el contenido, usabilidad y experiencia de los usuarios.</li>
                            <li>Recibir retroalimentación del ciudadano sobre la información publicada.</li>
                        </ul>

                        <p><strong>Notificaciones de cambios:</strong><br>
                            La SENESCYT podrá actualizar esta política y notificará los cambios sustanciales mediante
                            aviso en el portal correspondiente.</p>

                        <p><strong>Uso de cookies:</strong><br>
                            Se utilizan cookies para mejorar la navegación, calidad del sitio y experiencia del usuario.
                            No se comparte esta información.</p>

                        <p><strong>Medidas de seguridad:</strong><br>
                            Se mantienen protocolos estrictos para garantizar que los usuarios con acceso a datos
                            personales no divulguen la información.</p>

                        <p><strong>Base legal:</strong></p>
                        <ul>
                            <li>Ley de Comercio Electrónico, Firmas Electrónicas y Mensajes de Datos (Ley No. 2002-67).
                            </li>
                            <li>Plan de la Sociedad de la Información y del Conocimiento: Acuerdo Ministerial Nro.
                                016-2018.</li>
                            <li>Código Orgánico de la Economía Social de los Conocimientos: Disposición VIGÉSIMA
                                SÉPTIMA.</li>
                            <li>Ley Orgánica de Protección de Datos Personales y su Reglamento.</li>
                        </ul>

                        <h5 class="mt-4">II. TÉRMINOS Y CONDICIONES DE USO</h5>

                        <p>El uso del portal implica la aceptación de estos términos. La SENESCYT dispone de los
                            portales web para brindar información conforme a sus competencias.</p>

                        <p>El titular consiente libre, específica e informadamente el tratamiento de sus datos
                            personales.</p>

                        <p><strong>Responsabilidad:</strong><br>
                            La SENESCYT es responsable únicamente del tratamiento de los datos recolectados directamente
                            en sus portales. No se responsabiliza del uso inadecuado por parte del usuario.</p>

                        <p><strong>Obligaciones del usuario:</strong></p>
                        <ul>
                            <li>No dañar, inutilizar, modificar o deteriorar los canales electrónicos o sus contenidos.
                            </li>
                            <li>No usar versiones modificadas de sistemas para acceder sin autorización.</li>
                            <li>No interferir o interrumpir el acceso y funcionamiento del sitio.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Checkbox obligatorio -->
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="acepta_politica" id="acepta_politica" required
                    oninvalid="this.setCustomValidity('Debes aceptar la Política de Privacidad y los Términos de Uso.')"
                    oninput="this.setCustomValidity('')">
                <label class="form-check-label" for="acepta_politica">
                    He leído y acepto la Política de Privacidad y los Términos de Uso
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Enviar</button>
            <div class="mb-4">
                <p><strong>Si tienes alguna duda, no dudes en contactarnos a través de nuestros canales
                        oficiales:</strong></p>
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="https://www.facebook.com/EduSuperiorEC" target="_blank" rel="noopener">
                            Facebook: EduSuperiorEC
                        </a>
                    </li>
                    <li>
                        <a href="https://twitter.com/EduSuperiorEC" target="_blank" rel="noopener">
                            X: EduSuperiorEC
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/EduSuperior.Ec" target="_blank" rel="noopener">
                            Instagram: EduSuperior.Ec
                        </a>
                    </li>
                </ul>
            </div>

        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
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

        $('#tipo_documento').on('change', toggleBuscarSegunTipo);
        $(document).ready(toggleBuscarSegunTipo);
    </script>

    <script>
        $('#numero_identificacion').on('input', function() {
            let v = $(this).val().trim();
            if (v.length === 10) {
                $('#btn_buscar_cedula').prop('disabled', false);
                $('#msj_cedula').text('');
            } else {
                $('#btn_buscar_cedula').prop('disabled', true);
            }
        });

        $('#btn_buscar_cedula').click(function() {
            const cedula = $('#numero_identificacion').val().trim();
            $('#msj_cedula').css('color', 'black').text('🔍 Buscando...');
            $('#apellidos_nombres').val('').prop('readonly', false);

            $.ajax({
                url: '{{ route('registration.consultarCedula') }}',
                method: 'POST',
                data: {
                    cedula
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success(data) {
                    if (data.error) {
                        $('#msj_cedula').css('color', 'red').text('❌ ' + data.message);
                    } else {
                        $('#apellidos_nombres')
                            .val(data.nombre)
                            .prop('readonly', true)
                            .addClass('filled-by-search');
                        $('#msj_cedula').css('color', 'green').text('✅ Datos encontrados');
                    }
                },
                error() {
                    $('#msj_cedula').css('color', 'red').text('❌ Error en la consulta');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
      flatpickr("#fecha_expiracion", {
        locale: "es",
        dateFormat: "Y-m-d", //  Formato año-mes-día
        altInput: true,
        altFormat: "Y-m-d"
      });
    </script>
    <script>
        // Crea el mensaje CNE debajo del input si no existe
        const cneMsg = $('<div>')
            .attr('id', 'msj_cne')
            .addClass('form-text text-danger mt-1');
        $('#numero_identificacion').closest('.mb-3').append(cneMsg);

        $('#numero_identificacion').on('input', function() {
            let cedula = $(this).val().trim();
            if (cedula.length === 10) {
                $('#msj_cedula').css('color', 'black').text('🔍 Buscando Registro Civil...');
                $('#msj_cne').css('color', 'black').text('🔍 Consultando habilitación CNE...');
                $('#apellidos_nombres').val('').prop('readonly', false);

                // Consulta Registro Civil
                $.ajax({
                    url: '{{ route('registration.consultarCedula') }}',
                    method: 'POST',
                    data: { cedula },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success(data) {
                        if (data.error) {
                            $('#msj_cedula').css('color', 'red').text('❌ ' + data.message);
                        } else {
                            $('#apellidos_nombres')
                                .val(data.nombre)
                                .prop('readonly', true)
                                .addClass('filled-by-search');
                            $('#msj_cedula').css('color', 'green').text('✅ Datos Registro Civil encontrados');
                        }
                    },
                    error() {
                        $('#msj_cedula').css('color', 'red').text('❌ Error al consultar Registro Civil');
                    }
                });

                // Consulta CNE
                $.ajax({
                    url: '{{ route('registration.consultarCne') }}',
                    method: 'POST',
                    data: { cedula },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success(data) {
                        if (data.error) {
                            $('#msj_cne').css('color', 'red').text('❌ ' + data.message);
                        } else {
                            if (data.habilitado === 'SI') {
                                $('#msj_cne').css('color', 'green').text('✅ Ciudadano habilitado para trámite público');
                            } else {
                                $('#msj_cne').css('color', 'red').text('❌ Ciudadano NO habilitado para trámite público');
                            }
                        }
                    },
                    error() {
                        $('#msj_cne').css('color', 'red').text('❌ Error al consultar CNE');
                    }
                });

            } else {
                // Si la cédula se borra o no tiene 10 dígitos
                $('#msj_cedula').text('');
                $('#msj_cne').text('');
                $('#apellidos_nombres').val('').prop('readonly', false);
            }
        });
    </script>




</body>

</html>
