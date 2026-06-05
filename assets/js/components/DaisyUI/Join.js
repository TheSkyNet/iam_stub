import m from "mithril";

const Join = {
    view: ({ attrs, children }) => {
        const { vertical, horizontal, ...props } = attrs;
        const classes = [
            "join",
            vertical && "join-vertical",
            horizontal && "join-horizontal",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes, role: "group" }, children);
    }
};

export default Join;
