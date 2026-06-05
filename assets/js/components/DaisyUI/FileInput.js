import m from "mithril";

const FileInput = {
    view: ({ attrs }) => {
        const { color, size, ghost, ...props } = attrs;
        const classes = [
            "file-input",
            color && `file-input-${color}`,
            size && `file-input-${size}`,
            ghost && "file-input-ghost",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("input", { 
            type: "file", 
            ...props, 
            class: classes 
        });
    }
};

export default FileInput;
