// Font Awesome Free is bundled by Vite (works offline; no CDN needed).
import '@fortawesome/fontawesome-free/css/all.min.css';

// Vanilla JS for the public site and the admin dashboard.
const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));

/* ---- Font Awesome helpers ---- */
// Same rule as the server (App\Models\Skill::ICON_PATTERN).
const ICON_PATTERN = /^(?:fa-(?:solid|regular|brands)|fa[srb]) fa-[a-z0-9]+(?:-[a-z0-9]+)*$/;
const FALLBACK_ICON = 'fa-solid fa-code';

// An icon that exists in the installed Font Awesome Free build has a ::before glyph.
const glyphExists = (el) => {
    const content = window.getComputedStyle(el, '::before').content;
    return Boolean(content) && !['none', 'normal', '""', "''"].includes(content);
};

const modules = {
    /* ---- Public: mobile navigation ---- */
    mobileNav() {
        const toggle = $('[data-nav-toggle]');
        const nav = $('#site-nav');
        if (!toggle || !nav) return;

        const setOpen = (open) => {
            nav.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', String(open));
        };
        toggle.addEventListener('click', () => setOpen(!nav.classList.contains('is-open')));
        $$('a', nav).forEach((link) => link.addEventListener('click', () => setOpen(false)));
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !toggle.contains(e.target)) setOpen(false);
        });
        window.matchMedia('(min-width: 901px)').addEventListener('change', (e) => { if (e.matches) setOpen(false); });
    },

    headerBorder() {
        const header = $('.header');
        if (!header) return;
        const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    },

    scrollSpy() {
        const links = $$('[data-spy]');
        const sections = links.map((l) => document.getElementById(l.dataset.spy)).filter(Boolean);
        if (!sections.length || !('IntersectionObserver' in window)) return;
        const spy = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                links.forEach((l) => l.classList.toggle('is-active', l.dataset.spy === entry.target.id));
            });
        }, { rootMargin: '-45% 0px -50% 0px' });
        sections.forEach((s) => spy.observe(s));
    },

    /* ---- Public: scroll reveal (content is never left invisible) ---- */
    reveal() {
        const items = $$('.reveal');
        const showAll = () => items.forEach((el) => el.classList.add('is-visible'));
        if (!('IntersectionObserver' in window)) return showAll();

        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -5% 0px' });
        items.forEach((el) => io.observe(el));
        window.setTimeout(showAll, 3000); // safety net
    },

    /* ---- Public: project category filter ---- */
    projectFilter() {
        const filters = $$('[data-filter]');
        const cards = $$('.project-card');
        const emptyNote = $('#filter-empty');
        filters.forEach((button) => {
            button.addEventListener('click', () => {
                const value = button.dataset.filter;
                filters.forEach((b) => b.classList.toggle('is-active', b === button));
                let visible = 0;
                cards.forEach((card) => {
                    const show = value === 'all' || card.dataset.category === value;
                    card.classList.toggle('is-hidden', !show);
                    if (show) visible++;
                });
                if (emptyNote) emptyNote.hidden = visible !== 0;
            });
        });
    },

    lightbox() {
        const lightbox = $('#lightbox');
        if (!lightbox || typeof lightbox.showModal !== 'function') return;
        const img = $('img', lightbox);
        $$('[data-lightbox]').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                const source = $('img', trigger);
                img.src = source.currentSrc || source.src;
                img.alt = source.alt;
                lightbox.showModal();
            });
        });
        lightbox.addEventListener('click', () => lightbox.close());
    },

    /* ---- Admin: sidebar drawer (mobile) ---- */
    adminDrawer() {
        const sidebar = $('#admin-sidebar');
        const toggle = $('[data-sidebar-toggle]');
        if (!sidebar || !toggle) return;

        const setOpen = (open) => {
            sidebar.classList.toggle('is-open', open);
            document.body.classList.toggle('menu-open', open);
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        };
        toggle.addEventListener('click', () => setOpen(!sidebar.classList.contains('is-open')));
        $$('[data-sidebar-close], [data-sidebar-backdrop]').forEach((el) => el.addEventListener('click', () => setOpen(false)));
        $$('a', sidebar).forEach((a) => a.addEventListener('click', () => setOpen(false)));
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
        window.matchMedia('(min-width: 901px)').addEventListener('change', (e) => { if (e.matches) setOpen(false); });
    },

    /* ---- Admin: confirmation dialog for destructive forms ---- */
    confirmDialog() {
        const dialog = $('#confirm-dialog');
        const forms = $$('form[data-confirm]');
        if (!forms.length) return;

        if (!dialog || typeof dialog.showModal !== 'function') {
            forms.forEach((form) => form.addEventListener('submit', (e) => {
                if (!window.confirm(form.dataset.confirm)) e.preventDefault();
            }));
            return;
        }

        const message = $('#confirm-message');
        let pending = null;
        forms.forEach((form) => form.addEventListener('submit', (e) => {
            if (form.dataset.confirmed === '1') return;
            e.preventDefault();
            pending = form;
            message.textContent = form.dataset.confirm;
            dialog.showModal();
        }));
        dialog.addEventListener('close', () => {
            if (dialog.returnValue === 'confirm' && pending) {
                pending.dataset.confirmed = '1';
                pending.submit();
            }
            pending = null;
            dialog.returnValue = '';
        });
    },

    /* ---- Admin: image preview + client-side size/type check ---- */
    imagePreview() {
        $$('input[type="file"][data-preview]').forEach((input) => {
            const preview = $(input.dataset.preview);
            const errorBox = input.closest('.upload')?.querySelector('[data-upload-error]');
            const maxBytes = (parseFloat(input.dataset.maxMb) || 2) * 1024 * 1024;
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];
            const original = preview ? preview.src : null;

            input.addEventListener('change', () => {
                const file = input.files[0];
                if (errorBox) { errorBox.hidden = true; errorBox.textContent = ''; }
                if (!file) { if (preview && original) preview.src = original; return; }

                let problem = null;
                if (!allowed.includes(file.type)) problem = 'Please choose a JPG, PNG or WEBP image.';
                else if (file.size > maxBytes) problem = 'That file is larger than ' + (maxBytes / 1048576) + ' MB.';

                if (problem) {
                    input.value = '';
                    if (preview && original) preview.src = original;
                    if (errorBox) { errorBox.textContent = problem; errorBox.hidden = false; }
                    return;
                }
                if (preview) preview.src = URL.createObjectURL(file);
            });
        });
    },

    /* ---- Admin: live Font Awesome icon preview ---- */
    iconPreview() {
        const input = $('[data-icon-input]');
        if (!input) return;

        const preview = $(input.dataset.iconPreview);
        const message = $(input.dataset.iconMessage);
        const fallback = input.dataset.fallback || FALLBACK_ICON;
        const box = preview?.parentElement;
        const defaultMessage = message ? message.textContent : '';
        let timer = null;

        const show = (cls, warning) => {
            preview.className = cls;
            box?.classList.toggle('is-warning', Boolean(warning));
            if (message) {
                message.textContent = warning || defaultMessage;
                message.classList.toggle('is-warning', Boolean(warning));
            }
        };

        const update = () => {
            const value = input.value.trim().replace(/\s+/g, ' ').toLowerCase();
            if (value === '') return show(fallback, null);
            if (!ICON_PATTERN.test(value)) {
                return show(fallback, 'Invalid format. Use a style prefix and one icon name, for example "fa-brands fa-php".');
            }
            preview.className = value;
            // Wait a frame so the stylesheet can apply before checking for the glyph.
            window.requestAnimationFrame(() => {
                if (glyphExists(preview)) show(value, null);
                else show(fallback, 'This icon is not available in Font Awesome Free. The fallback icon is shown instead.');
            });
        };

        input.addEventListener('input', () => { window.clearTimeout(timer); timer = window.setTimeout(update, 150); });
        update();
    },

    /* ---- Public + admin: unsupported saved icons fall back gracefully ---- */
    skillIconFallback() {
        const run = () => $$('[data-skill-icon]').forEach((el) => {
            if (!glyphExists(el)) el.className = FALLBACK_ICON;
        });
        if (document.readyState === 'complete') run();
        else window.addEventListener('load', run, { once: true });
    },
};

document.addEventListener('DOMContentLoaded', () => {
    // One failing module must never break the others.
    Object.entries(modules).forEach(([name, init]) => {
        try { init(); } catch (error) { console.error(`[portfolio] ${name} failed`, error); }
    });
});
