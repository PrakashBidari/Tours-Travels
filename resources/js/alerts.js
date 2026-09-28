import Swal from 'sweetalert2';

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (el) => {
        el.onmouseenter = Swal.stopTimer;
        el.onmouseleave = Swal.resumeTimer;
    },
});

const AppAlert = {
    success(message, title = 'Success') {
        return toast.fire({ icon: 'success', title, text: message });
    },
    error(message, title = 'Something went wrong') {
        return toast.fire({ icon: 'error', title, text: message });
    },
    info(message, title = 'Heads up') {
        return toast.fire({ icon: 'info', title, text: message });
    },
    /**
     * Promise<SweetAlertResult> — caller checks `.isConfirmed`.
     */
    confirm({ title = 'Are you sure?', text = '', confirmButtonText = 'Yes, continue', icon = 'warning' } = {}) {
        return Swal.fire({
            title,
            text,
            icon,
            showCancelButton: true,
            confirmButtonText,
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
        });
    },
};

window.Swal = Swal;
window.AppAlert = AppAlert;

/**
 * Any <form data-confirm="Message"> is intercepted and only submitted after
 * the user accepts a SweetAlert confirmation, replacing native confirm().
 */
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) {
        return;
    }

    event.preventDefault();

    AppAlert.confirm({
        title: form.dataset.confirmTitle || 'Are you sure?',
        text: form.dataset.confirm,
        confirmButtonText: form.dataset.confirmButton || 'Yes, continue',
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}, true);

/**
 * Any button/link with data-confirm-url + data-confirm fires a POST (with an
 * optional data-confirm-method, default DELETE) to that URL after confirmation
 * — for one-off destructive actions that aren't already a <form>.
 */
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-confirm-url]');
    if (!trigger) {
        return;
    }

    event.preventDefault();

    AppAlert.confirm({
        title: trigger.dataset.confirmTitle || 'Are you sure?',
        text: trigger.dataset.confirm || '',
        confirmButtonText: trigger.dataset.confirmButton || 'Yes, continue',
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = trigger.dataset.confirmUrl;
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name=csrf-token]').content;
        form.appendChild(csrf);

        const method = trigger.dataset.confirmMethod || 'DELETE';
        if (method !== 'POST') {
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = method;
            form.appendChild(methodField);
        }

        document.body.appendChild(form);
        form.submit();
    });
}, true);

document.addEventListener('DOMContentLoaded', () => {
    const flash = window.__flash || {};

    if (flash.success) {
        AppAlert.success(flash.success);
    }

    if (flash.error) {
        AppAlert.error(flash.error);
    } else if (flash.firstError) {
        AppAlert.error(flash.firstError);
    }
});
