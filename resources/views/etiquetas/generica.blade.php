@php
    $prefill = session('etiqueta_preview', []);
    $soPrefill = $prefill['so'] ?? null;
    $medicoPrefill = $prefill['medico'] ?? null;

    function nombreCorto($full) {
        $full = trim((string)$full);
        if ($full === '') return null;
        $p = preg_split('/\s+/', $full);
        $first = $p[0] ?? '';
        $last  = $p[count($p)-1] ?? '';
        return trim($first.' '.$last);
    }
    $medicoCorto = nombreCorto($medicoPrefill);

    function abreviarNombreActivoView($nombre) {
        $nombre = preg_replace('/\s*\(.*?\)\s*/', '', (string)$nombre);
        if (stripos($nombre, 'BIFIDUMBACTERIUM') === 0) {
            return 'BIFIDUM' . substr($nombre, strlen('BIFIDUMBACTERIUM'));
        } elseif (stripos($nombre, 'LACTOBACILUS') === 0) {
            return 'LACTO' . substr($nombre, strlen('LACTOBACILUS'));
        }
        return $nombre;
    }

    function fmtCantidad($v, $maxDec = 2) {
        if (!is_numeric($v)) return (string)$v;
        $n = (float)$v;
        $s = number_format($n, $maxDec, '.', '');
        $s = rtrim(rtrim($s, '0'), '.');
        return $s === '' ? '0' : $s;
    }

    function fmtCantidadUfc($v, $unidad) {
        $u = trim(strtoupper((string)$unidad));
        if ($u !== 'UFC' || !is_numeric($v)) {
            return fmtCantidad($v, 2);
        }

        $n = (int) round($v);

        if ($n === 1000000) {
            return '1 Millon';
        }
        if ($n === 2000000) {
            return '2 Millones';
        }
        if ($n === 1000000000) {
            return '1 Billon';
        }
        if ($n === 2000000000) {
            return '2 Billones';
        }
        if ($n >= 1000000000 && $n % 1000000000 === 0) {
            return ($n / 1000000000) . ' Billones';
        }
        if ($n >= 1000000 && $n % 1000000 === 0) {
            return ($n / 1000000) . ' Millones';
        }

        return fmtCantidad($v, 2);
    }

    function unidadPrioritaria($unidad) {
        $u = trim(strtoupper((string)$unidad));
        return match ($u) {
            'UI', 'UND', 'UNIDADES' => 1,
            'MG', 'MILLIGRAMO', 'MILLIGRAMOS' => 2,
            'MCG', 'MICROGRAMO', 'MICROGRAMOS' => 3,
            'UFC' => 4,
            default => 5,
        };
    }

    // === Distribución asimétrica: la última columna debe tener menos elementos ===
    function distribuirActivosAsimetrico($items, int $cols = 2) {
        $cols = max(1, $cols);
        $n = $items->count();

        if ($n === 0) {
            return array_fill(0, $cols, collect());
        }

        if ($cols === 2) {
            $firstCount = (int) ceil($n / 2);
            $counts = [$firstCount, $n - $firstCount];
        } elseif ($cols === 3) {
            $lastCount = max(1, (int) floor($n / 3) - 1);
            $remaining = $n - $lastCount;
            $middleCount = (int) floor($remaining / 2);
            $firstCount = $remaining - $middleCount;
            $counts = [$firstCount, $middleCount, $lastCount];
        } else {
            $base = (int) floor($n / $cols);
            $remainder = $n % $cols;
            $counts = array_fill(0, $cols, $base);
            for ($i = 0; $i < $remainder; $i++) {
                $counts[$i]++;
            }
        }

        $columns = array_map(fn() => collect(), range(0, $cols - 1));
        $index = 0;

        foreach ($items as $it) {
            while ($index < $cols && $columns[$index]->count() >= $counts[$index]) {
                $index++;
            }
            if ($index >= $cols) {
                $index = $cols - 1;
            }
            $columns[$index]->push($it);
        }

        return $columns;
    }

    $items = $items ?? collect();
    $items = $items->reject(function($it) {
        // Quitar celulosa de la composición
        return (int)($it->cod_odoo ?? 0) === 3291;
    })->values();
    
    $items = $items->sortBy(function($it) {
        $prioridad = unidadPrioritaria($it->unidad ?? '');
        $nombre = strtoupper(trim((string)($it->activo ?? '')));
        return sprintf('%02d-%s', $prioridad, $nombre);
    })->values();

    $totalActivos = $items->count();

    // columnas: 2 por defecto, 3 si vienen muchos
    $columnas = 2;
    if ($totalActivos >= 18) $columnas = 3;

    // tipografía dinámica para compactar
    $fsActivo = 26; // px - aumentado de 22
    $fsCant  = 26;  // aumentado de 22
    $lh      = 1.05;

    if ($totalActivos >= 9 && $totalActivos <= 12) { $fsActivo = 22; $fsCant = 22; }
    elseif ($totalActivos >= 13 && $totalActivos <= 16) { $fsActivo = 20; $fsCant = 20; }
    elseif ($totalActivos >= 17 && $totalActivos <= 20) { $fsActivo = 18; $fsCant = 18; }
    elseif ($totalActivos >= 21 && $totalActivos <= 24) { $fsActivo = 16; $fsCant = 16; $lh = 1.0; }
    elseif ($totalActivos >= 25) { $fsActivo = 14; $fsCant = 14; $lh = 1.0; }

    // columnas asimétricas
    $cols = distribuirActivosAsimetrico($items, $columnas);

    // Pie
    $tomas = (int) ($formula->tomas_diarias ?? 3);
    $dias  = 30;
    $contieneCaps = $tomas * $dias;
    
    // Número de frascos
    $numFrascos = (int) ($numFrascos ?? 1);
    
    // Cápsulas por frasco
    $capsPerFrasco = $numFrascos > 0 ? (int)($contieneCaps / $numFrascos) : $contieneCaps;

    // Título
    $nombreEtiqueta = (string) ($formula->nombre_etiqueta ?? '');
    $fontSizeTitulo = (mb_strlen($nombreEtiqueta) > 31) ? '25px' : '28px';

    $fechaElaboracion = $fechaElaboracion ?? now()->format('Y-m-d');
