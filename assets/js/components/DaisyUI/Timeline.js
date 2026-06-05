import m from "mithril";

export const TimelineItem = {
    view: ({ attrs, children }) => {
        const { start, middle, end, connect, ...props } = attrs;
        return m("li", { ...props }, [
            connect === "start" || connect === "both" ? m("hr") : null,
            start && m(".timeline-start", start),
            middle && m(".timeline-middle", middle),
            end && m(".timeline-end", end),
            connect === "end" || connect === "both" ? m("hr") : null,
        ]);
    }
};

const Timeline = {
    view: ({ attrs, children }) => {
        const { vertical, horizontal, ...props } = attrs;
        const classes = [
            "timeline",
            vertical && "timeline-vertical",
            horizontal && "timeline-horizontal",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("ul", { ...props, class: classes, role: "list" }, children);
    }
};

export default Timeline;
