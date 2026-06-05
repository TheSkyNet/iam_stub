import m from "mithril";

const Kbd = {
    view: ({ attrs, children }) => {
        const { size, ...props } = attrs;
        const classes = ["kbd", size && `kbd-${size}`, attrs.class].filter(Boolean).join(" ");
        return m("kbd", { ...props, class: classes }, children);
    }
};

export default Kbd;
