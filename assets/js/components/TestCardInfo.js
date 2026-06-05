import m from "mithril";
import { Icon } from "./Icon";

const TestCardInfo = {
    view: (vnode) => {
        const { cards = [] } = vnode.attrs;
        if (cards.length === 0) return null;

        return m(".card.bg-base-200/50.border.border-base-300.mb-10", [
            m(".card-body.p-4.md:p-6", [
                m(".flex.items-center.gap-3.mb-4.text-info", [
                    m(Icon, { icon: "fa-solid fa-circle-info" }),
                    m("h3.font-bold.text-sm.uppercase.tracking-wider", "Test Card Information (Click to copy)")
                ]),
                m(".grid.grid-cols-1.sm:grid-cols-2.md:grid-cols-3.lg:grid-cols-4.gap-3", cards.map(card => {
                    return m("button.flex.items-center.justify-between.gap-3.p-3.bg-base-100.hover:bg-base-300.rounded-xl.transition-all.group.text-left.border.border-base-300/50", {
                        onclick: () => {
                            navigator.clipboard.writeText(card.number.replace(/\s/g, ''));
                            if (window.showToast) {
                                window.showToast(`${card.label} copied!`, "success");
                            }
                        },
                        title: `Copy ${card.label}`
                    }, [
                        m(".flex.flex-col", [
                            m("span.text-[10px].uppercase.font-black.opacity-40", card.label),
                            m("code.text-sm.font-mono", card.number),
                        ]),
                        m(Icon, { icon: "fa-solid fa-copy", class: "opacity-20 group-hover:opacity-100 transition-opacity" })
                    ]);
                }))
            ])
        ]);
    }
};

export default TestCardInfo;
