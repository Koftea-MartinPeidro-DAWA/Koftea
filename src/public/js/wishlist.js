const WISHLIST_KEY = 'koftea_wishlist';

function getWishlist() {
    return JSON.parse(localStorage.getItem(WISHLIST_KEY) || '[]');
}

function saveWishlist(list) {
    localStorage.setItem(WISHLIST_KEY, JSON.stringify(list));
    updateWishlistBadge();
    updateMiniWishlist();
}

function toggleWishlist(product) {
    const list = getWishlist();
    const idx  = list.findIndex(i => i.id === product.id);
    if (idx >= 0) {
        list.splice(idx, 1);
    } else {
        list.push(product);
    }
    saveWishlist(list);
    refreshWishlistBtn(product.id);
}

function removeFromWishlist(id) {
    saveWishlist(getWishlist().filter(i => i.id !== id));
    refreshWishlistBtn(id);
}

function addWishlistItemToCart(id) {
    const item = getWishlist().find(i => i.id === id);
    if (item) addToCart(item);
}

function isInWishlist(id) {
    return getWishlist().some(i => i.id === id);
}

function refreshWishlistBtn(id) {
    document.querySelectorAll(`.btn-wishlist[data-id="${id}"], .btn-wishlist-detalle[data-id="${id}"]`).forEach(btn => {
        const inList = isInWishlist(id);
        btn.classList.toggle('in-wishlist', inList);
        btn.setAttribute('aria-label', inList ? 'Quitar de lista de deseos' : 'Añadir a lista de deseos');
        btn.innerHTML = inList
            ? '<i class="fa-solid fa-heart"></i>'
            : '<i class="fa-regular fa-heart"></i>';
    });
}

function updateWishlistBadge() {
    const badge = document.getElementById('wishlist-badge');
    if (!badge) return;
    const count = getWishlist().length;
    badge.textContent = count;
    badge.hidden = count === 0;
}

function updateMiniWishlist() {
    const container = document.getElementById('mini-wishlist-items');
    if (!container) return;
    const list = getWishlist();
    if (list.length === 0) {
        container.innerHTML = '<p class="mini-cart-empty">Tu lista de deseos está vacía</p>';
    } else {
        container.innerHTML = list.map(i => `
            <div class="mini-cart-item">
                <span class="mci-name">${i.nombre}</span>
                <span class="mci-price">${i.precio.toFixed(2)} €</span>
                <button class="mci-add-cart" onclick="addWishlistItemToCart('${i.id}')" aria-label="Añadir ${i.nombre} al carrito">
                    <i class="fa-solid fa-cart-plus"></i>
                </button>
                <button class="mci-remove" onclick="removeFromWishlist('${i.id}')" aria-label="Eliminar ${i.nombre} de la lista de deseos">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `).join('');
    }
    const countEl = document.getElementById('mini-wishlist-count');
    if (countEl) countEl.textContent = `${list.length} producto${list.length !== 1 ? 's' : ''}`;
}

document.addEventListener('DOMContentLoaded', () => {
    updateWishlistBadge();
    updateMiniWishlist();

    document.querySelectorAll('.btn-wishlist, .btn-wishlist-detalle').forEach(btn => {
        refreshWishlistBtn(btn.dataset.id);
    });

    const wishlistBtn  = document.querySelector('.wishlist-btn');
    const miniWishlist = document.getElementById('mini-wishlist');

    if (wishlistBtn && miniWishlist) {
        wishlistBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const open = !miniWishlist.hidden;
            miniWishlist.hidden = open;
            wishlistBtn.setAttribute('aria-expanded', String(!open));
            // Cierra el carrito si está abierto
            if (!open) {
                const miniCart = document.getElementById('mini-cart');
                const cartBtn  = document.querySelector('.cart-btn');
                if (miniCart) { miniCart.hidden = true; }
                if (cartBtn)  { cartBtn.setAttribute('aria-expanded', 'false'); }
            }
        });

        document.addEventListener('click', (e) => {
            if (!miniWishlist.contains(e.target) && !wishlistBtn.contains(e.target)) {
                miniWishlist.hidden = true;
                wishlistBtn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                miniWishlist.hidden = true;
                wishlistBtn.setAttribute('aria-expanded', 'false');
                wishlistBtn.focus();
            }
        });
    }
});
