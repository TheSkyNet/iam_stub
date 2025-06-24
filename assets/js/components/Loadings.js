// Loading component
const Loadings = {
    view: function() {
        return m("div.loading", [
            m("div.spinner-border.text-primary", {
                role: "status"
            }),
            m("span.sr-only", "Loading...")
        ]);
    }
};


export default Loadings;