import m from "mithril";

const Carousel = {
    view: ({ attrs, children }) => {
        const { vertical, ...props } = attrs;
        const classes = [
            "carousel",
            vertical && "carousel-vertical",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("div", { ...props, class: classes }, children.map(child => 
            m(".carousel-item", child)
        ));
    }
};

export default Carousel;
