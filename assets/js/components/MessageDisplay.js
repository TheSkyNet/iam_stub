// MessageDisplay.js - Updated for Tailwind/DaisyUI
import m from "mithril";
const { Icon } = require('./Icon');

const MessageDisplay = {
    message: null,
    type: 'info', // can be 'success', 'error', 'info', 'warning'
    visible: false,
    timeout: null,

    setMessage: function(msg, type = 'info', duration = 5000) {
        MessageDisplay.message = msg;
        MessageDisplay.type = type;
        MessageDisplay.visible = true;

        // Clear existing timeout
        if (MessageDisplay.timeout) {
            clearTimeout(MessageDisplay.timeout);
        }

        // Auto-clear message after duration
        if (duration > 0) {
            MessageDisplay.timeout = setTimeout(() => {
                MessageDisplay.clear();
            }, duration);
        }

        m.redraw();
    },

    clear: function() {
        MessageDisplay.message = null;
        MessageDisplay.type = 'info';
        MessageDisplay.visible = false;
        if (MessageDisplay.timeout) {
            clearTimeout(MessageDisplay.timeout);
            MessageDisplay.timeout = null;
        }
        m.redraw();
    },

    getAlertClass: function() {
        const baseClass = 'alert';
        switch (MessageDisplay.type) {
            case 'success':
                return `${baseClass} alert-success`;
            case 'error':
                return `${baseClass} alert-error`;
            case 'warning':
                return `${baseClass} alert-warning`;
            case 'info':
            default:
                return `${baseClass} alert-info`;
        }
    },

    getIcon: function() {
        switch (MessageDisplay.type) {
            case 'success':
                return 'fa-solid fa-circle-check';
            case 'error':
                return 'fa-solid fa-circle-exclamation';
            case 'warning':
                return 'fa-solid fa-triangle-exclamation';
            case 'info':
            default:
                return 'fa-solid fa-circle-info';
        }
    },

    view: function() {
        if (!MessageDisplay.visible || !MessageDisplay.message) return null;

        return m(".message-display.fixed.top-4.right-4.z-50.max-w-md", [
            m(`.${MessageDisplay.getAlertClass()}`, [
                m(Icon, { name: MessageDisplay.getIcon(), class: 'shrink-0 text-lg' }),
                m("span", MessageDisplay.message),
                m("button.btn.btn-sm.btn-ghost.ml-auto.flex.items-center", {
                    onclick: MessageDisplay.clear,
                    "aria-label": "Close notification"
                }, [
                    m(Icon, { name: 'fa-solid fa-xmark', class: 'text-base' })
                ])
            ])
        ]);
    }
};

export {MessageDisplay};
