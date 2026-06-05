import m from "mithril";

const Steps = {
    view: ({ attrs, children }) => {
        const { vertical, horizontal, ...props } = attrs;
        const classes = [
            "steps",
            vertical && "steps-vertical",
            horizontal && "steps-horizontal",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("ul", { ...props, class: classes, role: "list" }, children);
    }
};

export default Steps;
