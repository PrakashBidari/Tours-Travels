<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class SiteLayout extends Component
{
    /**
     * @param  array<int, array<string, mixed>>  $schema  Extra JSON-LD objects for this page.
     * @param  array<int, array{0: string, 1?: string|null}>  $breadcrumbs  [label, url] pairs; the last one is the current page.
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $canonical = null,
        public array $schema = [],
        public array $breadcrumbs = [],
        public bool $transparentHeader = false,
    ) {
        //
    }

    public function render(): View
    {
        return view('frontend.layouts.site');
    }
}
