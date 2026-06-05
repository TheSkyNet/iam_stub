import m from "mithril";

const RadialProgress = {
    view: ({ attrs, children }) => {
        const { value, size, thickness, color, ...props } = attrs;
        const style = {
            "--value": value,
            "--size": size,
            "--thickness": thickness,
        };
        const classes = [
            "radial-progress",
            color && `text-${color}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { 
            role: "progressbar", 
            style, 
            class: classes, 
            "aria-valuenow": value,
            "aria-valuemin": 0,
            "aria-valuemax": 100,
            ...props 
        }, [
            children || `${value}%`
        ]);
    }
};

export default RadialProgress;
