<div class="card mb-4" id="upload-documento">
    <div class="card-header">
        <h5><i class="bi bi-file-earmark-arrow-up-fill me-2"></i> Carga de Documentos</h5>
        <small class="text-muted">Sube un único archivo PDF que contenga tu hoja de vida y los respaldos.</small>
    </div>
    <div class="card-body">
        <form id="form-carga-documento" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Archivo consolidado (PDF) <span class="text-danger">*</span></label>
                <input type="file" name="archivo" accept="application/pdf" class="form-control" required>
            </div>
            <div class="alert alert-info small">
                <strong>Nota:</strong> CV + respaldos de experiencia/capacitación.<br>
                <strong>Formato:</strong> PDF — <strong>Tamaño máx:</strong> 10MB.
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-upload"></i> Subir documento
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="toast-documento" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                ✅ Documento cargado correctamente.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
