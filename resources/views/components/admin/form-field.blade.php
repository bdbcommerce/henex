<div class="mb-6">
    <label class="block text-sm font-medium text-neutral-950 mb-2">
        {{ $label }}
        @if($required ?? false)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @if($type === 'textarea')
        <textarea
            {{ $attributes->class([
                'w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand resize-vertical',
                'border-red-500' => ($error ?? false)
            ]) }}
            rows="{{ $rows ?? 4 }}">{{ $slot }}</textarea>
    @elseif($type === 'select')
        <select {{ $attributes->class([
            'w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand',
            'border-red-500' => ($error ?? false)
        ]) }}>
            {{ $slot }}
        </select>
    @elseif($type === 'checkbox')
        <label class="flex items-center">
            <input type="checkbox" {{ $attributes->class([
                'w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand',
                'border-red-500' => ($error ?? false)
            ]) }}>
            <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
        </label>
    @else
        <input
            type="{{ $type ?? 'text' }}"
            {{ $attributes->class([
                'w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand',
                'border-red-500' => ($error ?? false)
            ]) }}
            value="{{ $slot }}">
    @endif
    @if($error ?? false)
        <p class="text-red-500 text-xs mt-1">{{ $error }}</p>
    @endif
    @if($help ?? false)
        <p class="text-gray-600 text-xs mt-1">{{ $help }}</p>
    @endif
</div>
