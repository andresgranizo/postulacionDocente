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


</body>

</html>
