import m from "mithril";
import { Icon } from "./Icon";
const Welcome = {
    view: function() {
        return m(".min-h-screen.flex.items-center.justify-center.p-8", [
            m(".container.mx-auto.max-w-4xl", [
                m(".text-center", [
                    m("h1.text-5xl.font-bold.text-base-content.mb-6", "Welcome to Your Phalcon Stub Project"),
                    m(".mt-12", [
                        m("h3.text-3xl.font-bold.text-base-content.mb-4", "Ready to Build Something Amazing?"),
                        m("p.text-base-content.opacity-70", "Start customizing this stub for your next Phalcon or Mithril.js project!")
                    ])
                ])
            ])
        ]);
    }
};

export {Welcome};
