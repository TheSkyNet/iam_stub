import m from "mithril";

const Label = {
    view: ({ attrs, children }) => {
        return m("label.label", { ...attrs }, children);
    }
};

export default Label;
