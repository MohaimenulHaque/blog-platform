@php
    $editorId = $editorId ?? 'content-editor';
    $value = $value ?? null;
@endphp

<textarea
    id="{{ $editorId }}"
    name="content"
    rows="18"
    class="input-field resize-y font-mono"
>{{ $value }}</textarea>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof tinymce === 'undefined') return;

        tinymce.init({
            selector: '#{{ $editorId }}',
            height: 560,
            menubar: false,
            branding: false,
            promotion: false,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            plugins: 'advlist autolink lists link image media table codesample code wordcount visualblocks help',
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link image media table codesample blockquote hr | removeformat code',
            block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Blockquote=blockquote; Code Block=pre',
            codesample_languages: [
                { text: 'HTML', value: 'markup' },
                { text: 'CSS', value: 'css' },
                { text: 'JavaScript', value: 'javascript' },
                { text: 'PHP', value: 'php' },
                { text: 'Python', value: 'python' },
                { text: 'SQL', value: 'sql' },
                { text: 'Shell', value: 'bash' },
                { text: 'JSON', value: 'json' },
            ],
            automatic_uploads: true,
            images_upload_url: '{{ route('admin.uploads.store') }}',
            images_upload_handler: function (blobInfo, success, failure) {
                var formData = new FormData();
                formData.append('image', blobInfo.blob(), blobInfo.filename());
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('admin.uploads.store') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData,
                })
                .then(function (response) {
                    if (!response.ok) return Promise.reject(new Error('Upload failed'));
                    return response.json();
                })
                .then(function (data) { success(data.location); })
                .catch(function (error) { failure(error.message || 'Upload failed'); });
            },
            file_picker_types: 'image',
            content_style: [
                'body { font-family: Inter, ui-sans-serif, system-ui, sans-serif; font-size: 15px; line-height: 1.7; color: #1c1917; }',
                'h1,h2,h3,h4 { font-family: Fraunces, ui-serif, Georgia, serif; letter-spacing: -0.02em; }',
                'table { border-collapse: collapse; width: 100%; }',
                'td, th { border: 1px solid #d6d3d1; padding: 6px 10px; }',
            ].join(' '),
        });
    });
</script>
