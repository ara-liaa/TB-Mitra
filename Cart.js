/**
 * Cart JavaScript - Toko Bangunan Mitra (TB Mitra)
 * Menangani perubahan jumlah item, penghapusan item, kalkulasi subtotal/total,
 * sinkronisasi badge keranjang di header, serta ringkasan checkout.
 */
document.addEventListener('DOMContentLoaded', function () {
    initCartPageControls();
    initCheckoutSummaryCalculator();
});

function formatRupiahCart(amount) {
    const num = Math.round(Number(amount) || 0);
    return 'Rp ' + num.toLocaleString('id-ID');
}

function updateCartBadgeCount(count) {
    const safeCount = Math.max(0, parseInt(count, 10) || 0);
    document.querySelectorAll('.cart-count-badge, #cartBadge').forEach((badge) => {
        badge.textContent = safeCount;
        badge.classList.remove('d-none');
    });
}

function showCartToast(message, type = 'primary') {
    let container = document.getElementById('tbCartToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'tbCartToastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1080';
        document.body.appendChild(container);
    }

    const bgMap = {
        success: 'text-bg-primary',
        primary: 'text-bg-primary',
        warning: 'text-bg-warning',
        danger: 'text-bg-danger',
        info: 'text-bg-secondary',
    };

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center ${bgMap[type] || 'text-bg-primary'} border-0 shadow`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body small fw-semibold"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    `;
    toastEl.querySelector('.toast-body').textContent = message;
    container.appendChild(toastEl);

    if (window.bootstrap && typeof window.bootstrap.Toast === 'function') {
        const inst = new window.bootstrap.Toast(toastEl, { delay: 3000 });
        inst.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    } else {
        setTimeout(() => toastEl.remove(), 3000);
    }
}

/**
 * Mengirim permintaan pembaruan keranjang ke api/cart-update.php
 */
async function sendCartUpdate(payload) {
    const endpoint = document.body.dataset.cartUpdateUrl || '../api/cart-update.php';
    const csrfToken = document.body.dataset.csrfToken || '';

    const formData = new FormData();
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }
    Object.entries(payload).forEach(([key, val]) => {
        formData.append(key, String(val));
    });

    const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: formData,
    });

    const data = await response.json();
    if (data.redirect) {
        window.location.href = data.redirect;
        return null;
    }

    if (!response.ok || !data.ok) {
        throw new Error(data.message || 'Gagal memperbarui keranjang.');
    }

    return data;
}

/**
 * Mengatur kontrol +, -, input jumlah, dan hapus item pada tabel keranjang
 */
