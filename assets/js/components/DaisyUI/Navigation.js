import m from "mithril";

export const Breadcrumbs = {
    view: ({ attrs, children }) => {
        return m(".breadcrumbs", { ...attrs }, m("ul", children));
    }
};

export const Dock = {
    view: ({ attrs, children }) => {
        return m(".dock", { ...attrs }, children);
    }
};

export const DockItem = {
    view: ({ attrs, children }) => {
        const { active, ...props } = attrs;
        return m("button", { ...props, class: `${active ? "dock-active" : ""} ${attrs.class || ""}`.trim() }, children);
    }
};

export const Link = {
    view: ({ attrs, children }) => {
        const { color, hover, ...props } = attrs;
        const classes = [
            "link",
            color && `link-${color}`,
            hover && "link-hover",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("a", { ...props, class: classes }, children);
    }
};

export const Menu = {
    view: ({ attrs, children }) => {
        const { horizontal, vertical, ...props } = attrs;
        const classes = [
            "menu",
            horizontal && "menu-horizontal",
            vertical && "menu-vertical",
            "bg-base-200",
            "rounded-box",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("ul", { ...props, class: classes }, children);
    }
};

export const Pagination = {
    view: ({ attrs }) => {
        const { total, current, onPageChange, ...props } = attrs;
        return m(".join", { ...props }, 
            Array.from({ length: total }).map((_, i) => 
                m("button.join-item.btn", {
                    class: current === i + 1 ? "btn-active" : "",
                    onclick: () => onPageChange && onPageChange(i + 1)
                }, i + 1)
            )
        );
    }
};
