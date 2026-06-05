import m from "mithril";

const Status = {
    view: ({ attrs }) => {
        const { color, size, ...props } = attrs;
        const classes = [
            "status",
            color && `status-${color}`,
            size && `status-${size}`,
            attrs.class
        ].filter(Boolean).join(" ");
        return m("span", { 
            ...props, 
            class: classes, 
            role: "status", 
            "aria-label": color || "status" 
        });
    }
};

export default Status;
