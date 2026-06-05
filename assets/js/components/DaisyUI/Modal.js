import m from "mithril";

const Modal = {
    view: ({ attrs, children }) => {
        const { id, open, onClose, boxClass, ...props } = attrs;
        return m("dialog.modal", { id, open, class: attrs.class, ...props }, [
            m(".modal-box", { class: boxClass }, [
                m("form", { method: "dialog" }, [
                    m("button.btn.btn-sm.btn-circle.btn-ghost.absolute.right-2.top-2", { onclick: onClose }, "x")
                ]),
                children
            ]),
            m("form.modal-backdrop", { method: "dialog" }, [
                m("button", { onclick: onClose }, "close")
            ])
        ]);
    }
};

export default Modal;
