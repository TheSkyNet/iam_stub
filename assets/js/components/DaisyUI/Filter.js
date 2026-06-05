import m from "mithril";

const Filter = {
    view: ({ attrs, options = [], selected, onSelect }) => {
        return m(".join", { ...attrs, role: "group", "aria-label": "Filter" }, 
            options.map(opt => 
                m("button.join-item.btn.btn-sm", {
                    class: selected === opt.value ? "btn-active" : "",
                    onclick: () => onSelect && onSelect(opt.value),
                    "aria-pressed": selected === opt.value
                }, opt.label)
            )
        );
    }
};

export default Filter;
