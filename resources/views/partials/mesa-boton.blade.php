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
        data-base-class="bg-blue-950/30"
        @if($id && $disponible)
          onclick="toggleMesa(event, {{ $id }}, 'Mesa {{ $codigo }}', {{ $p }}, '{{ $zona }}')"
        @else
          disabled
        @endif
        class="mesa-btn w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-md border text-white transition-all flex flex-col items-center justify-center leading-none gap-0.5
        {{ $disponible ? 'border-blue-500/50 bg-blue-950/30 hover:border-amber-400 cursor-pointer' : 'border-zinc-900 bg-black text-zinc-600 cursor-not-allowed opacity-80' }}">
    <span class="font-bold text-[10px]">{{ $codigo }}</span>
    <span class="text-[7px] {{ $disponible ? 'text-amber-400' : 'text-zinc-600' }}">
        {{ $disponible ? '$' . number_format($p, 0) : 'OCUPADA' }}
    </span>
</button>
