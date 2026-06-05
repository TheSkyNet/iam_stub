import m from "mithril";

const Button = {
    view: ({ attrs, children }) => {
        const { color, size, variant, loading, circle, square, outline, ghost, link, wide, block, soft, dash, ...props } = attrs;
        const classes = [
            "btn",
            color && `btn-${color}`,
            size && `btn-${size}`,
            variant && `btn-${variant}`,
            circle && "btn-circle",
            square && "btn-square",
            outline && "btn-outline",
            ghost && "btn-ghost",
            link && "btn-link",
            wide && "btn-wide",
            block && "btn-block",
            soft && "btn-soft",
            dash && "btn-dash",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("button", { ...props, class: classes }, [
            loading && m("span.loading.loading-spinner"),
            children
        ]);
    }
};

export default Button;
