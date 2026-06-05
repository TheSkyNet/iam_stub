import m from "mithril";

const Breadcrumbs = {
    view: ({ attrs, children }) => {
        return m(".breadcrumbs", { 
            ...attrs, 
            "aria-label": "breadcrumb" 
        }, m("ul", children));
    }
};

export default Breadcrumbs;
