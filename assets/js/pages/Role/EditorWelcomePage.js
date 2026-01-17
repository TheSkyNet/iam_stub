import m from "mithril";
import { Icon } from "../../components/Icon";

const EditorWelcomePage = {
    view: () => {
        return m(".container.mx-auto.p-4", [
            m(".hero.bg-base-100.rounded-xl.shadow-xl", [
                m(".hero-content.text-center", [
                    m(".max-w-md", [
                        m(Icon, { icon: "fa-solid fa-pen-nib", class: "text-6xl text-primary mb-4" }),
                        m("h1.text-3xl.font-bold", "Editor Dashboard"),
                        m("p.py-6", "Welcome back! You have permissions to create and edit content."),
                        m("button.btn.btn-primary", "Start Writing")
                    ])
                ])
            ])
        ]);
    }
};

export default EditorWelcomePage;
