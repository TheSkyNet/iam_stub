import m from "mithril";

const Countdown = {
    view: ({ attrs }) => {
        const { value, ...props } = attrs;
        return m("span.countdown", { 
            ...props, 
            role: "timer", 
            "aria-live": "polite" 
        }, 
            m("span", { style: `--value:${value}` })
        );
    }
};

export default Countdown;
