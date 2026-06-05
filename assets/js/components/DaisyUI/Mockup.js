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

const CodeMockup = {
    view: ({ attrs, children }) => {
        return m(".mockup-code", { ...attrs }, children);
    }
};

const PhoneMockup = {
    view: ({ attrs, children }) => {
        return m(".mockup-phone", { ...attrs }, [
            m(".camera"),
            m(".display", [
                m(".artboard.artboard-demo.phone-1", children)
            ])
        ]);
    }
};

const WindowMockup = {
    view: ({ attrs, children }) => {
        return m(".mockup-window.border.bg-base-300", { ...attrs }, [
            m(".flex.justify-center.bg-base-200.px-4.py-16", children)
        ]);
    }
};

export { BrowserMockup, CodeMockup, PhoneMockup, WindowMockup };
