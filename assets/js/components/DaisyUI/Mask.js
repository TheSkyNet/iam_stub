import m from "mithril";

const Mask = {
    view: ({ attrs }) => {
        const { shape, ...props } = attrs;
        const classes = [
            "mask",
            shape && `mask-${shape}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("img", { ...props, class: classes });
    }
};

export default Mask;
