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

export default Tabs;
