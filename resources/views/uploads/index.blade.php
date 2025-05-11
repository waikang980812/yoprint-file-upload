<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CSV Uploads</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: sans-serif;
            margin: 40px;
        }

        .upload-box {
            border: 2px dashed #ccc;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }

        .upload-box input[type="file"] {
            width: 100%;
            padding: 10px;
        }

        .upload-box button {
            position: absolute;
            right: 20px;
            top: 20px;
            padding: 8px 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        .status-pending { color: orange; }
        .status-processing { color: blue; }
        .status-completed { color: green; }
        .status-failed { color: red; }
    </style>
</head>
<body>
    <h2 style="display: inline-block; margin-right: 20px;">CSV Uploads</h2>
    <a href="{{ route('product') }}" style="padding: 8px 16px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">
        View Products
    </a>
    <div class="upload-box" id="drop-area">
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" id="fileElem" style="display:none" required>
            <p id="fileName" style="margin-top: 10px; font-style: italic; color: #555;"></p>
            <label for="fileElem" style="cursor:pointer;">Select file/Drag and drop</label>
            <button type="submit" style="margin-left:20px;">Upload File</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>File Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="uploadsList"></tbody>
    </table>

    <script>
        const uploadsList = document.getElementById('uploadsList');
        const uploadForm = document.getElementById('uploadForm');
        const fileElem = document.getElementById('fileElem');
        const dropArea = document.getElementById('drop-area');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const uploadsListUrl = "{{ route('upload.list') }}";
        const uploadsFileUrl = "{{ route('upload.store') }}";

        // Drag & drop support
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('highlight');
        });
        dropArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropArea.classList.remove('highlight');
        });
        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('highlight');
            fileElem.files = e.dataTransfer.files;
        });

        // Format time as "x minutes ago"
        function timeAgo(dateString) {
            const now = new Date();
            const then = new Date(dateString);
            const diff = Math.floor((now - then) / 1000);
            if (diff < 60) return `${diff} seconds ago`;
            if (diff < 3600) return `${Math.floor(diff/60)} minutes ago`;
            if (diff < 86400) return `${Math.floor(diff/3600)} hours ago`;
            return then.toLocaleString();
        }

        function getStatusClass(status) {
            return `status-${status.toLowerCase()}`;
        }

        async function fetchUploads() {
            try {
                const response = await fetch(uploadsListUrl);
                const { data } = await response.json();

                uploadsList.innerHTML = data.map(upload => `
                    <tr>
                        <td>
                            ${new Date(upload.uploaded_at).toLocaleString()}<br>
                            <small>(${timeAgo(upload.uploaded_at)})</small>
                        </td>
                        <td>${upload.file_name}</td>
                        <td class="${getStatusClass(upload.status)}">${upload.status}</td>
                    </tr>
                `).join('');

                const hasActiveUploads = data.some(upload =>
                    upload.status === 'pending' || upload.status === 'processing'
                );
                if (hasActiveUploads) setTimeout(fetchUploads, 2000);
            } catch (error) {
                console.error('Error fetching uploads:', error);
            }
        }

        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(uploadForm);
            try {
                await fetch(uploadsFileUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });
                fetchUploads();
                uploadForm.reset();
                document.getElementById('fileName').textContent = '';
            } catch (error) {
                console.error('Error uploading file:', error);
            }
        });
        fileElem.addEventListener('change', () => {
            const fileNameDisplay = document.getElementById('fileName');
            const file = fileElem.files[0];
            if (file) {
                fileNameDisplay.textContent = `Selected file: ${file.name}`;
            } else {
                fileNameDisplay.textContent = '';
            }
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('highlight');
            fileElem.files = e.dataTransfer.files;

            // Trigger change event to update display
            const event = new Event('change');
            fileElem.dispatchEvent(event);
        });

        fetchUploads();
    </script>
</body>
</html>
