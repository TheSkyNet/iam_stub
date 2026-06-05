import m from "mithril";

export const Accordion = {
    view: ({ attrs }) => {
        const { name, items = [], ...props } = attrs;
        return m(".join.join-vertical.w-full", { ...props }, 
            items.map((item) => 
                m(".collapse.collapse-arrow.join-item.border.border-base-300", [
                    m("input", { type: "radio", name, checked: item.active }),
                    m(".collapse-title.text-xl.font-medium", item.title),
                    m(".collapse-content", item.content)
                ])
            )
        );
    }
};

export const Collapse = {
    view: ({ attrs, children }) => {
        const { title, arrow, plus, ...props } = attrs;
        const classes = [
            "collapse",
            arrow && "collapse-arrow",
            plus && "collapse-plus",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes }, [
            m("input", { type: "checkbox" }),
            m(".collapse-title.text-xl.font-medium", title),
            m(".collapse-content", children)
        ]);
    }
};

export const Countdown = {
    view: ({ attrs }) => {
        const { value, size, ...props } = attrs;
        return m("span.countdown", { ...props }, 
            m("span", { style: `--value:${value}` })
        );
    }
};

export const Kbd = {
    view: ({ attrs, children }) => {
        const { size, ...props } = attrs;
        const classes = ["kbd", size && `kbd-${size}`, attrs.class].filter(Boolean).join(" ");
        return m("kbd", { ...props, class: classes }, children);
    }
};

export const Status = {
    view: ({ attrs }) => {
        const { color, size, ...props } = attrs;
        const classes = [
            "status",
            color && `status-${color}`,
            size && `status-${size}`,
            attrs.class
        ].filter(Boolean).join(" ");
        return m("span", { ...props, class: classes });
    }
};

export const List = {
    view: ({ attrs, children }) => {
        return m("ul.list", { ...attrs }, children);
    }
};

export const ListRow = {
    view: ({ attrs, children }) => {
        return m("li.list-row", { ...attrs }, children);
    }
};

export const Timeline = {
    view: ({ attrs, children }) => {
        const { vertical, horizontal, ...props } = attrs;
        const classes = [
            "timeline",
            vertical && "timeline-vertical",
            horizontal && "timeline-horizontal",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("ul", { ...props, class: classes }, children);
    }
};

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

export const Diff = {
    view: ({ attrs }) => {
        const { item1, item2, ...props } = attrs;
        return m(".diff", { ...props }, [
            m(".diff-item-1", item1),
            m(".diff-item-2", item2),
            m(".diff-resizer")
        ]);
    }
};
