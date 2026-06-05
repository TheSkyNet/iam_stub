import m from "mithril";

const Stat = {
    view: ({ attrs }) => {
        const { label, value, desc, figure, ...props } = attrs;
        return m(".stat", { ...props }, [
            figure && m(".stat-figure", figure),
            label && m(".stat-title", label),
            value && m(".stat-value", value),
            desc && m(".stat-desc", desc)
        ]);
    }
};

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

export { Stat, Stats };
