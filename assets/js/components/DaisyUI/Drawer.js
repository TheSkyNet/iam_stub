import m from "mithril";

const Drawer = {
    view: ({ attrs, children }) => {
        const { id, side, content, ...props } = attrs;
        return m(".drawer", { ...props }, [
            m("input.drawer-toggle", { id, type: "checkbox" }),
            m(".drawer-content", content),
            m(".drawer-side", [
                m("label.drawer-overlay", { for: id, "aria-label": "close sidebar" }),
                m("div", side)
            ])
        ]);
    }
};

export default Drawer;
