import m from "mithril";

const Chat = {
    view: ({ attrs, children }) => {
        const { position = "start", image, header, footer, bubbleClass, ...props } = attrs;
        const classes = [
            "chat",
            `chat-${position}`,
            attrs.class
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes }, [
            image && m(".chat-image.avatar", m(".w-10.rounded-full", m("img", { src: image }))),
            header && m(".chat-header", header),
            m(".chat-bubble", { class: bubbleClass }, children),
            footer && m(".chat-footer.opacity-50", footer)
        ]);
    }
};

export default Chat;
