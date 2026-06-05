import m from "mithril";

const WindowMockup = {
    view: ({ attrs, children }) => {
        return m(".mockup-window.border.bg-base-300", { ...attrs }, [
            m(".flex.justify-center.bg-base-200.px-4.py-16", children)
        ]);
    }
};

export default WindowMockup;
