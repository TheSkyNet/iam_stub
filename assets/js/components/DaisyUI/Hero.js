import m from "mithril";

const Hero = {
    view: ({ attrs, children }) => {
        const { image, overlay, ...props } = attrs;
        const style = image ? { backgroundImage: `url(${image})` } : {};
        
        return m(".hero", { ...props, style, class: attrs.class }, [
            overlay && m(".hero-overlay.bg-opacity-60"),
            m(".hero-content", children)
        ]);
    }
};

export default Hero;
