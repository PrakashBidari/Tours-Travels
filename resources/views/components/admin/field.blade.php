@props([
    'name',
    'label' => null,
    'type' => 'text',        // text|email|url|number|date|datetime-local|time|textarea|richtext|lines|select|checkbox|image|images|color
    'value' => null,
    'options' => [],         // select: [value => label]
    'help' => null,
    'required' => false,
    'full' => false,         // span both columns of the form grid
    'rows' => 4,
    'placeholder' => null,
    'step' => null,
    'min' => null,
])

{{-- One labelled admin form control with old() input and validation errors wired in. --}}
@php
    $id = 'f-'.str_replace(['[', ']', '.'], '-', $name);
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $inputClass = 'block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm';

    $current = match ($type) {
        'lines' => old($name, is_array($value) ? implode("\n", $value) : $value),
        'images' => old($name.'_list', is_array($value) ? implode("\n", $value) : $value),
        'datetime-local' => old($name, $value instanceof \DateTimeInterface ? $value->format('Y-m-d\TH:i') : $value),
        'date' => old($name, $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value),
        default => old($name, $value),
    };
@endphp

<div {{ $attributes->class([$full || in_array($type, ['textarea', 'richtext', 'lines', 'images'], true) ? 'sm:col-span-2' : '']) }}>
    @if ($type === 'checkbox')
        <label for="{{ $id }}" class="flex items-center gap-2 mt-2">
            <input type="hidden" name="{{ $name }}" value="0">
            <input id="{{ $id }}" type="checkbox" name="{{ $name }}" value="1" @checked((bool) $current) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
        </label>
    @else
        @if ($label)
            <label for="{{ $id }}" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ $label }} @if ($required)<span class="text-red-500">*</span>@endif</label>
        @endif

        @switch($type)
            @case('textarea')
                <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" @required($required) placeholder="{{ $placeholder }}" class="{{ $inputClass }}">{{ $current }}</textarea>
                @break

            @case('richtext')
                <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" data-ckeditor class="{{ $inputClass }}">{{ $current }}</textarea>
                @break

            @case('lines')
                <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder ?? 'One item per line' }}" class="{{ $inputClass }} font-mono text-xs">{{ $current }}</textarea>
                @break

            @case('select')
                <select id="{{ $id }}" name="{{ $name }}" @required($required) class="{{ $inputClass }}">
                    @foreach ($options as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
                    @endforeach
                </select>
                @break

            @case('image')
                <div class="mt-1 flex items-start gap-4">
                    @if ($value)
                        <img src="{{ media_url($value) }}" alt="" class="h-20 w-28 shrink-0 rounded-lg object-cover ring-1 ring-gray-900/10">
                    @endif
                    <div class="flex-1 space-y-2">
                        <input id="{{ $id }}" type="file" name="{{ $name }}" accept="image/*" class="block w-full text-sm text-gray-600 dark:text-gray-300">
                        <input type="url" name="{{ $name }}_url" value="{{ old($name.'_url') }}" placeholder="…or paste an image URL" class="{{ $inputClass }} mt-0">
                    </div>
                </div>
                @break

            @case('images')
                <div class="mt-1 space-y-2">
                    @if ($value)
                        <div class="flex flex-wrap gap-2">
                            @foreach ((array) $value as $image)
                                <img src="{{ media_url($image) }}" alt="" class="h-16 w-24 rounded-md object-cover ring-1 ring-gray-900/10">
                            @endforeach
                        </div>
                    @endif
                    <textarea id="{{ $id }}" name="{{ $name }}_list" rows="3" placeholder="Image URLs or stored paths, one per line — delete a line to remove that image" class="{{ $inputClass }} font-mono text-xs">{{ $current }}</textarea>
                    <input type="file" name="{{ $name }}[]" multiple accept="image/*" class="block w-full text-sm text-gray-600 dark:text-gray-300">
                </div>
                @break

            @default
                <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $current }}" @required($required)
                    @if ($step) step="{{ $step }}" @endif @if ($min !== null) min="{{ $min }}" @endif placeholder="{{ $placeholder }}" class="{{ $inputClass }}">
        @endswitch
    @endif

    @if ($help)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
    <x-input-error :for="$errorKey" class="mt-1" />
</div>