function initCartPageControls() {
    const cartTable = document.querySelector('[data-cart-table]');
    if (!cartTable) return;

    const totalEl = document.getElementById('cartGrandTotal');

    const syncRowUi = (row, itemData) => {
        if (!row || !itemData) return;

        const qtyDisplay = row.querySelector('.cart-qty-display');
        const qtyInput = row.querySelector('.cart-qty-input');
        const priceEl = row.querySelector('.cart-unit-price');
        const wholesaleBadge = row.querySelector('.cart-wholesale-badge');
        const subtotalEl = row.querySelector('.cart-item-subtotal');
        const incBtn = row.querySelector('.cart-btn-inc');
        const decBtn = row.querySelector('.cart-btn-dec');

        const qty = parseInt(itemData.qty, 10) || 1;
        const maxStock = parseInt(itemData.stock, 10) || 0;

        if (qtyDisplay) qtyDisplay.textContent = qty;
        if (qtyInput) qtyInput.value = qty;
        if (priceEl) priceEl.textContent = formatRupiahCart(itemData.effective_price);
        if (subtotalEl) subtotalEl.textContent = formatRupiahCart(itemData.subtotal);

        if (wholesaleBadge) {
            wholesaleBadge.classList.toggle('d-none', !itemData.is_wholesale);
        }

        if (incBtn) {
            incBtn.disabled = qty >= maxStock;
        }
        if (decBtn) {
            decBtn.disabled = qty <= 1;
        }
    };

    const handleRowAction = async (row, action, explicitQty = null) => {
        const productId = parseInt(row.dataset.productId || '0', 10);
        if (!productId) return;

        const controls = row.querySelectorAll('button, input');
        controls.forEach((el) => {
            el.disabled = true;
        });

        try {
            const payload = { action, product_id: productId };
            if (explicitQty !== null) {
                payload.qty = explicitQty;
            }

            const res = await sendCartUpdate(payload);
            if (!res) return;

            if (typeof res.cart_count !== 'undefined') {
                updateCartBadgeCount(res.cart_count);
            }

            if (typeof res.total_amount !== 'undefined' && totalEl) {
                totalEl.textContent = formatRupiahCart(res.total_amount);
            }

            if (action === 'remove' || res.removed) {
                row.remove();
                const remainingRows = cartTable.querySelectorAll('tbody tr[data-product-id]');
                if (remainingRows.length === 0) {
                    window.location.reload();
                    return;
                }
            } else if (res.item) {
                syncRowUi(row, res.item);
            }

            if (res.message) {
                showCartToast(res.message, 'success');
            }
        } catch (err) {
            showCartToast(err.message || 'Terjadi kesalahan saat memperbarui keranjang.', 'warning');
            window.location.reload();
        } finally {
            if (document.body.contains(row)) {
                controls.forEach((el) => {
                    el.disabled = false;
                });
                const currentQty = parseInt(row.dataset.currentQty || row.querySelector('.cart-qty-display')?.textContent || '1', 10);
                const maxStock = parseInt(row.dataset.maxStock || '999999', 10);
                const incBtn = row.querySelector('.cart-btn-inc');
                if (incBtn && currentQty >= maxStock) {
                    incBtn.disabled = true;
                }
            }
        }
    };

    cartTable.querySelectorAll('tbody tr[data-product-id]').forEach((row) => {
        const incBtn = row.querySelector('.cart-btn-inc');
        const decBtn = row.querySelector('.cart-btn-dec');
        const delBtn = row.querySelector('.cart-btn-remove');
        const qtyInput = row.querySelector('.cart-qty-input');

        if (incBtn) {
            incBtn.addEventListener('click', () => {
                const cur = parseInt(row.querySelector('.cart-qty-display')?.textContent || '1', 10);
                row.dataset.currentQty = cur + 1;
                handleRowAction(row, 'increase');
            });
        }

        if (decBtn) {
            decBtn.addEventListener('click', () => {
                const cur = parseInt(row.querySelector('.cart-qty-display')?.textContent || '1', 10);
                if (cur > 1) {
                    row.dataset.currentQty = cur - 1;
                    handleRowAction(row, 'decrease');
                }
            });
        }

        if (qtyInput) {
            qtyInput.addEventListener('change', () => {
                const val = Math.max(1, parseInt(qtyInput.value || '1', 10));
                row.dataset.currentQty = val;
                handleRowAction(row, 'set', val);
            });
        }

        if (delBtn) {
            delBtn.addEventListener('click', () => {
                if (window.confirm('Hapus produk ini dari keranjang belanja?')) {
                    handleRowAction(row, 'remove');
                }
            });
        }
    });
}

/**
 * Menyorot kartu alamat pengiriman yang dipilih pada halaman checkout
 */
function initCheckoutSummaryCalculator() {
    const addressRadios = document.querySelectorAll('input[name="address_id"]');
    if (!addressRadios.length) return;

    const updateAddressCardHighlight = () => {
        addressRadios.forEach((radio) => {
            const label = radio.closest('label');
            if (!label) return;
            if (radio.checked) {
                label.classList.add('border-primary', 'bg-primary-subtle');
                label.classList.remove('bg-white');
            } else {
                label.classList.remove('border-primary', 'bg-primary-subtle');
                label.classList.add('bg-white');
            }
        });
    };

    addressRadios.forEach((radio) => radio.addEventListener('change', updateAddressCardHighlight));
    updateAddressCardHighlight();
}
