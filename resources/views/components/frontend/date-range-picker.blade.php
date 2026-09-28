@props(['checkin' => null, 'checkout' => null])

@php
    $dateJs = fn (?string $d) => $d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? "new Date(".json_encode($d).")" : 'null';
@endphp

<div
    x-data="{
        open: false,
        month: new Date(),
        start: {{ $dateJs($checkin) }},
        end: {{ $dateJs($checkout) }},
        today: new Date(new Date().setHours(0, 0, 0, 0)),
        isPast(d) { return d < this.today; },
        fmt(d) { return d ? d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }) : ''; },
        iso(d) { return d ? d.toLocaleDateString('en-CA') : ''; },
        label() { return this.start ? this.fmt(this.start) + (this.end ? ' - ' + this.fmt(this.end) : '') : 'Check-in - Check-out'; },
        pick(d) {
            if (this.isPast(d)) return;
            if (!this.start || this.end) { this.start = d; this.end = null; }
            else if (d < this.start) { this.start = d; }
            else { this.end = d; }
        },
        same(a, b) { return a && b && a.getTime() === b.getTime(); },
        inRange(d) { return this.start && this.end && d > this.start && d < this.end; },
        days() {
            const y = this.month.getFullYear(), m = this.month.getMonth();
            const pad = new Date(y, m, 1).getDay(), total = new Date(y, m + 1, 0).getDate();
            return Array(pad).fill(null).concat([...Array(total)].map((_, i) => new Date(y, m, i + 1)));
        },
        shift(n) { this.month = new Date(this.month.getFullYear(), this.month.getMonth() + n, 1); },
    }"
    @click.away="open = false"
    class="relative text-left"
>
    <button type="button" @click="open = !open" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-left hover:border-brand-400 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 truncate">
        <span x-text="label()" :class="start ? 'text-gray-700' : 'text-gray-400'"></span>
    </button>
    <input type="hidden" name="checkin" :value="iso(start)">
    <input type="hidden" name="checkout" :value="iso(end)">

    <div x-show="open" x-cloak x-transition @click.stop class="absolute z-20 mt-2 w-72 rounded-xl bg-white p-4 shadow-lg ring-1 ring-gray-900/5">
        <div class="mb-3 flex items-center justify-between">
            <button type="button" @click="shift(-1)" class="rounded p-1 text-gray-500 hover:bg-gray-100">&larr;</button>
            <span class="text-sm font-semibold text-gray-800" x-text="month.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })"></span>
            <button type="button" @click="shift(1)" class="rounded p-1 text-gray-500 hover:bg-gray-100">&rarr;</button>
        </div>
        <div class="mb-1 grid grid-cols-7 text-center text-xs text-gray-400">
            <template x-for="d in ['S','M','T','W','T','F','S']"><span x-text="d"></span></template>
        </div>
        <div class="grid grid-cols-7 gap-1">
            <template x-for="(d, i) in days()" :key="i">
                <button
                    type="button" :disabled="!d || isPast(d)" @click="d && pick(d)"
                    class="h-8 w-8 rounded-full text-sm"
                    :class="!d ? 'invisible' : isPast(d) ? 'text-amber-700/50 cursor-not-allowed' : same(d, start) || same(d, end) ? 'bg-brand-600 text-white font-semibold' : inRange(d) ? 'bg-gray-200 text-gray-700' : 'text-gray-700 hover:bg-gray-100'"
                    x-text="d ? d.getDate() : ''"
                ></button>
            </template>
        </div>
    </div>
</div>
