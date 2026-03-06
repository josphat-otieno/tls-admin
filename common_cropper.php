<!-- Reusable Cropper Modal -->
<div id="cropper-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Crop Image</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="crop-image" src="" alt="Image to crop" style="max-width: 100%;">
                </div>
                <!-- Cropper Controls -->
                <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.rotate(-90)">
                        <i class="mdi mdi-rotate-left"></i> Rotate Left
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.rotate(90)">
                        <i class="mdi mdi-rotate-right"></i> Rotate Right
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.scaleX(-cropper.getData().scaleX || -1)">
                        <i class="mdi mdi-flip-horizontal"></i> Flip H
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.scaleY(-cropper.getData().scaleY || -1)">
                        <i class="mdi mdi-flip-vertical"></i> Flip V
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.zoom(0.1)">
                        <i class="mdi mdi-magnify-plus"></i> Zoom In
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.zoom(-0.1)">
                        <i class="mdi mdi-magnify-minus"></i> Zoom Out
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.reset()">
                        <i class="mdi mdi-refresh"></i> Reset
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop-save-btn">
                    <i class="mdi mdi-crop"></i> Crop & Use Image
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cropper JS and Logic -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
    let cropper = null;
    let currentCropFile = null;
    let currentInputId = null;
    let currentPreviewId = null;
    let currentRatio = NaN;

    /**
     * Initialize cropping for a file input
     * @param {File} file - The file object from input.files[0]
     * @param {string} inputId - ID of the file input element
     * @param {string} previewId - ID of the image element to show preview in
     * @param {number} aspectRatio - Optional aspect ratio (width/height)
     */
    function initCropper(file, inputId, previewId, aspectRatio = NaN) {
        if (!file) return;
        
        currentCropFile = file;
        currentInputId = inputId;
        currentPreviewId = previewId;
        currentRatio = aspectRatio;

        const objectUrl = URL.createObjectURL(file);
        const image = document.getElementById('crop-image');
        image.src = objectUrl;

        $('#cropper-modal').modal('show');

        $('#cropper-modal').one('shown.bs.modal', function () {
            if (cropper) cropper.destroy();
            cropper = new Cropper(image, {
                aspectRatio: currentRatio,
                viewMode: 0,
                autoCropArea: 1,
                responsive: true,
                background: false,
                zoomable: true,
                movable: true,
                rotatable: true,
                scalable: true,
            });
        });

        $('#cropper-modal').one('hidden.bs.modal', function() {
            URL.revokeObjectURL(objectUrl);
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });
    }

    // Set up the save button
    document.getElementById('crop-save-btn').addEventListener('click', function() {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            maxWidth: 2048,
            maxHeight: 2048,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            alert("Could not process image. It might be too large.");
            return;
        }

        canvas.toBlob(function(blob) {
            const croppedFile = new File([blob], currentCropFile.name, {
                type: currentCropFile.type,
                lastModified: Date.now()
            });

            // Update the file input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            const input = document.getElementById(currentInputId);
            if (input) {
                input.files = dataTransfer.files;
            }

            // Update the preview image
            const preview = document.getElementById(currentPreviewId);
            if (preview) {
                preview.src = canvas.toDataURL();
                // Ensure preview container and remove button are shown if the page uses that pattern
                const previewContainer = preview.parentElement;
                if (previewContainer && previewContainer.id.includes('preview')) {
                    previewContainer.style.display = 'block';
                }
                // Try to find a remove button in the same area
                const removeBtn = previewContainer?.parentElement?.querySelector('.remove-image-btn') || 
                                document.getElementById('remove-image-btn');
                if (removeBtn) removeBtn.style.display = 'inline-block';
            }

            $('#cropper-modal').modal('hide');
        }, currentCropFile.type);
    });

    // Helper function to check for special characters
    function hasSpecialChars(filename) {
        return /[^a-zA-Z0-9 ._\-()]/.test(filename);
    }

    // Helper to suggest a safe filename
    function getSafeSuggestion(filename) {
        const parts = filename.split(".");
        const ext = parts.length > 1 ? "." + parts.pop() : "";
        let base = parts.join(".");
        base = base.replace(/[’'“”"–—]/g, "").replace(/[^a-zA-Z0-9 ._\-()]+/g, "_").replace(/_+/g, "_").replace(/^_+|_+$/g, "");
        return (base || "file") + (ext ? "." + ext : "");
    }

    // Helper to show modal warning
    function showFilenameWarning(filename) {
        const currentEl = document.getElementById('warn-filename-current');
        const suggestedEl = document.getElementById('warn-filename-suggested');
        if (currentEl) currentEl.textContent = filename;
        if (suggestedEl) suggestedEl.textContent = getSafeSuggestion(filename);
        
        const modalEl = document.getElementById('filename-warning-modal');
        if (!modalEl) return;
        
        try {
            if (window.jQuery && jQuery.fn.modal) {
                $(modalEl).modal('show');
            } else if (window.bootstrap && bootstrap.Modal) {
                const myModal = new bootstrap.Modal(modalEl);
                myModal.show();
            } else {
                alert("Filename contains special characters: " + filename + "\nSuggested safe name: " + getSafeSuggestion(filename));
            }
        } catch (err) {
            alert("Filename contains special characters: " + filename + "\nSuggested safe name: " + getSafeSuggestion(filename));
        }
    }
</script>
