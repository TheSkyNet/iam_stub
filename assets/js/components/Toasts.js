import m from "mithril";
import { Icon } from "./Icon";
import { ToastService } from "../services/ToastService";

/**
 * Truncates message for UI display
 */
const MAX_MESSAGE_LENGTH = 2000;
function truncate(str) {
    if (typeof str !== 'string') return '';
    return str.length > MAX_MESSAGE_LENGTH ? str.slice(0, MAX_MESSAGE_LENGTH) + '…' : str;
}

/**
 * Mithril component for rendering toasts
 */
const Toasts = {
    view() {
        if (ToastService.toasts.length === 0) return null;
        
        return m(".toast.toast-top.toast-center", { class: "z-[9999]" },
            ToastService.toasts.map(t =>
                m(".alert.shadow-lg.flex.items-center.gap-2", {
                    class: `alert-${t.type || 'error'}`,
                    key: t.id
                }, [
                    t.type === 'success' ? m(Icon, { name: 'fa-solid fa-circle-check' }) :
                    t.type === 'warning' ? m(Icon, { name: 'fa-solid fa-triangle-exclamation' }) :
                    t.type === 'info' ? m(Icon, { name: 'fa-solid fa-circle-info' }) :
                    m(Icon, { name: 'fa-solid fa-circle-exclamation' }),
                    
                    m("span", truncate(t.message)),
                    
                    m("button.btn.btn-ghost.btn-xs", {
                        onclick: () => ToastService.remove(t.id),
                        "aria-label": "Close"
                    }, "×")
                ])
            )
        );
    }
};

export { Toasts };
