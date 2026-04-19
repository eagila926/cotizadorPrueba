@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Gestión de Activos</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('activos.create') }}" class="btn btn-primary">
            + Nuevo Activo
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Código Odoo</th>
                    <th>Nombre</th>
                    <th>Valor Costo</th>
                    <th>Unidad</th>
                    <th>Densidad</th>
                    <th style="width: 160px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activos as $activo)
                    <tr>
                        <td>{{ $activo->cod_odoo }}</td>
                        <td>{{ $activo->nombre }}</td>
                        <td>${{ number_format($activo->valor_costo, 2) }}</td>
                        <td>{{ $activo->unidad }}</td>
                        <td>{{ number_format($activo->densidad, 4) }}</td>
                        <td>
                            <a href="{{ route('activos.edit', $activo->cod_odoo) }}" class="btn btn-sm btn-outline-secondary">
                                Editar
                            </a>
                            <form action="{{ route('activos.destroy', $activo->cod_odoo) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Desea eliminar este activo?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No hay activos registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
