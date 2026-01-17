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

// Auto-inicializácia
document.addEventListener('DOMContentLoaded', function () {
    // Inicializuj len v admin sekcii
    if (document.body.classList.contains('admin-page') ||
        window.location.pathname.startsWith('/admin')) {
        window.AdminModule.init();
    }
});
