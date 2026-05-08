<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="p-4 mb-4 border rounded-md bg-green-50 border-green-200 text-green-800">
    {{ $message }}
</div>
