import m from "mithril";
import { Icon } from "../Icon";

const SubmitButton = {
    view: ({ attrs, children }) => {
        const { loading, icon, class: className = "", ...props } = attrs;
        
        return m("button.btn", {
            type: "submit",
            class: className,
            disabled: loading,
            ...props
        }, [
            loading ? m("span.loading.loading-spinner") : (icon && m(Icon, { icon, class: "mr-1" })),
            children
        ]);
    }
};

export default SubmitButton;
