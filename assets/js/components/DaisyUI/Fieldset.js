import m from "mithril";

const Fieldset = {
    view: ({ attrs, children }) => {
        const { legend, label, ...props } = attrs;
        return m("fieldset.fieldset", { ...props }, [
            legend && m("legend.fieldset-legend", legend),
            children,
            label && m("p.fieldset-label", label)
        ]);
    }
};

export default Fieldset;
