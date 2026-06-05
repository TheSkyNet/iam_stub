import m from "mithril";

export const DockItem = {
    view: ({ attrs, children }) => {
        const { active, label, ...props } = attrs;
        return m("button", { 
            ...props, 
            class: `${active ? "dock-active" : ""} ${attrs.class || ""}`.trim(),
            "aria-current": active ? "page" : undefined,
            "aria-label": label
        }, children);
    }
};

const Dock = {
    view: ({ attrs, children }) => {
        return m(".dock", { 
            ...attrs, 
            role: "navigation", 
            "aria-label": "Dock" 
        }, children);
    }
};

export default Dock;
