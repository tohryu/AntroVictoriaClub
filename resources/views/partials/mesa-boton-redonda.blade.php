@php
    $m = $mesas->firstWhere('numero', $codigo);
    $id = $m ? $m->id : '';
    $mapaPrecios = $mapaPrecios ?? [];
    $p = $m ? (array_key_exists($m->id, $mapaPrecios) ? (float) $mapaPrecios[$m->id] : (float) $m->precio) : 0;
    $disponible = $m ? ! in_array($m->id, $mesasReservadasIds) : false;
@endphp
<button type="button"
        data-id="{{ $id }}"
        data-disponible="{{ $disponible ? '1' : '0' }}"
        data-base-class="bg-amber-950/40"
        @if($id && $disponible)
          onclick="toggleMesa(event, {{ $id }}, 'Mesa {{ $codigo }}', {{ $p }}, '{{ $zona }}')"
        @else
          disabled
        @endif
        class="mesa-btn w-14 h-14 sm:w-16 sm:h-16 rounded-full border-2 text-white transition-all flex flex-col items-center justify-center leading-none
        {{ $disponible ? 'border-amber-500 bg-amber-950/40 hover:border-amber-300 hover:scale-105 cursor-pointer' : 'border-zinc-800 bg-black text-zinc-600 cursor-not-allowed opacity-80' }}">
    <span class="font-bold text-[10px]">{{ $codigo }}</span>
    <span class="text-[8px] {{ $disponible ? 'text-amber-300' : 'text-zinc-600' }}">
        {{ $disponible ? '$' . number_format($p, 0) : 'OCUPADA' }}
    </span>
</button>
