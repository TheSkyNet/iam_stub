// MessageDisplay.js
const MessageDisplay = {
    message: null,
    type: 'info', // can be 'success', 'error', 'info', 'warning'

    setMessage: function(msg, type = 'info') {
        MessageDisplay.message = msg;
        MessageDisplay.type = type;
        m.redraw();

        // Auto-clear message after 5 seconds
        setTimeout(() => {
            MessageDisplay.clear();
        }, 5000);
    },

    clear: function() {
        MessageDisplay.message = null;
        MessageDisplay.type = 'info';
        m.redraw();
    },

    view: function() {
        if (!MessageDisplay.message) return null;

        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'info': 'alert-info',
            'warning': 'alert-warning'
        }[MessageDisplay.type] || 'alert-info';

        return m(".alert-container.position-fixed.w-100", { style: "top: 20px; z-index: 1050" }, [
            m(".container", [
                m(`.alert.${alertClass}.alert-dismissible.fade.show`, [
                    m("span", MessageDisplay.message),
                    m("button.close[type=button]", {
                        onclick: MessageDisplay.clear
                    }, [
                        m("span[aria-hidden=true]", "×")
                    ])
                ])
            ])
        ]);
    }
};

module.exports = {MessageDisplay};