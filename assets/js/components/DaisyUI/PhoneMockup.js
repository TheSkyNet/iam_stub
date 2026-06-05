import m from "mithril";

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

export default PhoneMockup;
