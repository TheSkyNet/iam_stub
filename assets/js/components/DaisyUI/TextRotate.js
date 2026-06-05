import m from "mithril";

const TextRotate = {
    view: ({ attrs, items = [] }) => {
        return m("span.inline-flex.flex-col.h-[1em].overflow-hidden", { ...attrs }, 
            m("ul.animate-text-slide.text-left.leading-tight", 
                items.map(item => m("li", item))
            )
        );
    }
};

export default TextRotate;
