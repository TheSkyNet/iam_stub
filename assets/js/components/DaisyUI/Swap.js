import m from "mithril";

const Swap = {
    view: ({ attrs }) => {
        const { on, off, active, effect, ...props } = attrs;
        const classes = [
            "swap",
            effect && `swap-${effect}`,
            active && "swap-active",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("label", { ...props, class: classes }, [
            m("input[type=checkbox]"),
            m(".swap-on", on),
            m(".swap-off", off)
        ]);
    }
};

export default Swap;
