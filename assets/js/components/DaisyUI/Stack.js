import m from "mithril";

const Stack = {
    view: ({ attrs, children }) => {
        return m(".stack", { ...attrs }, children);
    }
};

export default Stack;
