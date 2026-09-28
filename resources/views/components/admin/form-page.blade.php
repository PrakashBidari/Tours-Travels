@props(['title', 'action', 'method' => 'POST', 'back', 'submit' => 'Save', 'files' => true, 'width' => 'max-w-4xl'])

{{-- Standard admin create/edit page: header, card, 2-column grid form, cancel + save. --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $title }}</h2>
            <a href="{{ $back }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">&larr; Back</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="{{ $width }} mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ $action }}" @if ($files) enctype="multipart/form-data" @endif class="space-y-6">
                @csrf
                @if (strtoupper($method) !== 'POST') @method($method) @endif

                <x-validation-errors />

                {{ $slot }}

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ $back }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</a>
                    <x-button>{{ $submit }}</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
