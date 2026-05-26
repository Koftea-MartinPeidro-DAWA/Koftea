// Inyectar estilos
const _style = document.createElement('style');
_style.textContent = `
    #page-overlay {
        position: fixed;
        inset: 0;
        background: #fff;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.25rem;
        font-family: Georgia, serif;
        color: #7a6555;
        font-size: 1rem;
        letter-spacing: .02em;
    }
    #page-overlay .ov-spinner {
        width: 38px;
        height: 38px;
        border: 3px solid #e8ddd0;
        border-top-color: #8b5e3c;
        border-radius: 50%;
        animation: ov-spin .8s linear infinite;
    }
    #page-overlay .ov-dot {
        display: inline-block;
        animation: ov-blink 1.4s infinite;
    }
    #page-overlay .ov-dot:nth-child(2) { animation-delay: .2s; }
    #page-overlay .ov-dot:nth-child(3) { animation-delay: .4s; }
    @keyframes ov-spin  { to { transform: rotate(360deg); } }
    @keyframes ov-blink { 0%,80%,100%{opacity:0} 40%{opacity:1} }
`;
document.head.appendChild(_style);

// Crear overlay
const _overlay = document.createElement('div');
_overlay.id = 'page-overlay';
_overlay.innerHTML = `
    <div class="ov-spinner"></div>
    <p>Cargando<span class="ov-dot">.</span><span class="ov-dot">.</span><span class="ov-dot">.</span></p>
`;
document.documentElement.appendChild(_overlay);

// Comprobar API
(async () => {
    try {
        await fetch('http://localhost:3000/productes', {
            method: 'HEAD',
            signal: AbortSignal.timeout(3000),
        });
        // API OK → quitar overlay cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => _overlay.remove());
        } else {
            _overlay.remove();
        }
    } catch {
        // API caída → mostrar mensaje de error y botón de reintento
        _overlay.querySelector('p').innerHTML =
            'El servidor se está actualizando, vuelve en unos instantes'
            + '<span class="ov-dot">.</span><span class="ov-dot">.</span><span class="ov-dot">.</span>';
        const _btn = document.createElement('button');
        _btn.textContent = 'Volver a intentar';
        _btn.style.cssText = 'margin-top:.25rem;padding:.6rem 1.4rem;background:#2e2e2c;color:#f4ecd8;border:none;border-radius:8px;font-family:Georgia,serif;font-size:.9rem;cursor:pointer;';
        _btn.onclick = () => window.location.reload();
        _overlay.appendChild(_btn);
    }
})();
