const PRODUCTO_ID = document.getElementById('producto-id')?.value;

function stars(n) {
    return '★'.repeat(n) + '☆'.repeat(5 - n);
}

function formatDate(iso) {
    const d = new Date(iso);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
}

// ── Cargar comentarios ──
async function loadComments() {
    const lista = document.getElementById('comentarios-lista');
    if (!lista || !PRODUCTO_ID) return;

    try {
        const res  = await fetch(`api/comments.php?producto_id=${encodeURIComponent(PRODUCTO_ID)}`);
        const data = await res.json();
        renderComments(data);
        updateAvgStars(data);
    } catch {
        lista.innerHTML = '<p class="comentarios-empty">Error al cargar las opiniones.</p>';
    }
}

function renderComments(list) {
    const lista = document.getElementById('comentarios-lista');
    if (!list.length) {
        lista.innerHTML = '<div class="comentarios-empty"><i class="fa-regular fa-comment"></i>Sé el primero en opinar sobre este producto.</div>';
        return;
    }

    lista.innerHTML = list.map(c => `
        <div class="comentario-card" id="c-${c.id}">
            <div class="comentario-avatar">${c.usuario[0].toUpperCase()}</div>
            <div class="comentario-body">
                <div class="comentario-header">
                    <span class="comentario-usuario">${escHtml(c.usuario)}</span>
                    <span class="comentario-stars">${stars(c.valoracion)}</span>
                    <span class="comentario-fecha">${formatDate(c.fecha)}</span>
                </div>
                <p class="comentario-texto">${escHtml(c.texto)}</p>
                <div class="comentario-actions">
                    <button class="btn-like ${c.liked ? 'liked' : ''}"
                            onclick="likeComment(${c.id}, this)"
                            aria-label="Me gusta">
                        <i class="fa-${c.liked ? 'solid' : 'regular'} fa-heart"></i>
                        <span>${c.likes}</span>
                    </button>
                    ${c.es_mio ? `<button class="btn-delete-comment" onclick="deleteComment(${c.id})" aria-label="Eliminar comentario"><i class="fa-solid fa-trash"></i></button>` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

function updateAvgStars(list) {
    const el = document.getElementById('avg-stars');
    const cnt = document.getElementById('avg-count');
    if (!el || !list.length) return;
    const avg = list.reduce((s, c) => s + c.valoracion, 0) / list.length;
    el.textContent = stars(Math.round(avg));
    if (cnt) cnt.textContent = `(${list.length} ${list.length === 1 ? 'opinión' : 'opiniones'})`;
}

// ── Enviar comentario ──
async function submitComment() {
    const texto     = document.getElementById('comentario-texto')?.value.trim();
    const valoracion = parseInt(document.querySelector('.star-selector input:checked')?.value ?? '0');
    const msgEl     = document.getElementById('form-msg');

    if (!texto || valoracion < 1) {
        showMsg(msgEl, 'Selecciona una valoración y escribe tu opinión.', 'error');
        return;
    }

    const btn = document.getElementById('btn-publicar');
    btn.disabled = true;

    try {
        const res  = await fetch('api/comments.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ action: 'add', producto_id: PRODUCTO_ID, texto, valoracion }),
        });
        const data = await res.json();

        if (!res.ok) { showMsg(msgEl, data.error || 'Error al publicar.', 'error'); return; }

        document.getElementById('comentario-texto').value = '';
        document.querySelectorAll('.star-selector input').forEach(i => i.checked = false);
        showMsg(msgEl, '¡Opinión publicada!', 'ok');
        await loadComments();
    } catch {
        showMsg(msgEl, 'Error de conexión.', 'error');
    } finally {
        btn.disabled = false;
    }
}

// ── Like ──
async function likeComment(id, btn) {
    try {
        const res  = await fetch('api/comments.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ action: 'like', id }),
        });
        if (res.status === 401) { window.location.href = 'auth/login.php'; return; }
        const data = await res.json();
        btn.classList.toggle('liked', data.liked);
        btn.querySelector('i').className = `fa-${data.liked ? 'solid' : 'regular'} fa-heart`;
        btn.querySelector('span').textContent = data.likes;
    } catch {}
}

// ── Eliminar ──
async function deleteComment(id) {
    if (!confirm('¿Eliminar este comentario?')) return;
    try {
        const res = await fetch('api/comments.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ action: 'delete', id }),
        });
        if (res.ok) {
            document.getElementById(`c-${id}`)?.remove();
            await loadComments();
        }
    } catch {}
}

// ── Helpers ──
function showMsg(el, text, type) {
    if (!el) return;
    el.textContent = text;
    el.className   = `msg-form ${type}`;
    setTimeout(() => { el.textContent = ''; }, 3000);
}

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('DOMContentLoaded', loadComments);
