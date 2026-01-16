import { Icon } from "./Icon";

const Welcome = {
    view: function() {
        return m(".min-h-screen.flex.items-center.justify-center.p-8", [
            m(".container.mx-auto.max-w-4xl", [
                m(".text-center", [
                    m("h1.text-5xl.font-bold.text-base-content.mb-6", "Welcome to Your Phalcon Stub Project"),
                    m("p.text-xl.text-base-content.opacity-80.mb-8", "This is a clean, minimal Phalcon PHP framework stub project designed to serve as a foundation for generating new applications."),
                    m(".grid.grid-cols-1.md:grid-cols-2.lg:grid-cols-3.gap-6.mt-12", [
                        m(".card.bg-base-100.shadow-xl.h-full", [
                            m(".card-body", [
                                m("h2.card-title.text-2xl.mb-4", [
                                    m(Icon, { name: 'fa-solid fa-lock', class: 'mr-2' }),
                                    "Authentication"
                                ]),
                                m("p.text-base-content.opacity-70.mb-6", "Complete user authentication system with login, register, forgot password, and session management."),
                                m(".card-actions.justify-center", [
                                    m(m.route.Link, {
                                        class: "btn btn-primary btn-sm",
                                        href: "/login"
                                    }, "Try Login")
                                ])
                            ])
                        ]),
                        m(".card.bg-base-100.shadow-xl.h-full", [
                            m(".card-body", [
                                m("h2.card-title.text-2xl.mb-4", [
                                    m(Icon, { name: 'fa-solid fa-envelope', class: 'mr-2' }),
                                    "Email Service"
                                ]),
                                m("p.text-base-content.opacity-70.mb-4", "Configurable email service with MailHog and Resend providers for development and production."),
                                m("p.text-base-content.opacity-50.text-sm", "Ready for password resets & notifications")
                            ])
                        ]),
                        m(".card.bg-base-100.shadow-xl.h-full", [
                            m(".card-body", [
                                m("h2.card-title.text-2xl.mb-4", [
                                    m(Icon, { name: 'fa-solid fa-bolt', class: 'mr-2' }),
                                    "Real-time"
                                ]),
                                m("p.text-base-content.opacity-70.mb-4", "Pusher.js integration for real-time WebSocket communication and live updates."),
                                m(".card-actions.justify-center", [
                                    m(m.route.Link, {
                                        class: "btn btn-secondary btn-sm",
                                        href: "/pusher-test"
                                    }, "Test Pusher")
                                ])
                            ])
                        ]),
                        m(".card.bg-base-100.shadow-xl.h-full", [
                            m(".card-body", [
                                m("h2.card-title.text-2xl.mb-4", [
                                    m(Icon, { name: 'fa-solid fa-folder-open', class: 'mr-2' }),
                                    "File Upload"
                                ]),
                                m("p.text-base-content.opacity-70.mb-4", "FilePond integration for modern file uploads with drag & drop support."),
                                m("p.text-base-content.opacity-50.text-sm", "Ready for images, documents & more")
                            ])
                        ]),
                        m(".card.bg-base-100.shadow-xl.h-full", [
                            m(".card-body", [
                                m("h2.card-title.text-2xl.mb-4", [
                                    m(Icon, { name: 'fa-solid fa-gears', class: 'mr-2' }),
                                    "Settings"
                                ]),
                                m("p.text-base-content.opacity-70.mb-4", "Flexible site settings management system for configuration and customization."),
                                m("p.text-base-content.opacity-50.text-sm", "Available after login")
                            ])
                        ]),
                        m(".card.bg-base-100.shadow-xl.h-full", [
                            m(".card-body", [
                                m("h2.card-title.text-2xl.mb-4", [
                                    m(Icon, { name: 'fa-solid fa-screwdriver-wrench', class: 'mr-2' }),
                                    "Developer Tools"
                                ]),
                                m("p.text-base-content.opacity-70.mb-4", "Docker setup, Tailwind CSS, DaisyUI, and modern build tools included."),
                                m("p.text-base-content.opacity-50.text-sm", "Ready for development")
                            ])
                        ])
                    ]),
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
