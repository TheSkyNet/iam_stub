import m from "mithril";

const Pagination = {
    view: ({ attrs }) => {
        const { total, current, onPageChange, ...props } = attrs;
        return m(".join", { 
            ...props, 
            role: "navigation", 
            "aria-label": "Pagination" 
        }, 
            Array.from({ length: total }).map((_, i) => 
                m("button.join-item.btn", {
                    class: current === i + 1 ? "btn-active" : "",
                    onclick: () => onPageChange && onPageChange(i + 1),
                    "aria-label": `Go to page ${i + 1}`,
                    "aria-current": current === i + 1 ? "page" : undefined
                }, i + 1)
            )
        );
    }
};

export default Pagination;
