import m from "mithril";

const Tooltip = {
    view: ({ attrs, children }) => {
        const { text, position, color, open, ...props } = attrs;
        const classes = [
            "tooltip",
            position && `tooltip-${position}`,
            color && `tooltip-${color}`,
            open && "tooltip-open",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("div", { 
            ...props, 
            "data-tip": text, 
            class: classes, 
            role: "tooltip",
            "aria-label": text
        }, children);
    }
};

export default Tooltip;
