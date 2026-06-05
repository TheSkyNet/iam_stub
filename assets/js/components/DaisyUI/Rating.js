import m from "mithril";

const Rating = {
    view: ({ attrs }) => {
        const { size, color, count = 5, name, value, onchange, ...props } = attrs;
        const classes = [
            "rating",
            size && `rating-${size}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes }, 
            Array.from({ length: count }).map((_, i) => 
                m("input", {
                    type: "radio",
                    name,
                    class: `mask mask-star-2 ${color ? `bg-${color}` : "bg-orange-400"}`,
                    checked: value === i + 1,
                    onchange: () => onchange && onchange(i + 1)
                })
            )
        );
    }
};

export default Rating;
