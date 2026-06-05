import m from "mithril";

const Collapse = {
    view: ({ attrs, children }) => {
        const { title, arrow, plus, ...props } = attrs;
        const classes = [
            "collapse",
            arrow && "collapse-arrow",
            plus && "collapse-plus",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes, role: "region", "aria-label": title }, [
            m("input", { type: "checkbox", "aria-label": title }),
            m(".collapse-title.text-xl.font-medium", title),
            m(".collapse-content", children)
        ]);
    }
};

export default Collapse;
