/**
 * Admin Module - AJAX operácie pre admin panel
 * Zmysluplné AJAX volanie č.2 - in-place editing skladu
 * 
 * Funkcie:
 * - In-place editing skladu priamo v tabuľke
 * - AJAX aktualizácia bez refresh stránky
 * - Toast notifikácie pre feedback
 */
window.AdminModule = (function () {
    'use strict';

    const API_BASE = '/api/admin';

    /**
     * Inicializácia modulu
     */
    function init() {
        bindStockEditing();
        bindDeleteConfirmations();
        console.log('AdminModule initialized');
    }

    /**
     * In-place editing skladu
     */
    function bindStockEditing() {
        // Nájdi všetky input pre sklad s triedou .stock-input-ajax
        document.querySelectorAll('.stock-input-ajax').forEach(input => {
            // Ulož pôvodnú hodnotu
            input.dataset.originalValue = input.value;

            // Debounce timer
            let debounceTimer = null;

            // Event pri zmene
            input.addEventListener('change', function () {
                const variantId = this.dataset.variantId;
                const newValue = parseInt(this.value, 10);
                const originalValue = parseInt(this.dataset.originalValue, 10);

                // Validácia na klientovi
                if (isNaN(newValue) || newValue < 0) {
                    showToast('Neplatná hodnota skladu', 'error');
                    this.value = originalValue;
                    return;
                }

                // Ak sa hodnota nezmenila, nerob nič
                if (newValue === originalValue) return;

                // AJAX update
                updateStockAjax(variantId, newValue, this);
            });

            // Live validácia pri písaní
            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const value = parseInt(this.value, 10);

                if (isNaN(value) || value < 0) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            // Enter klávesa
            input.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.blur(); // Trigger change event
                }
            });
        });
    }

    /**
     * AJAX aktualizácia skladu
     */
    async function updateStockAjax(variantId, stockQty, inputElement) {
        const originalValue = inputElement.dataset.originalValue;

        // Vizuálny feedback - loading
        inputElement.disabled = true;
        inputElement.classList.add('loading');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const response = await fetch(`${API_BASE}/variants/${variantId}/stock`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ stock_qty: stockQty })
            });

            const result = await response.json();

            if (result.ok) {
                // Úspech
                inputElement.dataset.originalValue = stockQty;
                inputElement.classList.remove('is-invalid');
                inputElement.classList.add('is-valid');

                showToast(`Sklad aktualizovaný: ${result.data.variant_info || ''} = ${stockQty} ks`, 'success');

                // Odstráň valid class po chvíli
                setTimeout(() => {
                    inputElement.classList.remove('is-valid');
                }, 2000);
            } else {
                // Chyba - vráť pôvodnú hodnotu
                inputElement.value = originalValue;
                inputElement.classList.add('is-invalid');
                showToast(result.error || 'Chyba pri aktualizácii', 'error');
            }
        } catch (error) {
            console.error('Stock update error:', error);
            inputElement.value = originalValue;
            inputElement.classList.add('is-invalid');
            showToast('Chyba siete', 'error');
        } finally {
            inputElement.disabled = false;
            inputElement.classList.remove('loading');
        }
    }

    /**
     * Potvrdenie mazania
     */
    function bindDeleteConfirmations() {
        document.querySelectorAll('[data-confirm]').forEach(element => {
            element.addEventListener('click', function (e) {
                const message = this.dataset.confirm || 'Ste si istý?';
                if (!confirm(message)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });
    }

    /**
     * Toast notifikácia
     */
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.style.backgroundColor = type === 'error' ? '#dc3545' : '#198754';
        toast.innerHTML = `<i class="bi bi-${type === 'error' ? 'x-circle' : 'check-circle'} me-2"></i>${escapeHtml(message)}`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Verejné API
    return {
        init,
        updateStockAjax,
        showToast
    };
})();

/**
 * Image Upload Module - Drag & Drop upload obrázkov
 * Zmysluplné AJAX volanie č.3 - drag & drop upload
 */
window.ImageUploadModule = (function () {
    'use strict';

    const API_BASE = '/api/admin';
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    let dropZone = null;
    let fileInput = null;
    let previewContainer = null;
    let selectedFiles = [];

    /**
     * Inicializácia modulu
     */
    function init() {
        dropZone = document.getElementById('image-drop-zone');
        fileInput = document.getElementById('images');
        previewContainer = document.getElementById('image-preview-container');

        if (!dropZone || !fileInput) return;

        bindDropZoneEvents();
        bindFileInputEvents();
        console.log('ImageUploadModule initialized');
    }

    /**
     * Naviazanie eventov na drop zónu
     */
    function bindDropZoneEvents() {
        // Prevencia defaultného správania
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        // Vizuálne zvýraznenie pri ťahaní
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        // Spracovanie drop
        dropZone.addEventListener('drop', handleDrop, false);

        // Klik na drop zónu otvorí file dialog
        dropZone.addEventListener('click', () => fileInput.click());
    }

    /**
     * Naviazanie eventov na file input
     */
    function bindFileInputEvents() {
        fileInput.addEventListener('change', function () {
            handleFiles(this.files);
        });
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        dropZone.classList.add('drag-over');
    }

    function unhighlight() {
        dropZone.classList.remove('drag-over');
    }

    /**
     * Spracovanie dropnutých súborov
     */
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    /**
     * Spracovanie vybraných súborov
     */
    function handleFiles(files) {
        const validFiles = [];
        const errors = [];

        [...files].forEach(file => {
            // Validácia typu
            if (!ALLOWED_TYPES.includes(file.type)) {
                errors.push(`${file.name}: Nepodporovaný formát (povolené: JPG, PNG, GIF, WebP)`);
                return;
            }

            // Validácia veľkosti
            if (file.size > MAX_FILE_SIZE) {
                errors.push(`${file.name}: Súbor je príliš veľký (max 5MB)`);
                return;
            }

            validFiles.push(file);
        });

        // Zobrazenie chýb
        if (errors.length > 0) {
            window.AdminModule.showToast(errors.join('\n'), 'error');
        }

        // Pridanie validných súborov
        if (validFiles.length > 0) {
            selectedFiles = [...selectedFiles, ...validFiles];
            updateFileInput();
            renderPreviews();
        }
    }

    /**
     * Aktualizácia file inputu
     */
    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    }

    /**
     * Vykreslenie náhľadov
     */
    function renderPreviews() {
        if (!previewContainer) return;

        previewContainer.innerHTML = '';

        if (selectedFiles.length === 0) {
            previewContainer.style.display = 'none';
            return;
        }

        previewContainer.style.display = 'flex';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'image-preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="${escapeHtml(file.name)}">
                    <button type="button" class="remove-btn" data-index="${index}" title="Odstrániť">
                        <i class="bi bi-x"></i>
                    </button>
                    <span class="file-name">${escapeHtml(file.name)}</span>
                `;
                previewContainer.appendChild(previewItem);

                // Event pre odstránenie
                previewItem.querySelector('.remove-btn').addEventListener('click', function () {
                    removeFile(parseInt(this.dataset.index));
                });
            };
            reader.readAsDataURL(file);
        });
    }

    /**
     * Odstránenie súboru z výberu
     */
    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updateFileInput();
        renderPreviews();
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Vyčistenie výberu
     */
    function clearSelection() {
        selectedFiles = [];
        updateFileInput();
        renderPreviews();
    }

    // Verejné API
    return {
        init,
        clearSelection
    };
})();

// Auto-inicializácia
document.addEventListener('DOMContentLoaded', function () {
    // Inicializuj len v admin sekcii
    if (document.body.classList.contains('admin-page') ||
        window.location.pathname.startsWith('/admin')) {
        window.AdminModule.init();
        window.ImageUploadModule.init();
    }
});
