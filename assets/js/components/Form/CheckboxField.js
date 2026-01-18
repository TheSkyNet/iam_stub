import m from "mithril";

const CheckboxField = {
    view: ({ attrs }) => {
        const { label, checked, onchange, class: className = "", ...props } = attrs;
        
        return m("label.label.cursor-pointer.justify-start.gap-3", { class: className }, [
            m("input[type=checkbox].checkbox.checkbox-primary", {
                checked,
                onchange,
                ...props
            }),
            label && m("span.label-text", label)
        ]);
    }
};

export default CheckboxField;
