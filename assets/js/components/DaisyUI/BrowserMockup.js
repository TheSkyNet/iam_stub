import m from "mithril";

const BrowserMockup = {
    view: ({ attrs, children }) => {
        const { url, ...props } = attrs;
        return m(".mockup-browser.border.bg-base-300", { ...props }, [
            m(".mockup-browser-toolbar", [
                m(".input", url || "https://daisyui.com")
            ]),
            m(".flex.justify-center.bg-base-200.px-4.py-16", children)
        ]);
    }
};

export default BrowserMockup;
