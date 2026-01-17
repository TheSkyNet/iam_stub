import m from "mithril";
import { Icon } from "../../components/Icon";

const RolesPage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m(".flex.justify-between.items-center.mb-6", [
                m("h1.text-3xl.font-bold", "Roles Management"),
                m("button.btn.btn-primary", [
                    m(Icon, { name: "fa-solid fa-plus" }),
                    " Add Role"
                ])
            ]),
            m(".overflow-x-auto.bg-base-100.rounded-xl.shadow", [
                m("table.table", [
                    m("thead", [
                        m("tr", [
                            m("th", "ID"),
                            m("th", "Name"),
                            m("th", "Description"),
                            m("th.text-right", "Actions")
                        ])
                    ]),
                    m("tbody", [
                        m("tr", [
                            m("td", "1"),
                            m("td", "Admin"),
                            m("td", "Full system access"),
                            m("td.text-right", [
                                m("button.btn.btn-sm.btn-ghost", m(Icon, { name: "fa-solid fa-pen" })),
                                m("button.btn.btn-sm.btn-ghost.text-error", m(Icon, { name: "fa-solid fa-trash" }))
                            ])
                        ]),
                        m("tr", [
                            m("td", "2"),
                            m("td", "Editor"),
                            m("td", "Can edit content"),
                            m("td.text-right", [
                                m("button.btn.btn-sm.btn-ghost", m(Icon, { name: "fa-solid fa-pen" })),
                                m("button.btn.btn-sm.btn-ghost.text-error", m(Icon, { name: "fa-solid fa-trash" }))
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default RolesPage;
