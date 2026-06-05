import m from "mithril";

const Step = {
    view: ({ attrs, children }) => {
        const { color, content, ...props } = attrs;
        const classes = [
            "step",
            color && `step-${color}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("li", { 
            ...props, 
            "data-content": content, 
            class: classes,
            "aria-current": color === "primary" ? "step" : undefined
        }, children);
    }
};

export default Step;
