@props([
    'status' => null,
    'size' => 'sm',
])

@php
    $peta = \App\Support\StatusPeta::get($status);
    $ukuran = $size === 'lg' ? 'text-sm px-3 py-1.5' : 'text-xs px-2.5 py-1';
@endphp

<span
    {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-semibold rounded-full {$peta['badge']} {$ukuran}"]) }}
    role="status"
>
    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="{{ $peta['ikon'] }}" />
    </svg>
    {{ $peta['label'] }}
</span>
