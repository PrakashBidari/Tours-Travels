@php
    $statusMessages = [
        'verification-link-sent' => 'A new verification link has been sent to your email address.',
    ];

    $status = session('status');
    $status = $statusMessages[$status] ?? $status;
@endphp
<script>
    window.__flash = {
        success: @json($status),
        error: @json(session('error')),
        firstError: @json($errors->any() ? $errors->first() : null),
    };
</script>
