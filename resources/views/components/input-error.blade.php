@foreach ($messages as $message)
    <p {{ $attributes->merge(['class' => 'text-sm text-red-600 dark:text-red-400 space-y-1']) }}>
        {{ $message }}
    </p>
@endforeach
