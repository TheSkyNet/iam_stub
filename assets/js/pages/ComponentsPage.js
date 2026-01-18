import m from "mithril";
import { Icon } from "../components/Icon";
import NavBrand from "../components/Nav/NavBrand";
import NavSearchInput from "../components/Nav/NavSearchInput";
import NavUserMenu from "../components/Nav/NavUserMenu";
import NavMenu from "../components/Nav/NavMenu";
import NavHorizontalMenu from "../components/Nav/NavHorizontalMenu";
import NavIndicator from "../components/Nav/NavIndicator";
import Footer from "../components/Footer";

const ComponentsPage = {
    view: () => {
        return m(".container.mx-auto.p-4.space-y-12", [
            m("header.mb-8", [
                m("h1.text-4xl.font-bold", "Navigation Components"),
                m("p", { class: "text-base-content opacity-70" }, "Modular DaisyUI navbar components built with Mithril.")
            ]),

            // Example 1
            m("section.space-y-4", [
                m("h2.text-2xl.font-semibold", "Example 1: Search and Avatar"),
                m(".navbar.bg-base-100.shadow-sm.rounded-box", [
                    m(".flex-1", [
                        m(NavBrand, { name: "daisyUI" })
                    ]),
                    m(".flex.gap-2", [
                        m(NavSearchInput),
                        m(NavUserMenu)
                    ])
                ])
            ]),

            // Example 2
            m("section.space-y-4", [
                m("h2.text-2xl.font-semibold", "Example 2: Center Logo and Icons"),
                m(".navbar.bg-base-100.shadow-sm.rounded-box", [
                    m(".navbar-start", [
                        m(NavMenu, {
                            items: [
                                { label: "Homepage", href: "/" },
                                { label: "Portfolio", href: "/portfolio" },
                                { label: "About", href: "/about" }
                            ]
                        })
                    ]),
                    m(".navbar-center", [
                        m(NavBrand, { name: "daisyUI" })
                    ]),
                    m(".navbar-end", [
                        m("button.btn.btn-ghost.btn-circle", [
                            m(Icon, { icon: "fa-solid fa-magnifying-glass" })
                        ]),
                        m(NavIndicator, { icon: "fa-solid fa-bell" })
                    ])
                ])
            ]),

            // Example 3
            m("section.space-y-4", [
                m("h2.text-2xl.font-semibold", "Example 3: Responsive with Submenus"),
                m(".navbar.bg-base-100.shadow-sm.rounded-box", [
                    m(".navbar-start", [
                        m(NavMenu, {
                            dropdownClass: "lg:hidden",
                            items: [
                                { label: "Item 1", href: "/item1" },
                                { 
                                    label: "Parent", 
                                    submenu: [
                                        { label: "Submenu 1", href: "/sub1" },
                                        { label: "Submenu 2", href: "/sub2" }
                                    ] 
                                },
                                { label: "Item 3", href: "/item3" }
                            ]
                        }),
                        m(NavBrand, { name: "daisyUI" })
                    ]),
                    m(".navbar-center.hidden.lg:flex", [
                        m(NavHorizontalMenu, {
                            items: [
                                { label: "Item 1", href: "/item1" },
                                { 
                                    label: "Parent", 
                                    submenu: [
                                        { label: "Submenu 1", href: "/sub1" },
                                        { label: "Submenu 2", href: "/sub2" }
                                    ] 
                                },
                                { label: "Item 3", href: "/item3" }
                            ]
                        })
                    ]),
                    m(".navbar-end", [
                        m("button.btn", "Button")
                    ])
                ])
            ]),

            // Navbar Theme Variations
            m("section.space-y-4", [
                m("h2.text-2xl.font-semibold", "Navbar Theme Variations"),
                m(".flex.flex-col.gap-4", [
                    m(".navbar.bg-neutral.text-neutral-content.rounded-box", [
                        m("button.btn.btn-ghost.text-xl", "daisyUI")
                    ]),
                    m(".navbar.bg-base-300.rounded-box", [
                        m("button.btn.btn-ghost.text-xl", "daisyUI")
                    ]),
                    m(".navbar.bg-primary.text-primary-content.rounded-box", [
                        m("button.btn.btn-ghost.text-xl", "daisyUI")
                    ])
                ])
            ]),

            // Data Input Section
            m("header.pt-12.mb-8.border-t.border-base-300", [
                m("h1.text-4xl.font-bold", "Data Input Components"),
                m("p", { class: "text-base-content opacity-70" }, "DaisyUI input components for forms and data entry.")
            ]),

            // Checkboxes
            m("section.space-y-8", [
                m("h2.text-2xl.font-semibold", "Checkboxes"),

                m(".grid.grid-cols-1.md:grid-cols-2.gap-8", [
                    // Basic & Fieldset
                    m(".space-y-4", [
                        m("h3.text-lg.font-medium", "Basic & Fieldset"),
                        m(".flex.items-center.gap-4", [
                            m("input[type=checkbox].checkbox", { checked: true })
                        ]),
                        m("fieldset.fieldset.bg-base-100.border-base-300.rounded-box.w-64.border.p-4", [
                            m("legend.fieldset-legend", "Login options"),
                            m("label.label.cursor-pointer", [
                                m("input[type=checkbox].checkbox", { checked: true }),
                                m("span.label-text.ml-2", "Remember me")
                            ])
                        ])
                    ]),

                    // Sizes
                    m(".space-y-4", [
                        m("h3.text-lg.font-medium", "Sizes"),
                        m(".flex.flex-wrap.items-center.gap-4", [
                            m("input[type=checkbox].checkbox.checkbox-xs", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-sm", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-md", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-lg", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-xl", { checked: true })
                        ])
                    ]),

                    // Colors
                    m(".space-y-4", [
                        m("h3.text-lg.font-medium", "Colors"),
                        m(".flex.flex-wrap.items-center.gap-4", [
                            m("input[type=checkbox].checkbox.checkbox-primary", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-secondary", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-accent", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-neutral", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-info", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-success", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-warning", { checked: true }),
                            m("input[type=checkbox].checkbox.checkbox-error", { checked: true })
                        ])
                    ]),

                    // States & Indeterminate
                    m(".space-y-4", [
                        m("h3.text-lg.font-medium", "States"),
                        m(".flex.flex-wrap.items-center.gap-4", [
                            m("input[type=checkbox].checkbox", { disabled: true }),
                            m("input[type=checkbox].checkbox", { disabled: true, checked: true }),
                            m("input[type=checkbox].checkbox", {
                                oncreate: (vnode) => {
                                    vnode.dom.indeterminate = true;
                                }
                            })
                        ])
                    ])
                ])
            ]),

            // Hero Section
            m("header.pt-12.mb-8.border-t.border-base-300", [
                m("h1.text-4xl.font-bold", "Hero Components"),
                m("p", { class: "text-base-content opacity-70" }, "DaisyUI hero components for high-impact landing areas.")
            ]),

            m("section.space-y-12", [
                // Hero with Background
                m(".space-y-4", [
                    m("h2.text-2xl.font-semibold", "Hero with Background Image"),
                    m(".hero.rounded-box.overflow-hidden.min-h-96", {
                        style: { backgroundImage: "url(https://img.daisyui.com/images/stock/photo-1507358522600-9f71e620c44e.webp)" }
                    }, [
                        m(".hero-overlay"),
                        m(".hero-content.text-neutral-content.text-center", [
                            m(".max-w-md", [
                                m("h1.mb-5.text-5xl.font-bold", "Hello there"),
                                m("p.mb-5", "Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi. In deleniti eaque aut repudiandae et a id nisi."),
                                m("button.btn.btn-primary", "Get Started")
                            ])
                        ])
                    ])
                ]),

                // Hero with Form
                m(".space-y-4", [
                    m("h2.text-2xl.font-semibold", "Hero with Login Form"),
                    m(".hero.bg-base-200.rounded-box.min-h-96", [
                        m(".hero-content.flex-col.lg:flex-row-reverse", [
                            m(".text-center.lg:text-left", [
                                m("h1.text-5xl.font-bold", "Login now!"),
                                m("p.py-6", "Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi. In deleniti eaque aut repudiandae et a id nisi.")
                            ]),
                            m(".card.bg-base-100.w-full.max-w-sm.shrink-0.shadow-2xl", [
                                m(".card-body", [
                                    m("fieldset.fieldset", [
                                        m("label.label", "Email"),
                                        m("input.input", { type: "email", placeholder: "Email" }),
                                        m("label.label", "Password"),
                                        m("input.input", { type: "password", placeholder: "Password" }),
                                        m("div", [
                                            m(m.route.Link, { href: "/forgot-password", class: "link link-hover" }, "Forgot password?")
                                        ]),
                                        m("button.btn.btn-neutral.mt-4", "Login")
                                    ])
                                ])
                            ])
                        ])
                    ])
                ])
            ]),

            // Card Section
            m("header.pt-12.mb-8.border-t.border-base-300", [
                m("h1.text-4xl.font-bold", "Card Components"),
                m("p", { class: "text-base-content opacity-70" }, "DaisyUI cards with various layout and background options.")
            ]),

            m("section.space-y-12", [
                // Cards with Image Background
                m(".space-y-4", [
                    m("h2.text-2xl.font-semibold", "Cards with Image Background (Image Full)"),
                    m(".grid.grid-cols-1.md:grid-cols-2.lg:grid-cols-3.gap-6", [
                        // Exact Shoes snippet from user
                        m("div", {"class":"card bg-base-100 image-full w-96 shadow-sm"}, [
                            m("figure", 
                                m("img", {"src":"https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp","alt":"Shoes"})
                            ),
                            m("div", {"class":"card-body"}, [
                                m("h2", {"class":"card-title"}, "Card Title"),
                                m("p", "A card component has a figure, a body part, and inside body there are title and actions parts"),
                                m("div", {"class":"card-actions justify-end"}, 
                                    m("button", {"class":"btn btn-primary"}, "Buy Now")
                                )
                            ])
                        ]),
                        m("div", {"class":"card bg-base-100 image-full shadow-sm"}, [
                            m("figure", 
                                m("img", {"src":"https://images.unsplash.com/photo-1493606278519-11aa9f86e40a?auto=format&fit=crop&w=800&q=80","alt":"Abstract"})
                            ),
                            m("div", {"class":"card-body"}, [
                                m("h2", {"class":"card-title"}, "Geometric Shapes"),
                                m("p", "Colorful abstract geometry for modern interfaces."),
                                m("div", {"class":"card-actions justify-end"}, 
                                    m("button", {"class":"btn btn-primary"}, "Order")
                                )
                            ])
                        ]),
                        m("div", {"class":"card bg-base-100 image-full shadow-sm"}, [
                            m("figure", 
                                m("img", {"src":"https://images.unsplash.com/photo-1550684848-fac1c5b4e853?auto=format&fit=crop&w=800&q=80","alt":"Abstract"})
                            ),
                            m("div", {"class":"card-body"}, [
                                m("h2", {"class":"card-title"}, "Vibrant Gradients"),
                                m("p", "Dynamic flow and color transitions."),
                                m("div", {"class":"card-actions justify-end"}, 
                                    m("button", {"class":"btn btn-primary"}, "Watch")
                                )
                            ])
                        ])
                    ])
                ]),

                // Additional Card Variations
                m(".space-y-4", [
                    m("h2.text-2xl.font-semibold", "More Card Variations"),
                    m(".grid.grid-cols-1.md:grid-cols-2.gap-8", [
                        // Colored Card
                        m(".card.bg-primary.text-primary-content.shadow-xl", [
                            m(".card-body", [
                                m("h2.card-title", "Card title!"),
                                m("p", "A card component has a figure, a body part, and inside body there are title and actions parts"),
                                m(".card-actions.justify-end", [
                                    m("button.btn", "Buy Now")
                                ])
                            ])
                        ]),
                        // Standard Card with Image
                        m(".card.bg-base-100.shadow-xl", [
                            m("figure", [
                                m("img", { src: "https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80", alt: "Abstract" })
                            ]),
                            m(".card-body", [
                                m("h2.card-title", "Abstract Art"),
                                m("p", "A card component has a figure, a body part, and inside body there are title and actions parts"),
                                m(".card-actions.justify-end", [
                                    m("button.btn.btn-primary", "Buy Now")
                                ])
                            ])
                        ])
                    ]),
                    // Side Image Card
                    m(".card.lg:card-side.bg-base-100.shadow-xl.mt-8", [
                        m("figure", [
                            m("img", { src: "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80", alt: "Abstract", class: "object-cover h-full" })
                        ]),
                        m(".card-body", [
                            m("h2.card-title", "Fluid Design"),
                            m("p", "Experience smooth transitions and organic shapes."),
                            m(".card-actions.justify-end", [
                                m("button.btn.btn-primary", "Explore")
                            ])
                        ])
                    ])
                ])
            ]),

            // Text Input Section
            m("header.pt-12.mb-8.border-t.border-base-300", [
                m("h1.text-4xl.font-bold", "Text Input Components"),
                m("p", { class: "text-base-content opacity-70" }, "DaisyUI text input components for various data entry needs.")
            ]),

            m("section.space-y-12", [
                // Basic & Decorators
                m(".space-y-4", [
                    m("h2.text-2xl.font-semibold", "Basic & Decorators"),
                    m(".grid.grid-cols-1.md:grid-cols-2.gap-8", [
                        m(".space-y-4", [
                            m("h3.text-lg.font-medium", "Basic Input"),
                            m("input.input.w-full", { type: "text", placeholder: "Type here" })
                        ]),
                        m(".space-y-4", [
                            m("h3.text-lg.font-medium", "Search with Kbd"),
                            m("label.input.w-full", [
                                m(Icon, { icon: "fa-solid fa-magnifying-glass", class: "opacity-50" }),
                                m("input.grow", { type: "search", placeholder: "Search" }),
                                m("kbd.kbd.kbd-sm", "Cmd"),
                                m("kbd.kbd.kbd-sm", "K")
                            ])
                        ]),
                        m(".space-y-4", [
                            m("h3.text-lg.font-medium", "Input with Icon (File)"),
                            m("label.input.w-full", [
                                m(Icon, { icon: "fa-solid fa-file-code", class: "opacity-50" }),
                                m("input.grow", { type: "text", placeholder: "index.php" })
                            ])
                        ]),
                        m(".space-y-4", [
                            m("h3.text-lg.font-medium", "Input with Label & Badge"),
                            m("label.input.w-full", [
                                "Path",
                                m("input.grow", { type: "text", placeholder: "src/app/" }),
                                m("span.badge.badge-neutral.badge-xs", "Optional")
                            ])
                        ])
                    ])
                ]),

                // Fieldset & States
                m(".space-y-4", [
                    m("h2.text-2xl.font-semibold", "Fieldset & States"),
                    m(".grid.grid-cols-1.md:grid-cols-2.gap-8", [
                        m(".space-y-4", [
                            m("fieldset.fieldset", [
                                m("legend.fieldset-legend", "What is your name?"),
                                m("input.input.w-full", { type: "text", placeholder: "Type here" }),
                                m("p.label", "Optional")
                            ])
                        ]),
                        m(".space-y-4", [
                            m("h3.text-lg.font-medium", "Validator"),
                            m("label.input.validator.w-full", [
                                m(Icon, { icon: "fa-solid fa-phone", class: "opacity-50" }),
                                m("input", {
                                    type: "tel",
                                    class: "tabular-nums",
                                    required: true,
                                    placeholder: "Phone",
                                    pattern: "[0-9]*",
                                    minlength: "10",
                                    maxlength: "10",
                                    title: "Must be 10 digits"
                                })
                            ]),
                            m("p.validator-hint", "Must be 10 digits")
                        ])
                    ])
                ]),

                // Colors
                m(".space-y-4", [
                    m("h2.text-2xl.font-semibold", "Colors"),
                    m(".flex.flex-wrap.gap-4", [
                        m("input.input.input-neutral", { type: "text", placeholder: "Neutral" }),
                        m("input.input.input-primary", { type: "text", placeholder: "Primary" }),
                        m("input.input.input-secondary", { type: "text", placeholder: "Secondary" }),
                        m("input.input.input-accent", { type: "text", placeholder: "Accent" }),
                        m("input.input.input-info", { type: "text", placeholder: "Info" }),
                        m("input.input.input-success", { type: "text", placeholder: "Success" }),
                        m("input.input.input-warning", { type: "text", placeholder: "Warning" }),
                        m("input.input.input-error", { type: "text", placeholder: "Error" })
                    ])
                ]),

                // Sizes
                m(".space-y-4", [
                    m("h2.text-2xl.font-semibold", "Sizes"),
                    m(".flex.flex-wrap.items-end.gap-4", [
                        m("input.input.input-xs", { type: "text", placeholder: "Xsmall" }),
                        m("input.input.input-sm", { type: "text", placeholder: "Small" }),
                        m("input.input.input-md", { type: "text", placeholder: "Medium" }),
                        m("input.input.input-lg", { type: "text", placeholder: "Large" }),
                        m("input.input.input-xl", { type: "text", placeholder: "Xlarge" })
                    ])
                ])
            ]),

            // Toast & Alert Section
            m("header.pt-12.mb-8.border-t.border-base-300", [
                m("h1.text-4xl.font-bold", "Toast & Alert Components"),
                m("p", { class: "text-base-content opacity-70" }, "DaisyUI alerts and toast notifications.")
            ]),

            m("section.space-y-8", [
                m("div", {"class": "toast toast-end relative bottom-auto right-auto"}, [
                    m("div", {"class": "alert alert-info shadow-sm"}, [
                        m("span", "New mail arrived.")
                    ]),
                    m("div", {"class": "alert alert-success shadow-sm"}, [
                        m("span", "Message sent successfully.")
                    ])
                ]),
                m("div", {"class": "flex flex-col gap-4"}, [
                    m("div", {"class": "alert alert-info shadow-sm"}, [
                        m(Icon, { name: "fa-solid fa-circle-info" }),
                        m("span", "New software update available.")
                    ]),
                    m("div", {"class": "alert alert-success shadow-sm"}, [
                        m(Icon, { name: "fa-solid fa-circle-check" }),
                        m("span", "Your purchase has been confirmed!")
                    ]),
                    m("div", {"class": "alert alert-warning shadow-sm"}, [
                        m(Icon, { name: "fa-solid fa-triangle-exclamation" }),
                        m("span", "Warning: Invalid email address!")
                    ]),
                    m("div", {"class": "alert alert-error shadow-sm"}, [
                        m(Icon, { name: "fa-solid fa-circle-xmark" }),
                        m("span", "Error! Task failed successfully.")
                    ])
                ])
            ]),

            // Footer Section
            m("header.pt-12.mb-8.border-t.border-base-300", [
                m("h1.text-4xl.font-bold", "Footer Components"),
                m("p", { class: "text-base-content opacity-70" }, "DaisyUI footer components for page bottom navigation.")
            ]),

            m("section.pb-12", [
                m(Footer)
            ])
        ]);
    },
};

export default ComponentsPage;
