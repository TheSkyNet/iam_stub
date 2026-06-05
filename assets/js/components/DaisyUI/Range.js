import m from "mithril";

const Range = {
    view: ({ attrs }) => {
        const { color, size, min, max, value, step, ...props } = attrs;
        const classes = [
            "range",
            color && `range-${color}`,
            size && `range-${size}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("input", { type: "range", min, max, value, step, ...props, class: classes });
    }
};

export default Range;
