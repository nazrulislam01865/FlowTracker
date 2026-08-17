(() => {
    if (window.__flowtrackClientValidationFocusLoaded) return;
    window.__flowtrackClientValidationFocusLoaded = true;

    let lastSignature = '';
    let scheduled = false;

    const isVisible = (element) => {
        if (!(element instanceof HTMLElement)) return false;
        const style = window.getComputedStyle(element);
        return style.display !== 'none' && style.visibility !== 'hidden' && element.getClientRects().length > 0;
    };

    const schedule = () => {
        if (scheduled) return;
        scheduled = true;
        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
                scheduled = false;
                focusFirstClientValidationError();
            });
        });
    };

    const focusFirstClientValidationError = () => {
        const root = document.querySelector('.ft-client-create-prototype[data-client-validation-signature]');
        if (!root) {
            lastSignature = '';
            return;
        }

        const signature = root.getAttribute('data-client-validation-signature') || '';
        if (!signature) {
            lastSignature = '';
            root.querySelectorAll('.ft-client-validation-focus-target').forEach((element) => {
                element.classList.remove('ft-client-validation-focus-target');
            });
            return;
        }

        if (signature === lastSignature) return;
        lastSignature = signature;

        const errors = [...root.querySelectorAll('.validation-error')].filter(isVisible);
        const firstError = errors[0];
        if (!firstError) return;

        root.querySelectorAll('.ft-client-validation-focus-target').forEach((element) => {
            element.classList.remove('ft-client-validation-focus-target');
        });

        const target = firstError.closest(
            '.ft-proto-field, .ft-theme-field, .ft-client-logo-upload, .ft-client-contact-editor-row, .ft-shipping-card, .ft-client-prototype-section'
        ) || firstError;

        target.classList.add('ft-client-validation-focus-target');
        target.scrollIntoView({
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
            block: 'center',
            inline: 'nearest',
        });

        const control = target.matches('input, select, textarea, button')
            ? target
            : target.querySelector('input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled])');

        if (control instanceof HTMLElement) {
            window.setTimeout(() => {
                try { control.focus({ preventScroll: true }); } catch (_) { control.focus(); }
            }, 180);
        }
    };

    const observer = new MutationObserver(schedule);
    const start = () => {
        if (document.body) observer.observe(document.body, { childList: true, subtree: true });
        schedule();
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true });
    else start();

    document.addEventListener('livewire:navigated', schedule);
    document.addEventListener('livewire:init', schedule);
})();
