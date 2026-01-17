import m from 'mithril';
import ToastService from '../services/ToastService';
import { Icon } from './Icon';

/**
 * Toast container component to be included in the main layout
 */
const Toasts = {
    view: () => {
        return m("div", {"class": "toast toast-end z-50"}, 
            ToastService.toasts.map(toast => {
                const alertClass = {
                    info: 'alert-info',
                    success: 'alert-success',
                    warning: 'alert-warning',
                    error: 'alert-error'
                }[toast.type] || 'alert-info';
                
                const iconName = {
                    info: 'fa-solid fa-circle-info',
                    success: 'fa-solid fa-circle-check',
                    warning: 'fa-solid fa-triangle-exclamation',
                    error: 'fa-solid fa-circle-xmark'
                }[toast.type] || 'fa-solid fa-circle-info';

                return m("div", {"class": `alert ${alertClass} shadow-sm`, "key": toast.id}, [
                    m(Icon, { name: iconName }),
                    m("span", toast.message),
                    m("button", {
                        "class": "btn btn-ghost btn-xs btn-circle",
                        "onclick": () => ToastService.remove(toast.id)
                    }, m(Icon, { name: 'fa-solid fa-xmark' }))
                ]);
            })
        );
    }
};

export default Toasts;
