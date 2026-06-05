import m from "mithril";

const Radio = {
    view: ({ attrs }) => {
        const { color, size, ...props } = attrs;
        const classes = [
            "radio",
            color && `radio-${color}`,
            size && `radio-${size}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("input", { type: "radio", ...props, class: classes });
    }
};

export default Radio;
