import m from "mithril";

const Toast = {
    view: ({ attrs, children }) => {
        const { position = "end", align = "bottom", ...props } = attrs;
        const classes = [
            "toast",
            `toast-${position}`,
            `toast-${align}`,
            attrs.class
        ].filter(Boolean).join(" ");
        return m("div", { 
            ...props, 
            class: classes, 
            role: "alert", 
            "aria-live": "assertive" 
        }, children);
    }
};

export default Toast;
