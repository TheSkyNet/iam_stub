import m from "mithril";

const HoverGallery = {
    view: ({ attrs, items = [] }) => {
        return m(".grid.grid-cols-2.md:grid-cols-4.gap-4", { ...attrs }, 
            items.map(item => 
                m(".overflow-hidden.rounded-box.group", [
                    m("img.transition-transform.duration-500.group-hover:scale-110", { 
                        src: item.src, 
                        alt: item.alt || "" 
                    })
                ])
            )
        );
    }
};

export default HoverGallery;
