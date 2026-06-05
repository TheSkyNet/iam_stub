import m from "mithril";

const Card = {
    view: ({ attrs, children }) => {
        const { title, image, actions, side, imageFull, ...props } = attrs;
        const classes = [
            "card",
            "bg-base-100",
            "shadow-sm",
            side && "lg:card-side",
            imageFull && "image-full",
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes }, [
            image && m("figure", m("img", { src: image, alt: title || "Card Image" })),
            m(".card-body", [
                title && m("h2.card-title", title),
                children,
                actions && m(".card-actions.justify-end", actions)
            ])
        ]);
    }
};

export default Card;
