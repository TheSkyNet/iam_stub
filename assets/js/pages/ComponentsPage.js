import m from "mithril";
import { Icon } from "../components/Icon";

// DaisyUI Reusable Components
import Button from "../components/DaisyUI/Button";
import Dropdown from "../components/DaisyUI/Dropdown";
import Modal from "../components/DaisyUI/Modal";
import Swap from "../components/DaisyUI/Swap";
import ThemeController from "../components/DaisyUI/ThemeController";
import Badge from "../components/DaisyUI/Badge";
import Alert from "../components/DaisyUI/Alert";
import Avatar from "../components/DaisyUI/Avatar";
import Card from "../components/DaisyUI/Card";
import { Stat, Stats } from "../components/DaisyUI/Stat";
import { Step, Steps } from "../components/DaisyUI/Steps";
import { Tabs, Tab } from "../components/DaisyUI/Tab";
import { Progress, RadialProgress } from "../components/DaisyUI/Progress";
import Loading from "../components/DaisyUI/Loading";
import TextInput from "../components/DaisyUI/TextInput";
import Checkbox from "../components/DaisyUI/Checkbox";
import Radio from "../components/DaisyUI/Radio";
import Toggle from "../components/DaisyUI/Toggle";
import Range from "../components/DaisyUI/Range";
import Rating from "../components/DaisyUI/Rating";
import Select from "../components/DaisyUI/Select";
import Textarea from "../components/DaisyUI/Textarea";
import Hero from "../components/DaisyUI/Hero";
import { BrowserMockup, CodeMockup, PhoneMockup, WindowMockup } from "../components/DaisyUI/Mockup";

// New Components
import { Accordion, Collapse, Countdown, Kbd, Status, List, ListRow, Timeline, TimelineItem, Diff } from "../components/DaisyUI/DataDisplay";
import { Breadcrumbs, Dock, DockItem, Link, Menu, Pagination } from "../components/DaisyUI/Navigation";
import { Toast, Tooltip } from "../components/DaisyUI/Feedback";
import { Calendar, Fieldset, Label, Validator } from "../components/DaisyUI/DataInput";
import Chat from "../components/DaisyUI/Chat";
import Carousel from "../components/DaisyUI/Carousel";

