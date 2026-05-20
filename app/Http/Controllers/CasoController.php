<?php

namespace App\Http\Controllers;

use App\Exports\CasosExport;
use App\Http\Requests\CasoRequest;
use App\Models\Caso;
use App\Models\Localidad;
use App\Models\TipoExpediente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CasoController extends Controller
{
    public function index(Request $request)
    {
        $query = Caso::with(['tipoExpediente', 'localidad']);

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('apellido_nombre', 'like', "%$b%")
                  ->orWhere('dni', 'like', "%$b%")
                  ->orWhere('nro_legajo', 'like', "%$b%")
                  ->orWhere('nro_expediente', 'like', "%$b%");
            });
        }

        if ($request->filled('tipo_expediente_id')) {
            $query->where('tipo_expediente_id', $request->tipo_expediente_id);
        }

        if ($request->filled('localidad_id')) {
            $query->where('localidad_id', $request->localidad_id);
        }

        if ($request->filled('archivado') && $request->archivado !== '') {
            $query->where('archivado', $request->archivado);
        }

        $casos      = $query->orderBy('fecha_recepcion', 'desc')->paginate(15)->withQueryString();
        $tipos      = TipoExpediente::orderBy('nombre')->get();
        $localidades = Localidad::orderBy('nombre')->get();

        return view('casos.index', compact('casos', 'tipos', 'localidades'));
    }

    public function create()
    {
        $tipos       = TipoExpediente::orderBy('nombre')->get();
        $localidades = Localidad::orderBy('nombre')->get();
        return view('casos.create', compact('tipos', 'localidades'));
    }

    public function store(CasoRequest $request)
    {
        Caso::create($request->validated());
        return redirect()->route('casos.index')->with('success', 'Caso registrado correctamente.');
    }

    public function show(Caso $caso)
    {
        $caso->load(['tipoExpediente', 'localidad']);
        return view('casos.show', compact('caso'));
    }

    public function edit(Caso $caso)
    {
        $tipos       = TipoExpediente::orderBy('nombre')->get();
        $localidades = Localidad::orderBy('nombre')->get();
        return view('casos.edit', compact('caso', 'tipos', 'localidades'));
    }

    public function update(CasoRequest $request, Caso $caso)
    {
        $caso->update($request->validated());
        return redirect()->route('casos.show', $caso)->with('success', 'Caso actualizado correctamente.');
    }

    public function destroy(Caso $caso)
    {
        $caso->delete();
        return redirect()->route('casos.index')->with('success', 'Caso eliminado.');
    }

    public function pdfCaso(Caso $caso)
    {
        $caso->load(['tipoExpediente', 'localidad']);

        $posiblesMembrete = [
            base_path('public/img/membrete.png'),
            base_path('../public_html/img/membrete.png'),
            base_path('../public/img/membrete.png'),
        ];
        $membrete = collect($posiblesMembrete)->first(fn($p) => file_exists($p));

        $pdf = Pdf::loadView('casos.pdf_caso', compact('caso', 'membrete'))->setPaper('a4', 'portrait');
        $nombre = 'caso-' . str_replace('/', '-', $caso->nro_legajo) . '.pdf';
        return $pdf->download($nombre);
    }

    public function exportExcel()
    {
        return Excel::download(new CasosExport, 'casos-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf()
    {
        $casos = Caso::with(['tipoExpediente', 'localidad'])
            ->orderBy('fecha_recepcion', 'desc')
            ->get();
        $pdf = Pdf::loadView('casos.pdf', compact('casos'))->setPaper('a4', 'landscape');
        return $pdf->download('casos-' . now()->format('Y-m-d') . '.pdf');
    }
}
