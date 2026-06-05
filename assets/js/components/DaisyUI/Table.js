import m from "mithril";

const Table = {
    view: ({ attrs, children }) => {
        const { zebra, pinRows, pinCols, ...props } = attrs;
        const classes = [
            "table",
            zebra && "table-zebra",
            pinRows && "table-pin-rows",
            pinCols && "table-pin-cols",
            attrs.class
        ].filter(Boolean).join(" ");

        return m(".overflow-x-auto", [
            m("table", { ...props, class: classes }, children)
        ]);
    }
};

export default Table;