@endphp

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Etiqueta – {{ $formula->codigo }}</title>
<style>
  body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
  .wrap { padding: 10px 12px; } /* margen interno general, SIN fijar tamaño total */

  /* “Área blanca” lógica: simula tu layout sin forzar 50x142 */
  .label-grid {
    display: grid;
    grid-template-columns: 22% 1fr 42px;  /* izq reservado / centro / banda vertical */
    gap: 10px;
    align-items: start;
  }

  /* Bloque izquierdo: NO mostramos nada (solo reserva espacio). */
  .left-spacer { min-height: 1px; }

  .center { min-width: 0; }

  /* ✅ TÍTULO centrado */
  .title{
    width: 100%;                 /* asegura que ocupe todo el ancho */
    display: block;              /* comportamiento de bloque completo */
    text-align: center;          /* centra el texto */
    font-size: {{ $fontSizeTitulo }};
    font-weight: 800;
    margin: 6px 0 10px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Composición */
  .comp {
    display: grid;
    grid-template-columns: repeat({{ $columnas }}, 1fr);
    gap: 18px;
  }

  .comp-col { min-width: 0; }

  .comp-row {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
    align-items: baseline;
    margin: 2px 0;
  }

  /* ✅ Composición SIN negrita */
  .comp-nombre {
    font-weight: 400;              /* ✅ antes 800 */
    font-size: {{ $fsActivo }}px;
    line-height: {{ $lh }};
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* ✅ Composición SIN negrita */
  .comp-cant {
    font-weight: 400;              /* ✅ antes 800 */
    font-size: {{ $fsCant }}px;
    white-space: nowrap;
    text-align: right;
    display: inline-flex;
    gap: 8px;
  }

  /* Banda vertical derecha */
  .vertical {
    font-weight: 800;
    font-size: 16px;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    white-space: nowrap;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 30px;
    margin-top:60px;
  }

  /* ✅ De abajo hacia arriba: rotamos SOLO el texto */
  .vertical .vertical-text{
    display: inline-block;
    transform: rotate(180deg);
    transform-origin: center;
  }

  /* Pie */
  .footer {
    margin-top: 14px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    align-items: start;
    font-size: 18px;
    font-weight: 200;
  }

  .so { font-size: 20px; margin-top: 6px; }

  /* Editables */
  .editable { border-bottom: 1px dashed #bbb; padding: 2px 4px; display: inline-block; }
  .editable:focus { outline: 2px solid #ddeaff; border-bottom-color: transparent; }

  @media print { .editable { border: none !important; padding: 0 !important; } }
</style>
</head>
<body>

<div class="wrap">
  <div class="label-grid">

    {{-- Reserva espacio izquierdo (precauciones del diseño físico) --}}
    <div class="left-spacer"></div>

    {{-- Centro --}}
    <div class="center">
      <div class="title editable" contenteditable="true">{{ $nombreEtiqueta }}</div>
      <div class="comp_editable" contenteditable="true"><strong>COMPOSICIÓN:</strong></div>

      <div class="comp">
        @foreach($cols as $col)
          <div class="comp-col">
            @foreach($col as $it)
              <div class="comp-row">
                <div class="comp-nombre editable" contenteditable="true">
                  {{ abreviarNombreActivoView($it->activo) }}
                </div>
                <div class="comp-cant">
                  <span class="editable js-num-dec" contenteditable="true">{{ fmtCantidadUfc($it->cantidad, $it->unidad) }}</span>
                  <span class="editable" contenteditable="true">{{ $it->unidad ?? 'mg' }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @endforeach
      </div>

      <div class="footer">
        <div>
        <span class="editable js-only-numbers" contenteditable="true">Dosis: {{ $tomas }} Cápsulas Diarias </span><br>  
        <span class="editable" contenteditable="true">Médico Prescriptor:{{ $medicoCorto ?? ($formula->medico ?? '-') }}</span><br>
          <span class="editable" contenteditable="true">Paciente: {{ $formula->paciente ?? '-' }}</span>
        </div>

        <div>
          F. Elab: <span class="editable" contenteditable="true">{{ $fechaElaboracion }}</span><br>
          Despues de abierto, consumir en un periodo máximo de 60 días
        </div>
      </div>
    </div>

    {{-- Banda vertical derecha --}}
    <div class="vertical editable" contenteditable="true">
      <span class="vertical-text">CONTENIDO: {{ $capsPerFrasco }} CAPSULAS</span>
    </div>

  </div>
</div>

<script>
  // Evitar saltos de línea
  document.querySelectorAll('.editable').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') { e.preventDefault(); el.blur(); }
    });
  });

  // Solo enteros
  document.querySelectorAll('.js-only-numbers').forEach(el => {
    el.addEventListener('input', () => {
      el.textContent = (el.textContent || '').replace(/[^\d]/g, '');
    });
    el.addEventListener('paste', (e) => {
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text');
      const clean = (text || '').replace(/[^\d]/g, '');
      document.execCommand('insertText', false, clean);
    });
  });

  // Decimal opcional
  document.querySelectorAll('.js-num-dec').forEach(el => {
    const clean = (txt) => {
      txt = (txt || '').replace(/,/g, '.');
      txt = txt.replace(/[^\d.]/g, '');
      const parts = txt.split('.');
      if (parts.length > 2) txt = parts[0] + '.' + parts.slice(1).join('');
      return txt;
    };

    el.addEventListener('input', () => { el.textContent = clean(el.textContent); });
    el.addEventListener('paste', (e) => {
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text');
      document.execCommand('insertText', false, clean(text));
    });
  });
</script>

</body>
</html>