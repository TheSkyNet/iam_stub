import m from "mithril";
import { Icon } from "../../components/Icon";

const StaffWelcomePage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m(".hero.bg-base-100.rounded-xl.shadow-xl", [
                m(".hero-content.text-center", [
                    m(".max-w-md", [
                        m(Icon, { icon: "fa-solid fa-user-tie", class: "text-6xl text-accent mb-4" }),
                        m("h1.text-3xl.font-bold", "Staff Portal"),
                        m("p.py-6", "Access staff tools and management consoles."),
                        m("button.btn.btn-accent", "Admin Panel")
                    ])
                ])
            ])
        ]);
    }
};

export default StaffWelcomePage;
