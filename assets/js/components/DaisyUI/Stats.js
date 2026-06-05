import m from "mithril";

const Stats = {
    view: ({ attrs, children }) => {
        const { horizontal, vertical, ...props } = attrs;
        const classes = [
            "stats",
            horizontal && "stats-horizontal",
            vertical && "stats-vertical",
            "shadow",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes }, children);
    }
};

export default Stats;
