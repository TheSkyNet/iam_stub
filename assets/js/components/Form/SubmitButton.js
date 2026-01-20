import m from "mithril";
import { Icon } from "../Icon";

const SubmitButton = {
    view: ({ attrs, children }) => {
        const { loading, icon, name, class: className = "", ...props } = attrs;
        
        return m("button.btn", {
            type: "submit",
            class: className,
            disabled: loading,
            ...props
        }, [
            loading ? m("span.loading.loading-spinner") : ((name || icon) && m(Icon, { name: name || icon, class: "mr-1" })),
            children
        ]);
    }
};

export default SubmitButton;
