import m from "mithril";
import { Icon } from "./Icon";

const Footer = {
    view: () => {
        const currentYear = new Date().getFullYear();
        
        return m("footer.footer.footer-horizontal.footer-center.bg-primary.text-primary-content.p-10", [
            m("aside", [
                m(Icon, { icon: "fa-solid fa-flask", class: "text-5xl mb-2" }),
                m("p.font-bold", [
                    "IamLab Industries Ltd.",
                    m("br"),
                    "Providing reliable tech since 2024"
                ]),
                m("p", `Copyright © ${currentYear} - All right reserved`)
            ]),
            m("nav", [
                m(".grid.grid-flow-col.gap-4", [
                    m("a", { href: "#", class: "link link-hover" }, [
                        m(Icon, { icon: "fa-brands fa-x-twitter", class: "text-2xl" })
                    ]),
                    m("a", { href: "#", class: "link link-hover" }, [
                        m(Icon, { icon: "fa-brands fa-youtube", class: "text-2xl" })
                    ]),
                    m("a", { href: "#", class: "link link-hover" }, [
                        m(Icon, { icon: "fa-brands fa-facebook-f", class: "text-2xl" })
                    ])
                ])
            ])
        ]);
    }
};

export default Footer;
