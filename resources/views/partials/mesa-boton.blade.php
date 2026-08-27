@php
    $m = $mesas->firstWhere('numero', $codigo);
    $id = $m ? $m->id : '';
    $p = $m ? (float) $m->precio : 0;
    $disponible = $m ? ! in_array($m->id, $mesasReservadasIds) : false;
@endphp
<button type="button"
        data-id="{{ $id }}"
        data-disponible="{{ $disponible ? '1' : '0' }}"
        @if($id && $disponible)
          onclick="toggleMesa(event, {{ $id }}, 'Mesa {{ $codigo }}', {{ $p }}, '{{ $zona }}')"
        @else
          disabled
        @endif
        class="mesa-btn py-2 px-1.5 rounded border text-white transition-all flex items-center justify-between
        {{ $disponible ? 'border-blue-500/50 bg-blue-950/30 hover:border-amber-400 cursor-pointer' : 'border-zinc-900 bg-black text-zinc-600 cursor-not-allowed opacity-80' }}">
    <span class="font-bold text-[10px]">{{ $codigo }}</span>
    <span class="text-[8px] {{ $disponible ? 'text-amber-400' : 'text-zinc-600' }}">
        {{ $disponible ? '$' . number_format($p, 0) : 'OCUPADA' }}
    </span>
</button>
