import m from "mithril";

const Validator = {
    view: ({ attrs, children }) => {
        const { type, hint, ...props } = attrs;
        const classes = [
            "validator",
            type && `validator-${type}`,
            attrs.class
        ].filter(Boolean).join(" ");
        return m("div", { ...props, class: classes }, [
            children,
            hint && m(".validator-hint", hint)
        ]);
    }
};

export default Validator;
