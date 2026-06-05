import m from "mithril";

const Loading = {
    view: ({ attrs }) => {
        const { variant, size, color, ...props } = attrs;
        const classes = [
            "loading",
            `loading-${variant || "spinner"}`,
            size && `loading-${size}`,
            color && `text-${color}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("span", { 
            ...props, 
            class: classes, 
            role: "status", 
            "aria-label": "loading" 
        });
    }
};

export default Loading;
