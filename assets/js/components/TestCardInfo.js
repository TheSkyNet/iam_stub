import m from "mithril";
import { Icon } from "./Icon";

const TestCardInfo = {
    view: (vnode) => {
        const { cards = [] } = vnode.attrs;
        if (cards.length === 0) return null;

        return m(".alert.alert-info.shadow-lg.mb-8.bg-info.text-info-content", [
            m(Icon, { icon: "fa-solid fa-circle-info" }),
            m("div.flex.flex-col.w-full", [
                m("h3.font-bold", "Test Card Information (Click to copy)"),
                m(".flex.flex-wrap.gap-4.mt-2", cards.map(card => {
                    return m(".badge.badge-outline.p-4.h-auto.flex.items-center.gap-2.bg-base-100.text-base-content.border-none", [
                        m("div.flex.flex-col.items-start", [
                            m("span.text-xs.uppercase.font-bold.opacity-60", card.label),
                            m("code.text-sm", card.number),
                        ]),
                        m("button.btn.btn-ghost.btn-xs.p-1", {
                            onclick: () => {
                                navigator.clipboard.writeText(card.number.replace(/\s/g, ''));
                                window.showToast(`${card.label} copied!`, "success");
                            },
                            title: `Copy ${card.label}`
                        }, m(Icon, { icon: "fa-solid fa-copy" }))
                    ]);
                }))
            ])
        ]);
    }
};

export default TestCardInfo;
