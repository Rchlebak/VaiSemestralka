/**
 * App.js - Hlavný JavaScript súbor
 * Inicializácia aplikácie a pomocné funkcie
 */
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // Inicializácia košíka
    if (window.Cart) {
        window.Cart.init();
    }

    // CSRF token pre AJAX požiadavky
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    /**
     * Fetch helper s CSRF tokenom
     */
    window.apiFetch = async function(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        };

        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers,
            },
        };

        try {
            const response = await fetch(url, mergedOptions);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('API Fetch error:', error);
            return { ok: false, error: 'Network error' };
        }
    };

    /**
     * Formátovanie ceny
     */
    window.formatPrice = function(price) {
        return parseFloat(price).toFixed(2) + ' €';
    };

    /**
     * Zobrazenie toast notifikácie
     */
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.style.backgroundColor = type === 'error' ? '#dc3545' : '#198754';
        toast.innerHTML = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    };

    /**
     * Potvrdenie akcie
     */
    window.confirmAction = function(message) {
        return confirm(message);
    };

    /**
     * Automatické skrytie alertov
     */
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        }, 5000);
    });

    console.log('E-Shop Tenisiek initialized');
});

