import m from "mithril";
import { Icon } from "../Icon";

const FormField = {
    view: ({ attrs }) => {
        const { label, icon, helpText, error, grow = true, class: className = "", containerClass = "", fieldset = true, ...inputProps } = attrs;
        
        const inputElement = m("label.input.w-full", { class: error ? "input-error" : "" }, [
            icon && m(Icon, { icon, class: "opacity-50" }),
            m("input", { 
                class: `${grow ? "grow" : ""} ${className}`.trim(),
                ...inputProps
            })
        ]);

        if (fieldset && label) {
            return m("fieldset.fieldset", { class: containerClass }, [
                m("legend.fieldset-legend", label),
                inputElement,
                helpText && m("p.fieldset-label", helpText),
                error && m("p.fieldset-label.text-error", error)
            ]);
        }

        if (label) {
            return m(".form-control.w-full", { class: containerClass }, [
                m("label.label", m("span.label-text", label)),
                inputElement,
                helpText && m("p.label.label-text-alt", helpText),
                error && m("p.label.label-text-alt.text-error", error)
            ]);
        }

        return inputElement;
    }
};

export default FormField;
