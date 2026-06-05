import m from "mithril";

const Textarea = {
    view: ({ attrs }) => {
        const { color, size, ghost, ...props } = attrs;
        const classes = [
            "textarea",
            color && `textarea-${color}`,
            size && `textarea-${size}`,
            ghost && "textarea-ghost",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("textarea", { ...props, class: classes });
    }
};

export default Textarea;