const ComponentsPage = {
    view: () => {
        return m(".container.mx-auto.p-4.space-y-16", [
            m("header.mb-12.text-center", [
                m("h1.text-5xl.font-black.mb-4", "DaisyUI 5 Component Gallery"),
                m("p.text-xl.opacity-60", "Comprehensive showcase of all DaisyUI components, categorized and working.")
            ]),

            // 1. ACTIONS
            m("section", [
                m("h2.text-3xl.font-bold.mb-8.border-b.pb-2", "Actions"),
                m(".grid.grid-cols-1.gap-12", { class: "md:grid-cols-2" }, [
                    // Buttons
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Buttons (with v5 soft/dash)"),
                        m(".flex.flex-wrap.gap-2", [
                            m(Button, { color: "primary" }, "Primary"),
                            m(Button, { color: "secondary", soft: true }, "Soft Secondary"),
                            m(Button, { color: "accent", dash: true }, "Dash Accent"),
                            m(Button, { outline: true }, "Outline"),
                            m(Button, { ghost: true }, "Ghost"),
                            m(Button, { loading: true }, "Loading"),
                        ])
                    ]),
                    // Dropdown
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Dropdown"),
                        m(Dropdown, { label: "Options", hover: true }, [
                            m("li", m("a", "Edit")),
                            m("li", m("a", "Delete")),
                        ])
                    ]),
                    // FAB / Speed Dial
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "FAB / Speed Dial"),
                        m(".relative.h-20", [
                            m(".absolute.bottom-0.left-0", [
                                m(Button, { circle: true, color: "primary", class: "shadow-lg" }, m(Icon, { icon: "fa-solid fa-plus" }))
                            ])
                        ])
                    ]),
                    // Modal
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Modal"),
                        m(Button, { onclick: () => document.getElementById('my_modal').showModal() }, "Open Modal"),
                        m(Modal, { id: "my_modal" }, [
                            m("h3.font-bold.text-lg", "Hello!"),
                            m("p.py-4", "This modal is working out of the box.")
                        ])
                    ]),
                    // Swap & Theme Controller
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Swap & Theme Controller"),
                        m(".flex.items-center.gap-4", [
                            m(Swap, { 
                                effect: "rotate", 
                                on: m(Icon, { icon: "fa-solid fa-sun", class: "text-2xl" }),
                                off: m(Icon, { icon: "fa-solid fa-moon", class: "text-2xl" })
                            }),
                            m(ThemeController, { theme: "dark", class: "toggle" })
                        ])
                    ])
                ])
            ]),

            // 2. DATA DISPLAY
            m("section", [
                m("h2.text-3xl.font-bold.mb-8.border-b.pb-2", "Data Display"),
                m(".grid.grid-cols-1.gap-12", { class: "md:grid-cols-2" }, [
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Accordion & Collapse"),
                        m(Accordion, { name: "acc1", items: [
                            { title: "Item 1", content: "Content 1", active: true },
                            { title: "Item 2", content: "Content 2" }
                        ]})
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Avatar & Badge"),
                        m(".flex.items-center.gap-4", [
                            m(Avatar, { src: "https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp", online: true }),
                            m(Badge, { color: "primary" }, "New"),
                            m(Badge, { color: "secondary", outline: true }, "Featured")
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Card"),
                        m(Card, { title: "DaisyUI 5", image: "https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" }, "Card component is updated.")
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Carousel"),
                        m(Carousel, { class: "rounded-box w-64" }, [
                            m("img", { src: "https://img.daisyui.com/images/stock/photo-1559703248-dcaaec9fab78.webp" }),
                            m("img", { src: "https://img.daisyui.com/images/stock/photo-1565098772267-60af42b81ef2.webp" })
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Chat Bubble"),
                        m(Chat, { header: "Obi-Wan", footer: "Delivered", image: "https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" }, "It's over Anakin!")
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Countdown & Kbd"),
                        m(".flex.items-center.gap-4", [
                            m(Countdown, { value: 45, class: "text-4xl font-mono" }),
                            m("div", [
                                m(Kbd, "ctrl"),
                                " + ",
                                m(Kbd, "c")
                            ])
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Diff"),
                        m(Diff, { 
                            class: "aspect-[16/9] w-full max-w-xs",
                            item1: m(".bg-primary.text-primary-content.grid.place-items-center.text-5xl.font-black", "BEFORE"),
                            item2: m(".bg-secondary.text-secondary-content.grid.place-items-center.text-5xl.font-black", "AFTER")
                        })
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "List (New in v5) & Status"),
                        m(List, { class: "bg-base-200 rounded-box p-2" }, [
                            m(ListRow, [
                                m(Status, { color: "success" }),
                                m("div", "User Online")
                            ]),
                            m(ListRow, [
                                m(Status, { color: "error", size: "lg" }),
                                m("div", "System Alert")
                            ])
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Stats"),
                        m(Stats, [
                            m(Stat, { label: "Downloads", value: "31K", desc: "Jan 1st - Feb 1st" }),
                            m(Stat, { label: "New Users", value: "4,200", desc: "400 (22%)" })
                        ])
                    ]),
                    m(".space-y-4.col-span-full", [
                        m("h3.text-xl.font-semibold", "Timeline"),
                        m(Timeline, { horizontal: true }, [
                            m(TimelineItem, { start: "1984", middle: m(Icon, { icon: "fa-solid fa-circle-check", class: "text-primary" }), end: "Mac", connect: "end" }),
                            m(TimelineItem, { start: "1998", middle: m(Icon, { icon: "fa-solid fa-circle-check", class: "text-primary" }), end: "iMac", connect: "both" }),
                            m(TimelineItem, { start: "2024", middle: m(Icon, { icon: "fa-solid fa-circle-check" }), end: "Vision Pro", connect: "start" })
                        ])
                    ])
                ])
            ]),

            // 3. NAVIGATION
            m("section", [
                m("h2.text-3xl.font-bold.mb-8.border-b.pb-2", "Navigation"),
                m(".grid.grid-cols-1.gap-12", { class: "md:grid-cols-2" }, [
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Breadcrumbs & Link"),
                        m(Breadcrumbs, [
                            m("li", m(Link, { href: "#" }, "Home")),
                            m("li", m(Link, { href: "#", color: "primary" }, "Components")),
                            m("li", "Navigation")
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Dock (New in v5)"),
                        m(Dock, { class: "relative" }, [
                            m(DockItem, { active: true }, [m(Icon, { icon: "fa-solid fa-house" }), m("span", "Home")]),
                            m(DockItem, [m(Icon, { icon: "fa-solid fa-user" }), m("span", "Profile")])
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Menu"),
                        m(Menu, [
                            m("li", m("a", "Item 1")),
                            m("li", m("a", "Item 2")),
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Pagination & Steps"),
                        m(".space-y-4", [
                            m(Pagination, { total: 5, current: 2 }),
                            m(Steps, { horizontal: true }, [
                                m(Step, { color: "primary" }, "Start"),
                                m(Step, { color: "primary" }, "Mid"),
                                m(Step, "End")
                            ])
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Tabs"),
                        m(Tabs, { variant: "boxed" }, [
                            m(Tab, { active: true }, "Active"),
                            m(Tab, "Inactive")
                        ])
                    ])
                ])
            ]),

            // 4. FEEDBACK
            m("section", [
                m("h2.text-3xl.font-bold.mb-8.border-b.pb-2", "Feedback"),
                m(".grid.grid-cols-1.gap-12", { class: "md:grid-cols-2" }, [
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Alert"),
                        m(Alert, { type: "info" }, "New updates available!")
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Loading & Progress"),
                        m(".flex.items-center.gap-4", [
                            m(Loading, { variant: "spinner" }),
                            m(Loading, { variant: "dots", color: "primary" }),
                            m(Progress, { value: 70, max: 100, class: "w-56" }),
                            m(RadialProgress, { value: 80 })
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Skeleton & Tooltip"),
                        m(".flex.items-center.gap-4", [
                            m(".skeleton.w-12.h-12.rounded-full"),
                            m(Tooltip, { text: "Useful hint", position: "top" }, m(Button, "Hover me"))
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Toast"),
                        m(".relative.h-20", [
                            m(Toast, { class: "absolute" }, [
                                m(Alert, { type: "success" }, "Message sent!")
                            ])
                        ])
                    ])
                ])
            ]),

            // 5. DATA INPUT
            m("section", [
                m("h2.text-3xl.font-bold.mb-8.border-b.pb-2", "Data Input"),
                m(".grid.grid-cols-1.gap-12", { class: "md:grid-cols-2" }, [
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Fieldset (v5) & Legend"),
                        m(Fieldset, { legend: "User Info" }, [
                            m(Label, "Name"),
                            m(TextInput, { placeholder: "John Doe" })
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Validator (v5)"),
                        m(Validator, { hint: "Must be a valid email" }, [
                            m(TextInput, { type: "email", placeholder: "email@example.com" })
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Selections"),
                        m(".grid.grid-cols-2.gap-4", [
                            m(".flex.items-center.gap-2", [m(Checkbox, { checked: true }), m("span", "Checkbox")]),
                            m(".flex.items-center.gap-2", [m(Radio, { checked: true }), m("span", "Radio")]),
                            m(Toggle, { checked: true }),
                            m(Rating, { value: 4 })
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Range & Select"),
                        m(".space-y-4", [
                            m(Range, { min: 0, max: 100, value: 50 }),
                            m(Select, [
                                m("option", "Option 1"),
                                m("option", "Option 2")
                            ])
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Textarea & File Input"),
                        m(".space-y-4", [
                            m(Textarea, { placeholder: "Tell us more..." }),
                            m("input.file-input.w-full", { type: "file" })
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Calendar (v5)"),
                        m(Calendar, { class: "bg-base-200 p-4 rounded-xl" }, "Calendar placeholder")
                    ])
                ])
            ]),

            // 6. LAYOUT
            m("section", [
                m("h2.text-3xl.font-bold.mb-8.border-b.pb-2", "Layout"),
                m(".grid.grid-cols-1.gap-12", { class: "md:grid-cols-2" }, [
                    m(".space-y-4.col-span-full", [
                        m("h3.text-xl.font-semibold", "Hero"),
                        m(Hero, { class: "bg-base-200 rounded-xl" }, [
                            m(".hero-content.text-center", [
                                m(".max-w-md", [
                                    m("h1.text-5xl.font-bold", "Hello there"),
                                    m("p.py-6", "Hero component is ready."),
                                    m(Button, { color: "primary" }, "Get Started")
                                ])
                            ])
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Divider & Indicator"),
                        m(".flex.flex-col.w-full", [
                            m("div.grid.h-20.card.bg-base-300.rounded-box.place-items-center", "Content"),
                            m(".divider", "OR"),
                            m(".indicator", [
                                m("span.indicator-item.badge.badge-secondary", "8"),
                                m("div.grid.h-20.w-20.card.bg-base-300.rounded-box.place-items-center", "Inbox")
                            ])
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Join, Stack & Mask"),
                        m(".flex.flex-col.gap-4", [
                            m(".join", [
                                m(Button, { class: "join-item" }, "L"),
                                m(Button, { class: "join-item" }, "M"),
                                m(Button, { class: "join-item" }, "R")
                            ]),
                            m(".stack", [
                                m(".bg-primary.w-20.h-20.rounded"),
                                m(".bg-secondary.w-20.h-20.rounded"),
                                m(".bg-accent.w-20.h-20.rounded")
                            ]),
                            m("img.mask.mask-heart.w-20", { src: "https://img.daisyui.com/images/stock/photo-1567653418876-5bb0e566e1c2.webp" })
                        ])
                    ]),
                    m(".space-y-4", [
                        m("h3.text-xl.font-semibold", "Drawer"),
                        m(".h-40.border.rounded-xl.overflow-hidden", [
                            m(".drawer", [
                                m("input.drawer-toggle", { id: "my-drawer", type: "checkbox" }),
                                m(".drawer-content.p-4", [
                                    m("label.btn.btn-primary.drawer-button", { for: "my-drawer" }, "Open Drawer")
                                ]),
                                m(".drawer-side", [
                                    m("label.drawer-overlay", { for: "my-drawer" }),
                                    m("ul.menu.p-4.w-80.min-h-full.bg-base-200", [
                                        m("li", m("a", "Sidebar Item"))
                                    ])
                                ])
                            ])
                        ])
                    ])
                ])
            ]),

            // 7. MOCKUP
            m("section", [
                m("h2.text-3xl.font-bold.mb-8.border-b.pb-2", "Mockup"),
                m(".grid.grid-cols-1.gap-12", [
                    m(BrowserMockup, { url: "daisyui.com" }, "Browser mockup content"),
                    m(".grid.grid-cols-1.gap-8", { class: "md:grid-cols-2" }, [
                        m(CodeMockup, [
                            m("pre", { "data-prefix": "$" }, m("code", "npm i daisyui"))
                        ]),
                        m(WindowMockup, "Hello World")
                    ]),
                    m(PhoneMockup, "Calling...")
                ])
            ]),
        ]);
    },
};

export default ComponentsPage;
