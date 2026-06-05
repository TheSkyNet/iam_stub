import m from "mithril";

const Diff = {
    view: ({ attrs }) => {
        const { item1, item2, ...props } = attrs;
        return m(".diff", { ...props, role: "img", "aria-label": "Image comparison" }, [
            m(".diff-item-1", { "aria-hidden": "true" }, item1),
            m(".diff-item-2", { "aria-hidden": "true" }, item2),
            m(".diff-resizer", { role: "slider", "aria-label": "Resize comparison" })
        ]);
    }
};

export default Diff;
