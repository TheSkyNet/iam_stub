import m from "mithril";

const Tabs = {
    view: ({ attrs, children }) => {
        const { variant, size, ...props } = attrs;
        const classes = [
            "tabs",
            variant && `tabs-${variant}`,
            size && `tabs-${size}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { role: "tablist", ...props, class: classes }, children);
    }
};

const Tab = {
    view: ({ attrs, children }) => {
        const { active, ...props } = attrs;
        const classes = [
            "tab",
            active && "tab-active",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("a", { role: "tab", ...props, class: classes }, children);
    }
};

export { Tabs, Tab };
