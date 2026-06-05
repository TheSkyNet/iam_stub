import m from "mithril";

export const ListRow = {
    view: ({ attrs, children }) => {
        return m("li.list-row", { ...attrs, role: "listitem" }, children);
    }
};

const List = {
    view: ({ attrs, children }) => {
        return m("ul.list", { ...attrs, role: "list" }, children);
    }
};

export default List;
