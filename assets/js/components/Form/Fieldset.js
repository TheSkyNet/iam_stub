import m from "mithril";
import { Icon } from "../Icon";

const Fieldset = {
    view: ({ attrs, children }) => {
        const { legend, icon, name, class: className = "", ...props } = attrs;
        
        return m("fieldset.fieldset", { 
            class: `bg-base-100 border-base-300 rounded-box border p-4 shadow-sm ${className}`.trim(), 
            ...props 
        }, [
            legend && m("legend.fieldset-legend.text-sm.font-medium", [
                (name || icon) && m(Icon, { name: name || icon, class: "mr-1" }),
                legend
            ]),
            children
        ]);
    }
};

export default Fieldset;
