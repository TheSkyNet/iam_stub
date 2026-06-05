import m from "mithril";

const TextInput = {
    view: ({ attrs }) => {
        const { color, size, ghost, ...props } = attrs;
        const classes = [
            "input",
            color && `input-${color}`,
            size && `input-${size}`,
            ghost && "input-ghost",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("input", { type: "text", ...props, class: classes });
    }
};

export default TextInput;
