@php
/**
 * Capas de animación de fondo. Cada modo (claro/oscuro) puede tener su propia
 * combinación, y varias animaciones se pueden mezclar entre sí.
 */
$layers = [
    'light' => cyber_anims('light'),
    'dark' => cyber_anims('dark'),
];
@endphp

@foreach($layers as $mode => $anims)
    @foreach($anims as $anim)
    <div class="cyber-anim cyber-anim-{{ $anim }}" data-mode="{{ $mode }}" aria-hidden="true">
        @if($anim === 'shooting')
            <span class="cyber-shooting-star" style="--sx: 4%;  --sy: 6%;  --sd: 7s;   --sdelay: 0s"></span>
            <span class="cyber-shooting-star" style="--sx: 34%; --sy: 2%;  --sd: 9.5s; --sdelay: 2.6s"></span>
            <span class="cyber-shooting-star" style="--sx: 62%; --sy: 12%; --sd: 11s;  --sdelay: 5.2s"></span>
            <span class="cyber-shooting-star" style="--sx: 12%; --sy: 34%; --sd: 13s;  --sdelay: 8s"></span>
        @elseif($anim === 'clouds')
            <span class="cyber-cloud" style="--cy: 8%;  --cw: 340px; --ch: 120px; --cd: 70s;  --cdelay: 0s"></span>
            <span class="cyber-cloud" style="--cy: 28%; --cw: 260px; --ch: 90px;  --cd: 95s;  --cdelay: -30s"></span>
            <span class="cyber-cloud" style="--cy: 52%; --cw: 420px; --ch: 140px; --cd: 120s; --cdelay: -70s"></span>
            <span class="cyber-cloud" style="--cy: 74%; --cw: 300px; --ch: 100px; --cd: 85s;  --cdelay: -15s"></span>
        @elseif($anim === 'planets')
            <span class="cyber-planet" style="--px: 78%; --py: 14%; --ps: 150px; --pd: 46s; --pdelay: 0s"></span>
            <span class="cyber-planet" style="--px: 8%;  --py: 58%; --ps: 90px;  --pd: 62s; --pdelay: -12s"></span>
            <span class="cyber-planet" style="--px: 55%; --py: 78%; --ps: 60px;  --pd: 54s; --pdelay: -28s"></span>
        @endif
    </div>
    @endforeach
@endforeach
