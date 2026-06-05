import m from "mithril";

const Skeleton = {
    view: ({ attrs }) => {
        const classes = ["skeleton", attrs.class].filter(Boolean).join(" ");
        return m("div", { 
            ...attrs, 
            class: classes, 
            role: "status", 
            "aria-label": "loading..." 
        });
    }
};

export default Skeleton;
