import m from "mithril";

const Navbar = {
    view: ({ attrs, children }) => {
        return m(".navbar.bg-base-100", { 
            ...attrs, 
            role: "navigation", 
            "aria-label": "main navigation" 
        }, children);
    }
};

export default Navbar;
