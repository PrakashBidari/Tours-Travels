import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

/**
 * Uploads images to our own /editor-images endpoint instead of CKFinder,
 * returning { default: url } as CKEditor's FileRepository upload contract expects.
 */
function UploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => ({
        upload() {
            return loader.file.then((file) => {
                const formData = new FormData();
                formData.append('upload', file);

                return fetch('/editor-images', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        Accept: 'application/json',
                    },
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => ({ default: data.url }));
            });
        },
        abort() {},
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('textarea[data-ckeditor]').forEach((textarea) => {
        ClassicEditor.create(textarea, {
            licenseKey: 'GPL',
            extraPlugins: [UploadAdapterPlugin],
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', 'blockQuote', '|',
                'uploadImage', 'insertTable', '|',
                'undo', 'redo',
            ],
        }).catch(console.error);
    });
});
