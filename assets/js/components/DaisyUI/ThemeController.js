import m from "mithril";

const ThemeController = {
    view: ({ attrs }) => {
        const { theme, ...props } = attrs;
        return m("input.theme-controller", { 
            type: "checkbox", 
            value: theme, 
            "aria-label": `Toggle ${theme} theme`,
            ...props 
        });
    }
};

export default ThemeController;
