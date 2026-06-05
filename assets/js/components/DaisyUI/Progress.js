import m from "mithril";

const Progress = {
    view: ({ attrs }) => {
        const { color, value, max, ...props } = attrs;
        const classes = [
            "progress",
            color && `progress-${color}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("progress", { 
            ...props, 
            class: classes, 
            value, 
            max,
            "aria-valuenow": value,
            "aria-valuemin": 0,
            "aria-valuemax": max
        });
    }
};

export default Progress;
