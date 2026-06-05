import m from "mithril";

/**
 * FAB (Floating Action Button) / Speed Dial Component
 * 
 * Supports simple FAB, Speed Dial (vertical list), and Flower (radial) modes.
 * Uses DaisyUI 5 .fab, .fab-flower classes.
 * 
 * @param {Object} attrs
 * @param {string} [attrs.color="primary"] - Button color (for simple FAB)
 * @param {string} [attrs.size] - Button size (xs, sm, md, lg)
 * @param {boolean} [attrs.speedDial] - Enable Speed Dial mode
 * @param {boolean} [attrs.flower] - Enable Flower (radial) mode
 * @param {string} [attrs.class] - Additional classes
 */
const FAB = {
    view: ({ attrs, children }) => {
        const { color = "primary", size, speedDial, flower, ...props } = attrs;

        if (speedDial || flower) {
            const classes = [
                "fab",
                flower && "fab-flower",
                attrs.class
            ].filter(Boolean).join(" ");

            const childrenArray = Array.isArray(children) ? children : [children];
            const [trigger, ...actions] = childrenArray;

            return m("div", { ...props, class: classes }, [
                // The first child with tabindex=0 is the toggle trigger
                m("div", { 
                    tabindex: 0, 
                    role: "button",
                    class: "cursor-pointer"
                }, trigger),
                // Subsequent children are shown on focus-within
                ...actions
            ]);
        }

        // Default simple FAB button
        const classes = [
            "btn btn-circle shadow-lg",
            color && `btn-${color}`,
            size && `btn-${size}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("button", { 
            ...props, 
            class: classes,
            "aria-label": attrs["aria-label"] || "Floating action button"
        }, children);
    }
};

export default FAB;
