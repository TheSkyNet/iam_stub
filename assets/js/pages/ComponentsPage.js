import m from "mithril";
import { Icon } from "../components/Icon";

// Actions
import Button from "../components/DaisyUI/Button";
import Dropdown from "../components/DaisyUI/Dropdown";
import FAB from "../components/DaisyUI/FAB";
import Modal from "../components/DaisyUI/Modal";
import Swap from "../components/DaisyUI/Swap";
import ThemeController from "../components/DaisyUI/ThemeController";

// Data Display
import Accordion from "../components/DaisyUI/Accordion";
import Avatar from "../components/DaisyUI/Avatar";
import Badge from "../components/DaisyUI/Badge";
import Card from "../components/DaisyUI/Card";
import Carousel from "../components/DaisyUI/Carousel";
import Chat from "../components/DaisyUI/Chat";
import Collapse from "../components/DaisyUI/Collapse";
import Countdown from "../components/DaisyUI/Countdown";
import Diff from "../components/DaisyUI/Diff";
import Hover3DCard from "../components/DaisyUI/Hover3DCard";
import HoverGallery from "../components/DaisyUI/HoverGallery";
import Kbd from "../components/DaisyUI/Kbd";
import List, { ListRow } from "../components/DaisyUI/List";
import Stat from "../components/DaisyUI/Stat";
import Stats from "../components/DaisyUI/Stats";
import Status from "../components/DaisyUI/Status";
import Table from "../components/DaisyUI/Table";
import TextRotate from "../components/DaisyUI/TextRotate";
import Timeline, { TimelineItem } from "../components/DaisyUI/Timeline";

// Navigation
import Breadcrumbs from "../components/DaisyUI/Breadcrumbs";
import Dock, { DockItem } from "../components/DaisyUI/Dock";
import Link from "../components/DaisyUI/Link";
import Menu from "../components/DaisyUI/Menu";
import Navbar from "../components/DaisyUI/Navbar";
import Pagination from "../components/DaisyUI/Pagination";
import Step from "../components/DaisyUI/Step";
import Steps from "../components/DaisyUI/Steps";
import Tab from "../components/DaisyUI/Tab";
import Tabs from "../components/DaisyUI/Tabs";

// Feedback
import Alert from "../components/DaisyUI/Alert";
import Loading from "../components/DaisyUI/Loading";
import Progress from "../components/DaisyUI/Progress";
import RadialProgress from "../components/DaisyUI/RadialProgress";
import Skeleton from "../components/DaisyUI/Skeleton";
import Toast from "../components/DaisyUI/Toast";
import Tooltip from "../components/DaisyUI/Tooltip";

// Data Input
import Calendar from "../components/DaisyUI/Calendar";
import Checkbox from "../components/DaisyUI/Checkbox";
import Fieldset from "../components/DaisyUI/Fieldset";
import FileInput from "../components/DaisyUI/FileInput";
import Filter from "../components/DaisyUI/Filter";
import Label from "../components/DaisyUI/Label";
import Radio from "../components/DaisyUI/Radio";
import Range from "../components/DaisyUI/Range";
import Rating from "../components/DaisyUI/Rating";
import Select from "../components/DaisyUI/Select";
import TextInput from "../components/DaisyUI/TextInput";
import Textarea from "../components/DaisyUI/Textarea";
import Toggle from "../components/DaisyUI/Toggle";
import Validator from "../components/DaisyUI/Validator";

// Layout
import Divider from "../components/DaisyUI/Divider";
import Drawer from "../components/DaisyUI/Drawer";
import Footer from "../components/DaisyUI/Footer";
import Hero from "../components/DaisyUI/Hero";
import Indicator from "../components/DaisyUI/Indicator";
import Join from "../components/DaisyUI/Join";
import Mask from "../components/DaisyUI/Mask";
import Stack from "../components/DaisyUI/Stack";

// Mockup
import BrowserMockup from "../components/DaisyUI/BrowserMockup";
import CodeMockup from "../components/DaisyUI/CodeMockup";
import PhoneMockup from "../components/DaisyUI/PhoneMockup";
import WindowMockup from "../components/DaisyUI/WindowMockup";

