
class ThemeManager {
    static STORAGE_KEY = 'theme';
    static VALUES = ['light', 'dark'];

    get current() {
        const stored = localStorage.getItem(ThemeManager.STORAGE_KEY);

        return ThemeManager.VALUES.includes(stored) ? stored : 'system';
    }

    get resolved() {
        return this.current === 'system' ? this.systemPreference() : this.current;
    }

    systemPreference() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    apply() {
        document.documentElement.classList.toggle('dark', this.resolved === 'dark');
    }

    set(value) {
        if (value === 'system') {
            localStorage.removeItem(ThemeManager.STORAGE_KEY);
        } else {
            localStorage.setItem(ThemeManager.STORAGE_KEY, value);
        }

        this.apply();
    }

    watch(callback) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', callback);
        document.addEventListener('livewire:navigated', callback);
    }
}

window.Theme = new ThemeManager();
window.Theme.apply();
window.Theme.watch(() => window.Theme.apply());

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        current: window.Theme.current,

        set(value) {
            this.current = value;
            window.Theme.set(value);
        },
    });
});
