<!DOCTYPE html>
<html lang="es">

<head>
    <style>
        /* Estilo general del contenedor */
        #toast-container>div {
            background-color: rgba(0, 0, 0, 0.9) !important;
            color: #fff !important;
            font-weight: bold;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        /* Opcional: iconos de éxito, error, etc. */
        .toast-success {
            background-color: #28a745 !important;
        }

        .toast-error {
            background-color: #dc3545 !important;
        }

        .toast-info {
            background-color: #17a2b8 !important;
        }

        .toast-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

    </style>


    <meta charset="UTF-8">
    <title>Postulación SENESCYT</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @include('components.header')

</head>

<body>

    <div class="text-center my-4">
        <img src="{{ asset('image001.jpg') }}" alt="Logo SENESCYT" style="max-width:100%; height:auto; width:700px;">
    </div>

    <div class="container mb-5">

        {{-- Notificaciones --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Título --}}
        <h1 class="mb-3 text-center fw-bold">
            PLATAFORMA INFORMÁTICA PARA BOLSA EMPLEO<br>
            INSTITUTOS Y CONSERVATORIOS SUPERIORES PÚBLICOS
        </h1>

        <p class="text-muted">
            Por favor, completa el siguiente formulario para registrarte en la plataforma de bolsa de empleo.
            La SENESCYT no comparte información sobre datos personales de los ciudadanos a terceros.
        </p>


        {{-- FORMULARIO PRINCIPAL --}}
        <form id="registration-form" method="POST" action="{{ route('registration.store') }}"
            enctype="multipart/form-data">
            @csrf

            {{-- Secciones anteriores --}}
            @include('components.datos-personales')
            @include('components.declaracion-autorizacion')
            @include('components.informacion-relevante')

            {{-- Carga de documentos integrada --}}
            <div class="card mb-4 mt-4" id="upload-documento">
                <div class="card-header">
                    <h5><i class="bi bi-file-earmark-arrow-up-fill me-2"></i> Carga de Documentos</h5>
                    <small class="text-muted">Sube un único archivo PDF que contenga tu hoja de vida y los
                        respaldos.</small>
                    <strong>(Nombre del archivo: APELLIDOS_NOMBRES.pdf)</strong><br>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Archivo consolidado (PDF) <span
                                class="text-danger">*</span></label>
                        <input type="file" name="archivo" accept="application/pdf" class="form-control" required>
                    </div>
                    <div class="alert alert-info small">
                        <strong>Nota:</strong> CV + respaldos de experiencia/capacitación.<br>
                        <strong>Formato:</strong> PDF — <strong>Tamaño máx:</strong> 10MB.
                    </div>
                </div>
            </div>



            <div id="titulos-hidden"></div>


            {{-- Aceptación de términos --}}
            <div class="mb-4">
                <label class="form-label fw-bold">
                    Aceptación del Acuerdo de Uso de Datos Personales *
                </label>
                <p>
                    Antes de continuar, por favor revisa el siguiente acuerdo legal.
                </p>

                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#modalAcuerdo">
                    Leer acuerdo de uso de datos
                </button>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="acepta_terminos" name="acepta_terminos"
                        required>
                    <label class="form-check-label" for="acepta_terminos">
                        He leído y acepto los términos del acuerdo.
                    </label>
                </div>
            </div>

            <div class="modal fade" id="modalAcuerdo" tabindex="-1" aria-labelledby="modalAcuerdoLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalAcuerdoLabel">Acuerdo de Uso de Datos Personales</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body" style="text-align: justify;">
                            <p><strong>ACUERDO DE USO DE DATOS PERSONALES</strong></p>
                            <p>
                                La Secretaría de Educación Superior, Ciencia, Tecnología e Innovación (en adelante
                                SENESCYT)
                                pone en su conocimiento las condiciones de uso, fines y almacenamiento de los datos
                                personales
                                proporcionados por usted.
                            </p>

                            <p><strong>I.- Autorización para el almacenamiento y uso de información personal registrada
                                    en la plataforma informática.</strong></p>

                            <p>
                                En cumplimiento de la Ley Orgánica de Protección de Datos Personales, en su artículo 12,
                                respecto del derecho a la información de los titulares, se procede a informar lo
                                siguiente:
                            </p>

                            <ul>
                                <li><strong>Fines del tratamiento:</strong> Los datos personales proporcionados por cada
                                    usuario tendrán las siguientes finalidades: su uso será para el registro del perfil
                                    profesional. Su tratamiento será únicamente para cumplir el fin previamente
                                    descrito.</li>
                                <li><strong>Base legal para el tratamiento:</strong> La Ley Orgánica de Protección de
                                    Datos Personales en su artículo 7 establece el tratamiento legítimo de datos
                                    personales. Art. 7.- Tratamiento legítimo de datos personales (sic).- El tratamiento
                                    será legítimo y lícito si se cumple con alguna de las siguientes condiciones:
                                    <ul>
                                        <li>2) Que sea realizado por el responsable del tratamiento en cumplimiento de
                                            una obligación legal.</li>
                                        <li>4) Que el tratamiento de datos personales se sustente en el cumplimiento de
                                            una misión realizada en interés público o en el ejercicio de poderes
                                            públicos conferidos al responsable (...)</li>
                                    </ul>
                                </li>
                                <li><strong>Tipos de tratamiento:</strong> Los datos proporcionados por los usuarios y
                                    aquellos obtenidos de las distintas bases de datos generadas por las entidades
                                    pertenecientes al Sistema Nacional de Registros Públicos se almacenarán en la
                                    plataforma informática de educación continua para el registro e inscripción.</li>
                                <li>Los datos personales de los usuarios serán protegidos con todas las medidas de
                                    seguridad establecidas en la Ley Orgánica de Protección de Datos Personales y las
                                    disposiciones del Esquema Gubernamental de Seguridad de la Información.</li>
                                <li>Los datos de los usuarios y los que se generen durante el registro e inscripción en
                                    la plataforma informática serán debidamente almacenados por la SENESCYT,
                                    garantizando la seguridad, integridad, disponibilidad y confidencialidad.</li>
                                <li><strong>Identidad y datos de contacto del responsable del tratamiento de datos
                                        personales:</strong> El titular de los datos, de ser el caso, puede ejercer sus
                                    derechos de acceso, rectificación, cancelación, oposición y otros reconocidos en la
                                    Ley Orgánica de Protección de Datos Personales, en la dirección: Alpallana E7-183
                                    entre Av. Diego de Almagro y Whymper. Código Postal: 170518 Quito - Ecuador, o en
                                    nuestros medios digitales.</li>
                            </ul>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>



            {{-- Botón Final --}}
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg w-100" id="btn_guardar_formulario"
                    disabled>Enviar</button>
            </div>




        </form>
    </div>
    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/datos-personales.js') }}"></script>
    <script src="{{ asset('js/datos-personales-tipo.js') }}"></script>

    <script>
        window.registrationConsultarCedulaUrl = "{{ route('registration.consultarCedula') }}";
    </script>

    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "preventDuplicates": true,
            "timeOut": "10000",
            "extendedTimeOut": "2000",
            "showDuration": "300",
            "hideDuration": "1000",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>

    <div class="modal fade" id="modalCargaDatos" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg text-center py-5 px-4"
                style="background: rgba(255, 255, 255, 0.95); border-radius: 1rem;">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <div class="spinner-border text-primary mb-4" role="status" style="width: 3rem; height: 3rem;">
                    </div>
                    <h5 class="fw-semibold">Cargando datos...</h5>
                    <p class="text-muted small">Por favor, espera un momento</p>
                </div>
            </div>
        </div>
    </div>



</body>

</html>
