import { getProduct } from './services/api.js';

const CAT_IMG = {
    'Grano':    'images/category/grano.jpg',
    'Molido':   'images/category/molido.jpg',
    'Cápsula':  'images/category/capsula.jpg',
    'Cápsulas': 'images/category/capsula.jpg',
    'Té':       'images/category/te.jpg',
};

function dots(n) {
    const v = Math.max(0, Math.min(5, parseInt(n) || 0));
    return '●'.repeat(v) + '○'.repeat(5 - v);
}

function productAttr(p) {
    const nombre = p.Nombre.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    const cat    = p.Categoria.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    return `{id:'${p.ID}',nombre:'${nombre}',precio:${parseFloat(p.Precio)},categoria:'${cat}'}`;
}

async function init() {
    const id = new URLSearchParams(window.location.search).get('id');
    if (!id) { window.location.href = 'productes.php'; return; }

    try {
        const p = await getProduct(id);
        if (!p) { window.location.href = 'productes.php'; return; }

        const inStock = parseInt(p.Stock) > 0;
        const pa      = productAttr(p);

        document.title = `${p.Nombre} · KoffTea`;

        document.getElementById('breadcrumb-nombre').textContent = p.Nombre;

        const imgEl = document.getElementById('producto-img');
        imgEl.src = CAT_IMG[p.Categoria] ?? 'images/category/grano.jpg';
        imgEl.alt = p.Nombre;

        document.getElementById('producto-badge').textContent   = p.Categoria;
        document.getElementById('producto-nombre').textContent  = p.Nombre;
        document.getElementById('producto-precio').textContent  =
            `${parseFloat(p.Precio).toFixed(2).replace('.', ',')} €`;

        document.getElementById('producto-origen').textContent     = p.ProcedenciaOrigen;
        document.getElementById('producto-formato').textContent    = p.Formato;
        document.getElementById('producto-intensidad').textContent = dots(p.Intensidad);

        const stockEl = document.getElementById('producto-stock');
        stockEl.innerHTML = inStock
            ? `<i class="fa-solid fa-circle-check" style="color:#4a7c4a"></i>
               <span class="producto-stock-ok">En stock (${parseInt(p.Stock)} uds.)</span>`
            : `<i class="fa-solid fa-circle-xmark" style="color:#c62828"></i>
               <span class="producto-stock-out">Sin stock</span>`;

        const descEl = document.getElementById('producto-desc');
        if (p.Descripcion) {
            descEl.textContent = p.Descripcion;
            descEl.hidden = false;
        }

        const btnCart = document.getElementById('btn-cart-detalle');
        btnCart.disabled = !inStock;
        btnCart.setAttribute('onclick', `addToCart(${pa})`);
        btnCart.setAttribute('aria-label', inStock ? `Añadir ${p.Nombre} al carrito` : 'Sin stock');
        btnCart.innerHTML = inStock
            ? '<i class="fa-solid fa-cart-plus"></i> Añadir al carrito'
            : '<i class="fa-solid fa-ban"></i> Sin stock';

        const btnWish = document.getElementById('btn-wishlist-detalle');
        btnWish.dataset.id = p.ID;
        btnWish.setAttribute('onclick', `toggleWishlist(${pa})`);
        btnWish.setAttribute('aria-label', `Añadir ${p.Nombre} a lista de deseos`);
        if (typeof refreshWishlistBtn === 'function') refreshWishlistBtn(p.ID);

        document.getElementById('producto-id').value = p.ID;

    } catch (err) {
        console.error(err);
        window.location.href = 'error.html';
    }
}

document.addEventListener('DOMContentLoaded', init);
