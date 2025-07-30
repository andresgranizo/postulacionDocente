<form method="POST" action="{{ route('documentos.upload') }}" enctype="multipart/form-data">
    @csrf
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-file-earmark-arrow-up-fill me-2"></i> Carga de Documentos</h5>
            <small class="text-muted">Sube un único archivo PDF que contenga tu hoja de vida y los respaldos de experiencia y capacitaciones.</small>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="archivo" class="form-label fw-semibold">Archivo consolidado (PDF) <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="archivo" name="archivo" accept="application/pdf" required>
            </div>
            <div class="alert alert-info small">
                <strong>Nota:</strong> El archivo debe incluir tu CV, experiencia profesional y capacitaciones.<br>
                <strong>Formato:</strong> PDF — <strong>Tamaño máx:</strong> 10MB.
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-upload me-1"></i> Subir documento
                </button>
            </div>
        </div>
    </div>
</form>
