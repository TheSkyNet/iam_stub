import m from "mithril";

const Tab = {
    view: ({ attrs, children }) => {
        const { active, ...props } = attrs;
        const classes = [
            "tab",
            active && "tab-active",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("a", { 
            role: "tab", 
            ...props, 
            class: classes,
            "aria-selected": active ? "true" : "false"
        }, children);
    }
};

export default Tab;
