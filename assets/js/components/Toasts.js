import m from 'mithril';
import ToastService from '../services/ToastService';
import { Icon } from './Icon';

/**
 * Toast container component to be included in the main layout
 */
const Toasts = {
    view: () => {
        return m('.toast.toast-top.toast-center', { class: 'z-[100]' }, 
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

                return m('.alert.shadow-lg.mb-2.flex.justify-between.items-start', { 
                    class: `${alertClass} min-w-[300px] max-w-[90vw]`, 
                    key: toast.id 
                }, [
                    m('.flex.items-start.gap-2', [
                        m(Icon, { name: iconName, class: 'mt-1' }),
                        m('span.text-sm.whitespace-pre-wrap.text-left', toast.message)
                    ]),
                    m('button.btn.btn-ghost.btn-xs.btn-circle', {
                        onclick: () => ToastService.remove(toast.id)
                    }, m(Icon, { name: 'fa-solid fa-xmark' }))
                ]);
            })
        );
    }
};

export default Toasts;
