<?php

namespace App\Http\Controllers\Backend\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Helpers for the Ram Tours admin CRUD forms. */
trait HandlesAdminInput
{
    /** "One item per line" textarea → array of non-empty trimmed lines. */
    protected function lines(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /** Itinerary textarea: one day per line as "Title | Description". */
    protected function itinerary(?string $text): array
    {
        return collect($this->lines($text))->map(function ($line) {
            [$title, $description] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');

            return ['title' => $title, 'description' => $description];
        })->all();
    }

    /**
     * Image gallery field: keeps the URLs/paths listed in `{$field}_list` (one per line, in
     * order — admins remove an image by deleting its line) and appends new uploads.
     */
    protected function gallery(Request $request, string $field, string $folder): array
    {
        $images = $this->lines($request->input($field.'_list'));

        foreach ($request->file($field, []) as $file) {
            $images[] = $file->store($folder, 'public');
        }

        return array_values(array_unique($images));
    }

    /** Single image field: a new upload wins, otherwise a pasted URL, otherwise keep current. */
    protected function singleImage(Request $request, string $field, string $folder, ?string $current = null): ?string
    {
        if ($request->hasFile($field)) {
            return $request->file($field)->store($folder, 'public');
        }

        return $request->filled($field.'_url') ? $request->input($field.'_url') : $current;
    }

    /** @param class-string<Model> $model */
    protected function uniqueSlug(string $model, string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: Str::random(8);
        $slug = $base;
        $i = 2;

        while ($model::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    protected static function imageRules(string $field, bool $multiple = false): array
    {
        return $multiple
            ? [$field => ['nullable', 'array', 'max:12'], $field.'.*' => ['image', 'max:5120'], $field.'_list' => ['nullable', 'string']]
            : [$field => ['nullable', 'image', 'max:5120'], $field.'_url' => ['nullable', 'url', 'max:1000']];
    }
}
