import { getProducts } from './services/api.js';

const CAT_IMG = {
    'Grano':    'images/category/grano.jpg',
    'Molido':   'images/category/molido.jpg',
    'Cápsula':  'images/category/capsula.jpg',
    'Cápsulas': 'images/category/capsula.jpg',
    'Té':       'images/category/te.jpg',
};

function esc(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function stars(n = 4) {
    return '★'.repeat(n) + '☆'.repeat(5 - n);
}

function productAttr(p) {
    const nombre = p.Nombre.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    const cat    = p.Categoria.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    return `{id:'${p.ID}',nombre:'${nombre}',precio:${parseFloat(p.Precio)},categoria:'${cat}'}`;
}

function cardHtml(p) {
    const inStock = parseInt(p.Stock) > 0;
    const img     = CAT_IMG[p.Categoria] ?? 'images/category/grano.jpg';
    const precio  = parseFloat(p.Precio).toFixed(2).replace('.', ',');
    const pa      = productAttr(p);

    return `
        <article class="product-card ${inStock ? '' : 'out-of-stock'}"
                 data-cat="${esc(p.Categoria)}"
                 data-precio="${parseFloat(p.Precio)}"
                 data-stock="${parseInt(p.Stock)}">
            <div class="card-img">
                <img src="${img}" alt="${esc(p.Nombre)}" loading="lazy">
                <span class="badge-categoria">${esc(p.Categoria)}</span>
                ${!inStock ? '<span class="badge-stock-out">Sin stock</span>' : ''}
            </div>
            <div class="card-body">
                <p class="card-nombre">
                    <a class="card-link" href="producto.php?id=${esc(p.ID)}">${esc(p.Nombre)}</a>
                </p>
                <p class="card-origen"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> ${esc(p.ProcedenciaOrigen)}</p>
                <p class="card-stars" aria-label="Valoración: 4 de 5 estrellas">${stars()}</p>
                <p class="card-precio">${precio} €</p>
                <p class="card-formato">${esc(p.Formato)}</p>
            </div>
            <div class="card-actions">
                <button class="btn-cart"
                        data-id="${esc(p.ID)}"
                        ${inStock ? '' : 'disabled'}
                        onclick="addToCart(${pa})"
                        aria-label="${inStock ? `Añadir ${esc(p.Nombre)} al carrito` : `${esc(p.Nombre)} sin stock`}">
                    ${inStock
                        ? '<i class="fa-solid fa-cart-plus" aria-hidden="true"></i> Añadir al carrito'
                        : '<i class="fa-solid fa-ban" aria-hidden="true"></i> Sin stock'}
                </button>
                <button class="btn-wishlist"
                        data-id="${esc(p.ID)}"
                        onclick="toggleWishlist(${pa})"
                        aria-label="Añadir ${esc(p.Nombre)} a lista de deseos">
                    <i class="fa-regular fa-heart"></i>
                </button>
            </div>
        </article>`;
}

function renderCategories(products) {
    const container = document.getElementById('filter-categories');
    if (!container) return;
    const cats = [...new Set(products.map(p => p.Categoria))];
    container.innerHTML = cats.map(cat => `
        <label>
            <input type="checkbox" class="filter-cat" value="${esc(cat)}">
            ${esc(cat)}
        </label>`).join('');
}

function applyFilters() {
    const cats      = [...document.querySelectorAll('.filter-cat:checked')].map(c => c.value);
    const minPrice  = parseFloat(document.getElementById('price-min').value) || 0;
    const maxPrice  = parseFloat(document.getElementById('price-max').value) || Infinity;
    const onlyStock = document.getElementById('filter-stock').checked;
    const query     = document.getElementById('search-input')?.value.toLowerCase() || '';

    let visible = 0;
    document.querySelectorAll('.product-card').forEach(card => {
        const ok = (cats.length === 0 || cats.includes(card.dataset.cat))
            && parseFloat(card.dataset.precio) >= minPrice
            && parseFloat(card.dataset.precio) <= maxPrice
            && (!onlyStock || parseInt(card.dataset.stock) > 0)
            && (!query || card.querySelector('.card-nombre').textContent.toLowerCase().includes(query));

        card.style.display = ok ? '' : 'none';
        if (ok) visible++;
    });

    document.getElementById('empty-state').style.display = visible > 0 ? 'none' : '';
    document.getElementById('products-count').textContent = `${visible} productos`;
}

function initFilters() {
    document.querySelectorAll('.filter-cat, #filter-stock').forEach(el =>
        el.addEventListener('change', applyFilters)
    );
    document.getElementById('price-min').addEventListener('input', applyFilters);
    document.getElementById('price-max').addEventListener('input', applyFilters);
    document.getElementById('search-input')?.addEventListener('input', applyFilters);

    document.getElementById('btn-clear-filters').addEventListener('click', () => {
        document.querySelectorAll('.filter-cat').forEach(c => c.checked = false);
        document.getElementById('price-min').value = '';
        document.getElementById('price-max').value = '';
        document.getElementById('filter-stock').checked = false;
        const si = document.getElementById('search-input');
        if (si) si.value = '';
        applyFilters();
    });
}

function applyUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const cat    = params.get('cat');
    const q      = params.get('q');
    if (cat) {
        document.querySelectorAll('.filter-cat').forEach(cb => {
            if (cb.value === cat) cb.checked = true;
        });
    }
    if (q) {
        const si = document.getElementById('search-input');
        if (si) si.value = q;
    }
    if (cat || q) applyFilters();
}

async function init() {
    const grid    = document.getElementById('products-grid');
    const countEl = document.getElementById('products-count');

    try {
        const products = await getProducts();

        renderCategories(products);

        grid.innerHTML = products.map(cardHtml).join('')
            + `<div class="empty-state" id="empty-state" hidden>
                   <i class="fa-solid fa-magnifying-glass"></i>
                   <p>Ningún producto coincide con los filtros seleccionados.</p>
               </div>`;

        countEl.textContent = `${products.length} productos`;

        if (typeof refreshWishlistBtn === 'function') {
            products.forEach(p => refreshWishlistBtn(p.ID));
        }

        initFilters();
        applyUrlParams();
    } catch (err) {
        console.error(err);
        window.location.href = 'error.html';
    }
}

document.addEventListener('DOMContentLoaded', init);
