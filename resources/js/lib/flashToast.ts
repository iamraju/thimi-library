import { router } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import { t } from "@/composables/useI18n";
import type { FlashToast } from "@/types/ui";

export function initializeFlashToast(): void {
    router.on("flash", (event) => {
        const flash = (event as CustomEvent).detail?.flash;
        const data = flash?.toast as FlashToast | undefined;

        if (!data) {
            return;
        }

        toast[data.type](t(data.message));
    });
}
