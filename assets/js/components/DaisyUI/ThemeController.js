import m from "mithril";

const ThemeController = {
    view: ({ attrs }) => {
        const { theme, ...props } = attrs;
        return m("input.theme-controller", { type: "checkbox", value: theme, ...props });
    }
};

export default ThemeController;
