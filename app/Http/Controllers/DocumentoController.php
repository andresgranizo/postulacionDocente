<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;


class DocumentoController extends Controller
{
    public function create()
    {
        $contacts = Contact::all();
        return view('documentos.create', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:10240', // Máximo 10MB
        ]);

        $contact = Contact::where('cedula', session('cedula'))->firstOrFail();

        // Elimina documento anterior si existe
        $anterior = Documento::where('contact_id', $contact->id)->first();
        if ($anterior) {
            Storage::delete($anterior->ruta);
            $anterior->delete();
        }

        $archivo = $request->file('archivo');
        $nombreOriginal = $archivo->getClientOriginalName();
        $ruta = $archivo->storeAs(
            'public/documentos_cv',
            $contact->cedula . '_cv.pdf'
        );

        Documento::create([
            'contact_id'      => $contact->id,
            'tipo'            => 'cv', // fijo, ya no editable
            'ruta'            => $ruta,
            'nombre_original' => $nombreOriginal,
        ]);

        return back()->with('success', '✅ Documento cargado exitosamente.');
    }

}
