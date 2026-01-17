import m from "mithril";
import { Icon } from "../components/Icon";

const WelcomePage = {
    view: () => {
        return m(".hero.min-h-screen.bg-base-200", [
           m(".hero-content.text-center", [
               m(".max-w-md", [
                   m("h1.text-5xl.font-bold", "Welcome to IamLab"),
                   m("p.py-6", "Your Phalcon-based Laboratory for Identity and Access Management."),
                   m(m.route.Link, { href: "/login", class: "btn btn-primary" }, [
                    m(Icon, { icon: "fa-solid fa-right-to-bracket" }),
                       " Get Started"
                   ]),
                   m(m.route.Link, { href: "/components", class: "btn btn-outline ml-2" }, [
                    m(Icon, { icon: "fa-solid fa-cubes" }),
                       " Components"
                   ])
               ])
           ])
        ]);
    }
};

export default WelcomePage;
