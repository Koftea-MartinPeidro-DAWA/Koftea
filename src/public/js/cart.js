const CART_KEY = 'koftea_cart';

function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY) || '[]');
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateBadge();
    updateMiniCart();
}

function addToCart(product) {
    const cart = getCart();
    const idx  = cart.findIndex(i => i.id === product.id);
    if (idx >= 0) {
        cart[idx].qty += 1;
    } else {
        cart.push({ ...product, qty: 1 });
    }
    saveCart(cart);
    flashBtn(product.id);
}

function removeFromCart(id) {
    saveCart(getCart().filter(i => i.id !== id));
}

function getTotal() {
    return getCart().reduce((sum, i) => sum + i.precio * i.qty, 0);
}

function getTotalQty() {
    return getCart().reduce((sum, i) => sum + i.qty, 0);
}

function updateBadge() {
    const badge = document.getElementById('cart-badge');
    if (!badge) return;
    const qty = getTotalQty();
    badge.textContent = qty;
    badge.hidden = qty === 0;
}

function updateMiniCart() {
    const container = document.getElementById('mini-cart-items');
    const totalEl   = document.getElementById('mini-cart-total');
    if (!container) return;

    const cart = getCart();
    if (cart.length === 0) {
        container.innerHTML = '<p class="mini-cart-empty">El carret és buit</p>';
    } else {
        container.innerHTML = cart.map(i => `
            <div class="mini-cart-item">
                <span class="mci-name">${i.nombre}</span>
                <span class="mci-qty">${i.qty}×</span>
                <span class="mci-price">${(i.precio * i.qty).toFixed(2)} €</span>
                <button class="mci-remove" onclick="removeFromCart('${i.id}')" aria-label="Eliminar ${i.nombre}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `).join('');
    }
    if (totalEl) totalEl.textContent = getTotal().toFixed(2) + ' €';
}

function flashBtn(id) {
    const btn = document.querySelector(`[data-id="${id}"]`);
    if (!btn) return;
    btn.classList.add('added');
    btn.textContent = '✓ Afegit';
    setTimeout(() => {
        btn.classList.remove('added');
        btn.innerHTML = '<i class="fa-solid fa-cart-plus"></i> Afegir al carret';
    }, 1200);
}

// ── Mini-cart toggle ──
document.addEventListener('DOMContentLoaded', () => {
    updateBadge();
    updateMiniCart();

    const cartBtn  = document.querySelector('.cart-btn');
    const miniCart = document.getElementById('mini-cart');

    if (cartBtn && miniCart) {
        cartBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const open = miniCart.hidden === false;
            miniCart.hidden = open;
            cartBtn.setAttribute('aria-expanded', String(!open));
        });

        document.addEventListener('click', (e) => {
            if (!miniCart.contains(e.target) && !cartBtn.contains(e.target)) {
                miniCart.hidden = true;
                cartBtn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                miniCart.hidden = true;
                cartBtn.setAttribute('aria-expanded', 'false');
                cartBtn.focus();
            }
        });
    }

    // ── Hamburger ──
    const hamburger = document.querySelector('.hamburger');
    const mainNav   = document.getElementById('main-nav');
    if (hamburger && mainNav) {
        hamburger.addEventListener('click', () => {
            const open = mainNav.classList.toggle('open');
            hamburger.setAttribute('aria-expanded', String(open));
            hamburger.classList.toggle('active', open);
        });
    }
});
