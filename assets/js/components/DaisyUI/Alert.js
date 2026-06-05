import m from "mithril";

const Alert = {
    view: ({ attrs, children }) => {
        const { type, ...props } = attrs;
        const classes = [
            "alert",
            type && `alert-${type}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { role: "alert", ...props, class: classes }, children);
    }
};

export default Alert;
