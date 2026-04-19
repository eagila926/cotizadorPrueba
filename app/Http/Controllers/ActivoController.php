<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivoController extends Controller
{
    // Verificar que el usuario sea admin
    protected function authorizeAdmin()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['ADMIN'])) {
            abort(403, 'No tiene permisos para gestionar activos.');
        }
    }

    // Listar todos los activos
    public function index()
    {
        $this->authorizeAdmin();

        $activos = Activo::orderBy('cod_odoo')->get();

        return view('activos.index', compact('activos'));
    }

    // Mostrar formulario para crear nuevo activo
    public function create()
    {
        $this->authorizeAdmin();

        return view('activos.form', [
            'activo' => null,
            'titulo' => 'Nuevo Activo'
        ]);
    }

    // Guardar nuevo activo
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'cod_odoo'      => 'required|integer|unique:activos,cod_odoo',
            'nombre'        => 'required|string|max:255',
            'valor_costo'   => 'required|numeric|min:0',
            'unidad'        => 'required|string|max:10',
            'densidad'      => 'required|numeric|min:0',
        ]);

        Activo::create([
            'cod_odoo'      => $request->cod_odoo,
            'nombre'        => $request->nombre,
            'valor_costo'   => $request->valor_costo,
            'unidad'        => $request->unidad,
            'densidad'      => $request->densidad,
            'factor'        => 0,
            'minimo'        => 0,
            'maximo'        => 0,
            'factor_venta'  => 0
        ]);

        return redirect()->route('activos.index')
            ->with('success', 'Activo creado correctamente');
    }

    // Mostrar formulario para editar activo
    public function edit(Activo $activo)
    {
        $this->authorizeAdmin();

        return view('activos.form', [
            'activo' => $activo,
            'titulo' => 'Editar Activo'
        ]);
    }

    // Actualizar activo
    public function update(Request $request, Activo $activo)
    {
        $this->authorizeAdmin();

        $request->validate([
            'nombre'        => 'required|string|max:255',
            'valor_costo'   => 'required|numeric|min:0',
            'unidad'        => 'required|string|max:10',
            'densidad'      => 'required|numeric|min:0',
        ]);

        $activo->update([
            'nombre'        => $request->nombre,
            'valor_costo'   => $request->valor_costo,
            'unidad'        => $request->unidad,
            'densidad'      => $request->densidad,
        ]);

        return redirect()->route('activos.index')
            ->with('success', 'Activo actualizado correctamente');
    }

    // Eliminar activo
    public function destroy(Activo $activo)
    {
        $this->authorizeAdmin();

        $activo->delete();

        return redirect()->route('activos.index')
            ->with('success', 'Activo eliminado correctamente');
    }
}