const Section = {
    view: ({ attrs, children }) => m("section.space-y-6", [
        m("div.flex.items-center.gap-4", [
            m("h2.text-3xl.font-black.tracking-tight", attrs.title),
            m("div.h-px.flex-1.bg-base-300")
        ]),
        m(".grid.grid-cols-1.gap-6.md:grid-cols-2.xl:grid-cols-3", children)
    ])
};

const CardWrapper = {
    view: ({ attrs, children }) => m(".card.bg-base-200/50.border.border-base-300", [
        m(".card-body.p-6", [
            m("h3.card-title.text-sm.uppercase.tracking-widest.opacity-50.mb-6", attrs.title),
            m(".flex.flex-wrap.gap-4.items-start", children)
        ])
    ])
};

const ComponentsPage = {
    view: () => {

        return m(".container.mx-auto.p-6.md:p-10.max-w-7xl.space-y-16", [
            m("header.py-8.md:py-12.text-center.space-y-4", [
                m("h1.text-4xl.md:text-6xl.font-black.bg-clip-text.text-transparent.bg-gradient-to-r.from-primary.to-secondary", "Mithril.js + DaisyUI 5"),
                m("p.text-xl.opacity-60.max-w-2xl.mx-auto", "ARIA-Compliant, single-file components for rapid development.")
            ]),

            // ACTIONS
            m(Section, { title: "Actions" }, [
                m(CardWrapper, { title: "Buttons" }, [
                    m(Button, { color: "primary" }, "Primary"),
                    m(Button, { color: "secondary", soft: true }, "Soft"),
                    m(Button, { color: "accent", dash: true }, "Dash"),
                    m(Button, { ghost: true }, "Ghost"),
                    m(Button, { loading: true, color: "primary" })
                ]),
                m(CardWrapper, { title: "Dropdown" }, [
                    m(Dropdown, { label: "Hover Menu", hover: true }, [
                        m("li", m("a", "Item 1")),
                        m("li", m("a", "Item 2"))
                    ])
                ]),
                m(CardWrapper, { title: "FAB / Speed Dial", class: "md:col-span-2" }, [
                    m(".relative.w-full.h-64.bg-base-300/30.rounded-xl.overflow-hidden", [
                        m(".p-4.text-xs.opacity-50", "Click icons to toggle Speed Dial / Flower menus"),
                        
                        // Simple FAB
                        m(FAB, { class: "absolute left-4 top-12", color: "secondary" }, m(Icon, { icon: "fa-solid fa-heart" })),
                        
                        // Speed Dial (Vertical)
                        m(FAB, { speedDial: true, class: "absolute left-4 bottom-4" }, [
                            m(Button, { circle: true, color: "primary" }, m(Icon, { icon: "fa-solid fa-plus" })),
                            m(Button, { circle: true, color: "primary", class: "fab-close" }, m(Icon, { icon: "fa-solid fa-xmark" })),
                            m(Button, { circle: true, color: "accent", size: "sm" }, m(Icon, { icon: "fa-solid fa-share" })),
                            m(Button, { circle: true, color: "secondary", size: "sm" }, m(Icon, { icon: "fa-solid fa-envelope" })),
                        ]),

                        // Flower Menu (Radial)
                        m(FAB, { flower: true, class: "absolute right-4 bottom-4" }, [
                            m(Button, { circle: true, color: "primary" }, m(Icon, { icon: "fa-solid fa-gear" })),
                            m(Button, { circle: true, color: "primary", class: "fab-close" }, m(Icon, { icon: "fa-solid fa-xmark" })),
                            m(Button, { circle: true, color: "info" }, m(Icon, { icon: "fa-solid fa-camera" })),
                            m(Button, { circle: true, color: "success" }, m(Icon, { icon: "fa-solid fa-print" })),
                            m(Button, { circle: true, color: "warning" }, m(Icon, { icon: "fa-solid fa-trash" })),
                        ])
                    ])
                ]),
                m(CardWrapper, { title: "Modal" }, [
                    m(Button, { onclick: () => document.getElementById('comp_modal').showModal() }, "Open Modal")
                ]),
                m(CardWrapper, { title: "Swap" }, [
                    m(Swap, { 
                        effect: "flip", 
                        on: m(Icon, { icon: "fa-solid fa-sun", class: "text-3xl text-yellow-500" }),
                        off: m(Icon, { icon: "fa-solid fa-moon", class: "text-3xl" })
                    })
                ]),
                m(CardWrapper, { title: "Theme Controller" }, [
                    m(".flex.items-center.gap-2", [
                        m("span", "Dark Mode"),
                        m(ThemeController, { theme: "dark", class: "toggle" })
                    ])
                ])
            ]),

            // DATA DISPLAY
            m(Section, { title: "Data Display" }, [
                m(CardWrapper, { title: "Accordion" }, [
                    m(Accordion, { name: "gallery-acc", items: [
                        { title: "Review", content: "Great components!", active: true },
                        { title: "Details", content: "Built with Mithril.js" }
                    ]})
                ]),
                m(CardWrapper, { title: "Avatar & Badge" }, [
                    m(Avatar, { src: "https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp", online: true }),
                    m(Badge, { color: "secondary" }, "Active")
                ]),
                m(CardWrapper, { title: "Card" }, [
                    m(Card, { title: "v5 Card", class: "w-full shadow-lg" }, "Improved shadow and padding.")
                ]),
                m(CardWrapper, { title: "Carousel" }, [
                    m(Carousel, { class: "rounded-box w-full" }, [
                        m("img", { src: "https://img.daisyui.com/images/stock/photo-1559703248-dcaaec9fab78.webp" })
                    ])
                ]),
                m(CardWrapper, { title: "Chat Bubble", class: "md:col-span-2 xl:col-span-2" }, [
                    m(".w-full.space-y-4", [
                        m(Chat, { 
                            image: "https://img.daisyui.com/images/profile/demo/kenobee@192.webp",
                            header: [
                                "Obi-Wan Kenobi ",
                                m("time.text-xs.opacity-50", "12:45")
                            ],
                            footer: "Delivered",
                            position: "start"
                        }, "You were the Chosen One!"),
                        m(Chat, { 
                            image: "https://img.daisyui.com/images/profile/demo/anakeen@192.webp",
                            header: [
                                "Anakin ",
                                m("time.text-xs.opacity-50", "12:46")
                            ],
                            footer: "Seen at 12:46",
                            position: "end",
                            color: "neutral"
                        }, "I hate you!")
                    ])
                ]),
                m(CardWrapper, { title: "Collapse" }, [
                    m(Collapse, { title: "Click me", arrow: true, class: "bg-base-100" }, "Hidden content")
                ]),
                m(CardWrapper, { title: "Countdown & Kbd" }, [
                    m(Countdown, { value: 10, class: "text-4xl" }),
                    m(Kbd, "⌘K")
                ]),
                m(CardWrapper, { title: "Diff" }, [
                    m(Diff, { 
                        class: "aspect-square w-full rounded-xl",
                        item1: m(".bg-primary.grid.place-items-center.text-white", "A"),
                        item2: m(".bg-secondary.grid.place-items-center.text-white", "B")
                    })
                ]),
                m(CardWrapper, { title: "Hover 3D Card" }, [
                    m(Hover3DCard, { class: "p-6 w-full text-center" }, "3D Hover Effect")
                ]),
                m(CardWrapper, { title: "Hover Gallery" }, [
                    m(HoverGallery, { items: [{ src: "https://img.daisyui.com/images/stock/photo-1559703248-dcaaec9fab78.webp" }] })
                ]),
                m(CardWrapper, { title: "List (v5)" }, [
                    m(List, { class: "w-full" }, [
                        m(ListRow, [m(Status, { color: "success" }), m("span", "Online")]),
                        m(ListRow, [m(Status, { color: "error" }), m("span", "Offline")])
                    ])
                ]),
                m(CardWrapper, { title: "Stats" }, [
                    m(Stats, { class: "stats-vertical lg:stats-horizontal w-full" }, [
                        m(Stat, { label: "Users", value: "2.5K" })
                    ])
                ]),
                m(CardWrapper, { title: "Table" }, [
                    m(Table, { zebra: true, class: "w-full" }, [
                        m("thead", m("tr", [m("th", "Name"), m("th", "Role")])),
                        m("tbody", m("tr", [m("td", "Morton"), m("td", "Dev")]))
                    ])
                ]),
                m(CardWrapper, { title: "Text Rotate" }, [
                    m("div.text-xl.font-bold", [
                        "I am ",
                        m(TextRotate, { items: ["Fast", "Reliable", "Modern"] })
                    ])
                ]),
                m(CardWrapper, { title: "Timeline", class: "col-span-full" }, [
                    m(Timeline, { horizontal: true, class: "w-full overflow-x-auto" }, [
                        m(TimelineItem, { start: "2020", middle: m(Icon, { icon: "fa-solid fa-check" }), connect: "end" }),
                        m(TimelineItem, { start: "2024", middle: m(Icon, { icon: "fa-solid fa-star" }), connect: "start" })
                    ])
                ])
            ]),

            // NAVIGATION
            m(Section, { title: "Navigation" }, [
                m(CardWrapper, { title: "Navbar" }, [
                    m(Navbar, { class: "shadow-lg rounded-box" }, [
                        m(".flex-1", m("a.btn.btn-ghost.text-xl", "Brand")),
                        m(".flex-none", m(Button, { ghost: true }, m(Icon, { icon: "fa-solid fa-bars" })))
                    ])
                ]),
                m(CardWrapper, { title: "Breadcrumbs" }, [
                    m(Breadcrumbs, [
                        m("li", m(Link, { href: "#" }, "Home")),
                        m("li", "Settings")
                    ])
                ]),
                m(CardWrapper, { title: "Dock" }, [
                    m(Dock, { class: "static rounded-box" }, [
                        m(DockItem, { active: true }, m(Icon, { icon: "fa-solid fa-house" })),
                        m(DockItem, m(Icon, { icon: "fa-solid fa-bell" }))
                    ])
                ]),
                m(CardWrapper, { title: "Menu" }, [
                    m(Menu, { class: "w-full" }, [
                        m("li", m("a", "Dashboard")),
                        m("li", m("a", "Projects"))
                    ])
                ]),
                m(CardWrapper, { title: "Pagination" }, [
                    m(Pagination, { total: 3, current: 1 })
                ]),
                m(CardWrapper, { title: "Steps" }, [
                    m(Steps, { horizontal: true, class: "w-full" }, [
                        m(Step, { color: "primary" }, "Fly"),
                        m(Step, "Arrive")
                    ])
                ]),
                m(CardWrapper, { title: "Tabs" }, [
                    m(Tabs, { variant: "lifted" }, [
                        m(Tab, { active: true }, "Tab 1"),
                        m(Tab, "Tab 2")
                    ])
                ])
            ]),

            // FEEDBACK
            m(Section, { title: "Feedback" }, [
                m(CardWrapper, { title: "Alert" }, [
                    m(Alert, { type: "info", class: "w-full" }, "Updates available.")
                ]),
                m(CardWrapper, { title: "Loading & Progress" }, [
                    m(Loading, { variant: "dots" }),
                    m(Progress, { value: 40, max: 100, class: "w-full" }),
                    m(RadialProgress, { value: 75 })
                ]),
                m(CardWrapper, { title: "Skeleton" }, [
                    m(".flex.flex-col.gap-4.w-full", [
                        m(Skeleton, { class: "h-32 w-full" }),
                        m(Skeleton, { class: "h-4 w-28" })
                    ])
                ]),
                m(CardWrapper, { title: "Toast" }, [
                    m(".relative.h-20.w-full", [
                        m(Toast, { class: "absolute" }, [
                            m(Alert, { type: "success" }, "Saved!")
                        ])
                    ])
                ]),
                m(CardWrapper, { title: "Tooltip" }, [
                    m(Tooltip, { text: "Hello!" }, m(Button, "Hover me"))
                ])
            ]),

            // DATA INPUT
            m(Section, { title: "Data Input" }, [
                m(CardWrapper, { title: "Checkbox & Toggle" }, [
                    m(Checkbox, { checked: true }),
                    m(Toggle, { checked: true, color: "secondary" })
                ]),
                m(CardWrapper, { title: "Fieldset & Legend" }, [
                    m(Fieldset, { legend: "Login", class: "w-full" }, [
                        m(Label, "Password"),
                        m(TextInput, { type: "password", class: "w-full" })
                    ])
                ]),
                m(CardWrapper, { title: "File Input" }, [
                    m(FileInput, { class: "w-full" })
                ]),
                m(CardWrapper, { title: "Filter" }, [
                    m(Filter, { 
                        options: [
                            { label: "All", value: "all" },
                            { label: "Recent", value: "recent" }
                        ],
                        selected: "all"
                    })
                ]),
                m(CardWrapper, { title: "Radio & Range" }, [
                    m(Radio, { name: "opt", checked: true }),
                    m(Range, { min: 0, max: 100, value: 30, class: "w-full" })
                ]),
                m(CardWrapper, { title: "Rating" }, [
                    m(Rating, { value: 4 })
                ]),
                m(CardWrapper, { title: "Select & Textarea" }, [
                    m(Select, { class: "w-full" }, [m("option", "Pick me")]),
                    m(Textarea, { class: "w-full", placeholder: "Bio" })
                ]),
                m(CardWrapper, { title: "Validator" }, [
                    m(Validator, { hint: "Too short", class: "w-full" }, [
                        m(TextInput, { class: "w-full" })
                    ])
                ]),
                m(CardWrapper, { title: "Calendar" }, [
                    m(Calendar, { class: "bg-base-300 p-6 rounded-xl w-full text-center" }, "Calendar widget")
                ])
            ]),

            // LAYOUT
            m(Section, { title: "Layout" }, [
                m(CardWrapper, { title: "Hero" }, [
                    m(Hero, { class: "bg-base-300 rounded-box p-6 w-full" }, "Hero Content")
                ]),
                m(CardWrapper, { title: "Divider & Join" }, [
                    m(".flex.flex-col.w-full", [
                        m("div", "Top"),
                        m(Divider, "OR"),
                        m(Join, [
                            m(Button, { class: "join-item" }, "L"),
                            m(Button, { class: "join-item" }, "R")
                        ])
                    ])
                ]),
                m(CardWrapper, { title: "Indicator & Stack" }, [
                    m(Indicator, { item: m(Badge, { color: "primary" }, "8") }, [
                        m(Stack, [
                            m(".bg-base-300.w-20.h-20.rounded"),
                            m(".bg-base-200.w-20.h-20.rounded")
                        ])
                    ])
                ]),
                m(CardWrapper, { title: "Mask" }, [
                    m(Mask, { shape: "heart", src: "https://img.daisyui.com/images/stock/photo-1567653418876-5bb0e566e1c2.webp", class: "w-20" })
                ]),
                m(CardWrapper, { title: "Drawer Sidebar" }, [
                    m(".h-40.w-full.border.rounded-xl.overflow-hidden", [
                        m(Drawer, { 
                            id: "demo-drawer",
                            content: m(Button, { onclick: () => document.getElementById('demo-drawer').click() }, "Open"),
                            side: m(Menu, { class: "p-6 w-64 min-h-full" }, [m("li", m("a", "Sidebar Item"))])
                        })
                    ])
                ]),
                m(CardWrapper, { title: "Footer", class: "col-span-full" }, [
                    m(Footer, { class: "rounded-box" }, [
                        m("nav", [m("h6.footer-title", "Services"), m("a.link.link-hover", "Branding")]),
                        m("nav", [m("h6.footer-title", "Company"), m("a.link.link-hover", "About us")])
                    ])
                ])
            ]),

            // MOCKUP
            m(Section, { title: "Mockup" }, [
                m(CardWrapper, { title: "Browser", class: "col-span-full" }, [
                    m(BrowserMockup, { url: "mithril.js.org", class: "w-full" }, "Content")
                ]),
                m(CardWrapper, { title: "Code" }, [
                    m(CodeMockup, { class: "w-full" }, m("pre", m("code", "npm install mithril")))
                ]),
                m(CardWrapper, { title: "Window" }, [
                    m(WindowMockup, { class: "w-full" }, "Hello World")
                ]),
                m(CardWrapper, { title: "Phone" }, [
                    m(PhoneMockup, "Calling...")
                ])
            ]),

            m(Modal, { id: "comp_modal" }, [
                m("h3.text-lg.font-bold", "Hello!"),
                m("p.py-4", "Press ESC key or click the button below to close")
            ])
        ]);
    },
};

export default ComponentsPage;
