import m from "mithril";

const CodeMockup = {
    view: ({ attrs, children }) => {
        return m(".mockup-code", { ...attrs }, children);
    }
};

export default CodeMockup;
