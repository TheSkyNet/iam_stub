import m from "mithril";

const Step = {
    view: ({ attrs, children }) => {
        const { color, content, ...props } = attrs;
        const classes = [
            "step",
            color && `step-${color}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("li", { ...props, "data-content": content, class: classes }, children);
    }
};

const Steps = {
    view: ({ attrs, children }) => {
        const { vertical, horizontal, ...props } = attrs;
        const classes = [
            "steps",
            vertical && "steps-vertical",
            horizontal && "steps-horizontal",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("ul", { ...props, class: classes }, children);
    }
};

export { Step, Steps };
