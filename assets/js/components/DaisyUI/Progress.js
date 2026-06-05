import m from "mithril";

const Progress = {
    view: ({ attrs }) => {
        const { color, value, max, ...props } = attrs;
        const classes = [
            "progress",
            color && `progress-${color}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("progress", { ...props, class: classes, value, max });
    }
};

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

        return m("div", { role: "progressbar", style, class: classes, ...props }, [
            children || `${value}%`
        ]);
    }
};

export { Progress, RadialProgress };
