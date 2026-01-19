document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.main-header');
    if (!header) return;

    // Determine depth to set base path
    const path = window.location.pathname;
    const isRoot = path.endsWith('index.html') || path.endsWith('/') || !path.includes('/src/pages/');
    const basePath = isRoot ? '' : '../../';
    const pagePath = isRoot ? 'src/pages/' : '';

    // --- 1. SETUP MENU MOBILE ---
    const hamburgerBtn = document.createElement('button');
    hamburgerBtn.className = 'hamburger-btn';
    hamburgerBtn.setAttribute('aria-label', 'Apri Menu');
    hamburgerBtn.setAttribute('aria-expanded', 'false');
    hamburgerBtn.setAttribute('aria-controls', 'mobile-menu-overlay');
    hamburgerBtn.innerHTML = '<span class="bar"></span><span class="bar"></span><span class="bar"></span>';
    header.appendChild(hamburgerBtn);

    const overlay = document.createElement('div');
    overlay.id = 'mobile-menu-overlay';
    overlay.className = 'mobile-menu-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', 'Menu mobile');
    overlay.innerHTML = `
        <button class="close-menu-btn" aria-label="Chiudi Menu">&times;</button>
        <nav class="mobile-nav-content" aria-label="Navigazione mobile">
            <ul>
                <li><a href="${basePath}index.html" class="mobile-link">Home</a></li>
                <li><a href="${basePath}${pagePath}shop.php" class="mobile-link">Shop</a></li>
                <li><a href="${basePath}${pagePath}tea-info.html" class="mobile-link">Il Nostro Tè</a></li>
                <li><a href="${basePath}${pagePath}about.html" class="mobile-link">About</a></li>
                <li style="height: 20px;" aria-hidden="true"></li>
                <li><a href="${basePath}${pagePath}register.php" class="mobile-link" style="color:var(--universitea-green)">Join Now</a></li>
            </ul>
        </nav>
    `;
    document.body.appendChild(overlay);

    // --- 2. LOGICA APERTURA/CHIUSURA ---
    const closeBtn = overlay.querySelector('.close-menu-btn');

    function toggleMenu(open) {
        document.body.classList.toggle('menu-open', open);
        hamburgerBtn.setAttribute('aria-expanded', open);
        if (open) {
            closeBtn.focus();
            document.addEventListener('keydown', handleTabKey);
        } else {
            hamburgerBtn.focus();
            document.removeEventListener('keydown', handleTabKey);
        }
    }

    function handleTabKey(e) {
        const focusableElements = overlay.querySelectorAll('a, button');
        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        if (e.key === 'Tab') {
            if (e.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    lastFocusable.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    firstFocusable.focus();
                    e.preventDefault();
                }
            }
        }
        if (e.key === 'Escape') {
            toggleMenu(false);
        }
    }

    hamburgerBtn.addEventListener('click', () => toggleMenu(true));
    closeBtn.addEventListener('click', () => toggleMenu(false));

    overlay.querySelectorAll('.mobile-link').forEach(link => {
        link.addEventListener('click', () => toggleMenu(false));
    });

    // --- 3. HEADER COLOR CHANGE (DINAMICO) ---
    function updateHeaderColor() {
        const triggerPoint = window.innerHeight * 0.85;
        if (window.scrollY > triggerPoint) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
    window.addEventListener('scroll', updateHeaderColor);
});