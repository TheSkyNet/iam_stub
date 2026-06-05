import m from "mithril";

const Carousel = {
    view: ({ attrs, children }) => {
        const { vertical, ...props } = attrs;
        const classes = [
            "carousel",
            vertical && "carousel-vertical",
            attrs.class
        ].filter(Boolean).join(" ");
        return m("div", { 
            ...props, 
            class: classes, 
            role: "region", 
            "aria-label": "Carousel" 
        }, [].concat(children).filter(Boolean).map(child => 
            m(".carousel-item", { role: "group", "aria-roledescription": "slide" }, child)
        ));
    }
};

export default Carousel;
