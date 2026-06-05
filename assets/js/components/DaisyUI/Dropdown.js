import m from "mithril";

const Dropdown = {
    view: ({ attrs, children }) => {
        const { label, align, position, hover, open, triggerClass, contentClass, ...props } = attrs;
        const classes = [
            "dropdown",
            align && `dropdown-${align}`,
            position && `dropdown-${position}`,
            hover && "dropdown-hover",
            open && "dropdown-open",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes }, [
            m("div", { 
                tabindex: 0, 
                role: "button", 
                class: triggerClass || "btn m-1",
                "aria-haspopup": "true",
                "aria-expanded": open ? "true" : "false"
            }, label),
            m("ul", { 
                tabindex: 0, 
                role: "menu",
                class: `dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52 ${contentClass || ""}` 
            }, children)
        ]);
    }
};

export default Dropdown;
