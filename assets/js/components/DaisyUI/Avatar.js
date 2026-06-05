import m from "mithril";

const Avatar = {
    view: ({ attrs }) => {
        const { src, alt, size, shape, placeholder, initials, online, offline, ...props } = attrs;
        const classes = [
            "avatar",
            online && "online",
            offline && "offline",
            placeholder && "placeholder",
            attrs.class
        ].filter(Boolean).join(" ");

        const innerClasses = [
            size ? (typeof size === 'number' ? `w-${size}` : size) : "w-12",
            shape && `mask mask-${shape}`,
            !shape && "rounded-full",
            placeholder && "bg-neutral text-neutral-content"
        ].filter(Boolean).join(" ");

        return m("div", { ...props, class: classes }, [
            m("div", { class: innerClasses }, [
                placeholder ? m("span", initials) : m("img", { src, alt })
            ])
        ]);
    }
};

export default Avatar;
