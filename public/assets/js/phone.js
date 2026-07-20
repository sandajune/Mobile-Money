(() => {
    const INPUT_SELECTOR = 'input[data-phone-input], textarea[data-phone-input]';
    const DISPLAY_SELECTOR = '[data-phone-display]';
    const MAX_DIGITS = 10;
    const GROUPS = [3, 2, 3, 2];

    function clean(value) {
        return String(value ?? '').replace(/\D/g, '').slice(0, MAX_DIGITS);
    }

    function format(value) {
        const digits = clean(value);

        if (!digits) {
            return '';
        }

        const parts = [];
        let offset = 0;

        for (const size of GROUPS) {
            if (offset >= digits.length) {
                break;
            }

            parts.push(digits.slice(offset, offset + size));
            offset += size;
        }

        return parts.join(' ');
    }

    function formatInputElement(element) {
        element.value = format(element.value);
    }

    function formatDisplayElement(element) {
        const source = element.dataset.phoneValue ?? element.textContent;
        element.textContent = format(source);
    }

    function formatAll(root = document) {
        root.querySelectorAll(INPUT_SELECTOR).forEach(formatInputElement);
        root.querySelectorAll(DISPLAY_SELECTOR).forEach(formatDisplayElement);
    }

    document.addEventListener('DOMContentLoaded', () => {
        formatAll();
    });

    document.addEventListener('input', (event) => {
        if (event.target.matches(INPUT_SELECTOR)) {
            formatInputElement(event.target);
        }
    });

    document.addEventListener('submit', (event) => {
        event.target.querySelectorAll(INPUT_SELECTOR).forEach((element) => {
            element.value = clean(element.value);
        });
    });

    window.PhoneFormatter = {
        clean,
        format,
        formatAll,
        formatInputElement,
        formatDisplayElement,
    };
})();
