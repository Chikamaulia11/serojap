@props([
    'type' => 'error',
])

@php
    $petas = [
        'error' => [
            'ikon' => 'M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
            'kelas' => 'bg-rose-50 text-[var(--danger)] ring-rose-600/20',
            'judul' => 'Terjadi kesalahan',
            'pesan' => 'Ada gangguan saat memproses halaman ini. Coba muat ulang.',
        ],
        'success' => [
            'ikon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'kelas' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20',
            'judul' => 'Berhasil',
            'pesan' => 'Perubahan kamu sudah tersimpan.',
        ],
        'warning' => [
            'ikon' => 'M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'kelas' => 'bg-amber-50 text-amber-900 ring-amber-600/20',
            'judul' => 'Perhatian',
            'pesan' => 'Ada hal yang perlu kamu perhatikan.',
        ],
    ];

    $peta = $petas[$type];
@endphp

{{--
    Class Goes Through The Attribute Bag Only Once

    The previous version wrote `class="... {{ $peta['kelas'] }} {{
    $attributes->merge(...) }}"` -- that emits TWO `class` attributes in
    one tag. The browser keeps the first and drops the rest, so
    everything a caller passed through the bag (e.g. `class="mb-4"`)
    was quietly discarded.

    The fix is to build the entire class string inside the bag, and
    include the caller class as the merge default.
--}}
<div
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->merge([
        'class' => 'flex items-start gap-3 rounded-xl px-4 py-3 text-sm ring-1 ' . $peta['kelas'],
    ]) }}
>
    <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="{{ $peta['ikon'] }}" />
    </svg>

    <div class="min-w-0">
        <p class="font-semibold">{{ $peta['judul'] }}</p>
        <p class="mt-0.5 leading-relaxed">{{ $slot->isEmpty() ? $peta['pesan'] : $slot }}</p>
    </div>
</div>
