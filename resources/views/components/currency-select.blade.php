@props(['name' => 'currency', 'id' => null, 'selected' => null])

<select
    id="{{ $id ?? $name }}"
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full']) }}
>
    @foreach (config('currencies') as $code => $currency)
        <option value="{{ $code }}" @selected($selected === $code)>{{ $currency['flag'] }} {{ $code }} — {{ $currency['name'] }}</option>
    @endforeach
</select>
