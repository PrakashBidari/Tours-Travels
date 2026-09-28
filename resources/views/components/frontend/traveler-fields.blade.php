@props(['passport' => false, 'passportRequired' => false, 'service' => null, 'amountExpression' => null])

{{--
    Traveler detail fields shared by every booking form. When `service` and
    `amountExpression` (an Alpine expression for the current subtotal) are given, a
    coupon box is shown that previews the discount via AJAX.
--}}
@php $user = auth()->user(); @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label for="full_name" class="rt-label">{{ __('Full name (as in passport / ID)') }} <span class="text-rose-500">*</span></label>
        <input id="full_name" name="full_name" value="{{ old('full_name', $user?->name) }}" required maxlength="120" class="rt-input" autocomplete="name">
        <x-input-error for="full_name" class="mt-1" />
    </div>
    <div>
        <label for="email" class="rt-label">{{ __('Email') }} <span class="text-rose-500">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email', $user?->email) }}" required class="rt-input" autocomplete="email">
        <x-input-error for="email" class="mt-1" />
    </div>
    <div>
        <label for="phone" class="rt-label">{{ __('Phone / WhatsApp') }} <span class="text-rose-500">*</span></label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone', $user?->phone) }}" required placeholder="98XXXXXXXX" class="rt-input" autocomplete="tel">
        <x-input-error for="phone" class="mt-1" />
    </div>
    <div>
        <label for="nationality" class="rt-label">{{ __('Nationality') }}</label>
        <input id="nationality" name="nationality" value="{{ old('nationality', 'Nepali') }}" class="rt-input">
    </div>
    @if ($passport || $passportRequired)
        <div>
            <label for="passport_number" class="rt-label">{{ __('Passport number') }} @if ($passportRequired)<span class="text-rose-500">*</span>@endif</label>
            <input id="passport_number" name="passport_number" value="{{ old('passport_number') }}" @required($passportRequired) class="rt-input uppercase">
            <x-input-error for="passport_number" class="mt-1" />
        </div>
    @endif
    {{ $slot }}
    <div class="sm:col-span-2">
        <label for="special_request" class="rt-label">{{ __('Special request') }}</label>
        <textarea id="special_request" name="special_request" rows="3" placeholder="{{ __('Dietary needs, room preference, pickup details…') }}" class="rt-input">{{ old('special_request') }}</textarea>
    </div>

    @if ($service && $amountExpression)
        <div class="sm:col-span-2" x-data="{
                code: @js(old('coupon_code', '')), message: '', valid: null, checking: false,
                async check() {
                    if (! this.code) { this.valid = null; this.message = ''; discount = 0; return; }
                    this.checking = true;
                    try {
                        const { data } = await window.axios.post(@js(route('coupons.check')), { code: this.code, amount: {{ $amountExpression }}, service: @js($service) });
                        this.valid = true; this.message = data.message; discount = data.discount;
                    } catch (e) {
                        this.valid = false; this.message = e.response?.data?.message || @js(__('Invalid coupon code.')); discount = 0;
                    } finally { this.checking = false; }
                }
            }">
            <label for="coupon_code" class="rt-label">{{ __('Coupon / promo code') }}</label>
            <div class="flex gap-2">
                <input id="coupon_code" name="coupon_code" x-model="code" @input="valid = null; discount = 0" class="rt-input uppercase" placeholder="e.g. DASHAIN15">
                <button type="button" @click="check()" :disabled="checking" class="shrink-0 rounded-lg border-2 border-brand-700 px-4 text-sm font-semibold text-brand-700 hover:bg-brand-50 disabled:opacity-50">{{ __('Apply') }}</button>
            </div>
            <p x-show="message" x-cloak x-text="message" :class="valid ? 'text-emerald-600' : 'text-rose-600'" class="mt-1 text-xs font-medium"></p>
            <x-input-error for="coupon_code" class="mt-1" />
        </div>
    @endif
</div>
