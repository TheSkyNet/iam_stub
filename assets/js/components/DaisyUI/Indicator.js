import m from "mithril";

const Indicator = {
    view: ({ attrs, item, children }) => {
        return m(".indicator", { ...attrs }, [
            m("span.indicator-item", item),
            children
        ]);
    }
};

export default Indicator;
