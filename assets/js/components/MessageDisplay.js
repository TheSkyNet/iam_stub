// MessageDisplay.js - Updated for Tailwind/DaisyUI
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
                return 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
            case 'error':
                return 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';
            case 'warning':
                return 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z';
            case 'info':
            default:
                return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
        }
    },

    view: function() {
        if (!MessageDisplay.visible || !MessageDisplay.message) return null;

        return m(".message-display.fixed.top-4.right-4.z-50.max-w-md", [
            m(`.${MessageDisplay.getAlertClass()}`, [
                m("svg.stroke-current.shrink-0.w-6.h-6", {
                    fill: "none",
                    viewBox: "0 0 24 24"
                }, [
                    m("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        "stroke-width": "2",
                        d: MessageDisplay.getIcon()
                    })
                ]),
                m("span", MessageDisplay.message),
                m("button.btn.btn-sm.btn-ghost.ml-auto", {
                    onclick: MessageDisplay.clear
                }, "×")
            ])
        ]);
    }
};

module.exports = {MessageDisplay};
