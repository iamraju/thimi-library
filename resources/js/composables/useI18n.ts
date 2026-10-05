import { usePage } from '@inertiajs/vue3';

export function t(
    key: string,
    replace: Record<string, string | number> = {},
): string {
    let text = usePage().props.translations?.[key] ?? key;

    for (const [name, value] of Object.entries(replace)) {
        text = text.replaceAll(`:${name}`, String(value));
    }

    return text;
}

export function useI18n() {
    const page = usePage();

    return {
        t,
        locale: () => page.props.locale,
        locales: () => page.props.locales,
        defaultLocale: () => page.props.defaultLocale,
    };
}
