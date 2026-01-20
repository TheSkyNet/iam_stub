import m from "mithril";
import { Icon } from "../Icon";

const FormField = {
    view: ({ attrs }) => {
        const { label, icon, helpText, error, grow = true, class: className = "", containerClass = "", fieldset = true, ...inputProps } = attrs;
        
        const id = inputProps.id || (label ? `field-${label.toLowerCase().replace(/\s+/g, '-')}` : null);
        const helpId = helpText ? `${id}-help` : null;
        const errorId = error ? `${id}-error` : null;

        const inputElement = m("label.input.w-full", { 
            class: error ? "input-error" : "",
            for: id
        }, [
            icon && m(Icon, { icon, class: "opacity-50" }),
            m("input", { 
                id,
                class: `${grow ? "grow" : ""} ${className}`.trim(),
                "aria-describedby": [helpId, errorId].filter(Boolean).join(' ') || undefined,
                "aria-invalid": error ? "true" : undefined,
                ...inputProps
            })
        ]);

        if (fieldset && label) {
            return m("fieldset.fieldset", { class: containerClass }, [
                m("legend.fieldset-legend", label),
                inputElement,
                helpText && m("p.fieldset-label", { id: helpId }, helpText),
                error && m("p.fieldset-label.text-error", { id: errorId, role: "alert" }, error)
            ]);
        }

        if (label) {
            return m(".form-control.w-full", { class: containerClass }, [
                m("label.label", { for: id }, m("span.label-text", label)),
                inputElement,
                helpText && m("p.label.label-text-alt", { id: helpId }, helpText),
                error && m("p.label.label-text-alt.text-error", { id: errorId, role: "alert" }, error)
            ]);
        }

        return inputElement;
    }
};

export default FormField;
