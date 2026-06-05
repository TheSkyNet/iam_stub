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

export default Stat;
