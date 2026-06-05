import m from "mithril";

export const Toast = {
    view: ({ attrs, children }) => {
        const { position = "end", align = "bottom", ...props } = attrs;
        const classes = [
            "toast",
            `toast-${position}`,
            `toast-${align}`,
            attrs.class
        ].filter(Boolean).join(" ");
        return m("div", { ...props, class: classes }, children);
    }
};

export const Tooltip = {
    view: ({ attrs, children }) => {
        const { text, position, color, open, ...props } = attrs;
        const classes = [
            "tooltip",
            position && `tooltip-${position}`,
            color && `tooltip-${color}`,
            open && "tooltip-open",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("div", { ...props, "data-tip": text, class: classes }, children);
    }
};
