import m from "mithril";
import { Icon } from "../../components/Icon";

const MemberWelcomePage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m(".hero.bg-base-100.rounded-xl.shadow-xl", [
                m(".hero-content.text-center", [
                    m(".max-w-md", [
                        m(Icon, { icon: "fa-solid fa-user-graduate", class: "text-6xl text-secondary mb-4" }),
                        m("h1.text-3xl.font-bold", "Member Area"),
                        m("p.py-6", "Welcome back! Access your courses and community resources here."),
                        m("button.btn.btn-secondary", "View My Courses")
                    ])
                ])
            ])
        ]);
    }
};

export default MemberWelcomePage;
