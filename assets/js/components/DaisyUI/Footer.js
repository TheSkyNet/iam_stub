import m from "mithril";

const Footer = {
    view: ({ attrs, children }) => {
        const { center, ...props } = attrs;
        const classes = [
            "footer",
            center && "footer-center",
            "p-10 bg-base-200 text-base-content",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("footer", { 
            ...props, 
            class: classes, 
            role: "contentinfo" 
        }, children);
    }
};

export default Footer;
