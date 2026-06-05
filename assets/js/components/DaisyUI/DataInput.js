import m from "mithril";

export const Calendar = {
    view: ({ attrs }) => {
        return m("div.calendar", { ...attrs });
    }
};

export const Fieldset = {
    view: ({ attrs, children }) => {
        const { legend, label, ...props } = attrs;
        return m("fieldset.fieldset", { ...props }, [
            legend && m("legend.fieldset-legend", legend),
            children,
            label && m("p.fieldset-label", label)
        ]);
    }
};

export const Label = {
    view: ({ attrs, children }) => {
        return m("label.label", { ...attrs }, children);
    }
};

export const Validator = {
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
