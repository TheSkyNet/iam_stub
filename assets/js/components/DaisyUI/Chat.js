import m from "mithril";

const Chat = {
    view: ({ attrs, children }) => {
        const { position = "start", color, image, header, footer, bubbleClass, ...props } = attrs;
        const classes = [
            "chat",
            `chat-${position}`,
            attrs.class
        ].filter(Boolean).join(" ");

        const bubbleClasses = [
            "chat-bubble",
            color && `chat-bubble-${color}`,
            bubbleClass
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes }, [
            image && (typeof image === 'string' ? 
                m(".chat-image.avatar", m(".w-10.rounded-full", m("img", { src: image }))) : 
                m(".chat-image", image)),
            header && m(".chat-header", header),
            m("div", { class: bubbleClasses }, children),
            footer && m(".chat-footer.opacity-50", footer)
        ]);
    }
};

export default Chat;
