@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-3">{{ $titulo }}</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $activo ? route('activos.update', $activo->cod_odoo) : route('activos.store') }}" method="POST">
                @csrf
                @if($activo)
                    @method('PUT')
                @endif

                <!-- Código Odoo -->
                <div class="mb-3">
                    <label for="cod_odoo" class="form-label">Código Odoo <span class="text-danger">*</span></label>
                    <input 
                        type="number" 
                        class="form-control @error('cod_odoo') is-invalid @enderror" 
                        id="cod_odoo" 
                        name="cod_odoo"
                        value="{{ old('cod_odoo', $activo?->cod_odoo) }}"
                        {{ $activo ? 'readonly' : '' }}
                        required
                    >
                    @error('cod_odoo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nombre -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('nombre') is-invalid @enderror" 
                        id="nombre" 
                        name="nombre"
                        value="{{ old('nombre', $activo?->nombre) }}"
                        required
                    >
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Valor Costo -->
                <div class="mb-3">
                    <label for="valor_costo" class="form-label">Valor Costo <span class="text-danger">*</span></label>
                    <input 
                        type="number" 
                        class="form-control @error('valor_costo') is-invalid @enderror" 
                        id="valor_costo" 
                        name="valor_costo"
                        step="0.01"
                        value="{{ old('valor_costo', $activo?->valor_costo) }}"
                        required
                    >
                    @error('valor_costo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Unidad -->
                <div class="mb-3">
                    <label for="unidad" class="form-label">Unidad <span class="text-danger">*</span></label>
                    <select 
                        class="form-select @error('unidad') is-invalid @enderror" 
                        id="unidad" 
                        name="unidad"
                        required
                    >
                        <option value="">-- Seleccionar unidad --</option>
                        <option value="UI" {{ old('unidad', $activo?->unidad) === 'UI' ? 'selected' : '' }}>UI</option>
                        <option value="UFC" {{ old('unidad', $activo?->unidad) === 'UFC' ? 'selected' : '' }}>UFC</option>
                        <option value="mg" {{ old('unidad', $activo?->unidad) === 'mg' ? 'selected' : '' }}>mg</option>
                        <option value="mcg" {{ old('unidad', $activo?->unidad) === 'mcg' ? 'selected' : '' }}>mcg</option>
                    </select>
                    @error('unidad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Densidad -->
                <div class="mb-3">
                    <label for="densidad" class="form-label">Densidad <span class="text-danger">*</span></label>
                    <input 
                        type="number" 
                        class="form-control @error('densidad') is-invalid @enderror" 
                        id="densidad" 
                        name="densidad"
                        step="0.0001"
                        value="{{ old('densidad', $activo?->densidad) }}"
                        required
                    >
                    @error('densidad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ $activo ? 'Actualizar' : 'Crear' }}
                    </button>
                    <a href="{{ route('activos.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
