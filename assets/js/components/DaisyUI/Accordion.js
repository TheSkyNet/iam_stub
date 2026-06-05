import m from "mithril";

const Accordion = {
    view: ({ attrs }) => {
        const { name, items = [], ...props } = attrs;
        return m(".join.join-vertical.w-full", { ...props }, 
            items.map((item) => 
                m(".collapse.collapse-arrow.join-item.border.border-base-300", [
                    m("input", { type: "radio", name, checked: item.active, "aria-label": item.title }),
                    m(".collapse-title.text-xl.font-medium", item.title),
                    m(".collapse-content", item.content)
                ])
            )
        );
    }
};

export default Accordion;
