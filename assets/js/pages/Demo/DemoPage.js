import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";

export default class DemoPage {
    view() {
        const isAdmin = AuthService.isAdmin();
        const isLoggedIn = AuthService.isLoggedIn();

        const demoCards = [
            {
                title: "Real-time (Pusher)",
                description: "Test WebSocket communication, private channels, and event triggering.",
                icon: "fa-solid fa-broadcast-tower",
                href: "/pusher-test",
                color: "primary",
                requiresAuth: true
            },
            {
                title: "Server-Sent Events (SSE)",
                description: "Test one-way real-time updates from the server using SSE.",
                icon: "fa-solid fa-clock",
                href: "/sse-test",
                color: "secondary",
                requiresAuth: true
            },
            {
                title: "Payments & Subscriptions",
                description: "Multi-provider payment system supporting Stripe, PayPal, and Square.",
                icon: "fa-solid fa-credit-card",
                href: "/payments",
                color: "accent",
                requiresAuth: true
            },
            {
                title: "PayPal Integration Demo",
                description: "Deep dive into PayPal-specific features and sandbox testing.",
                icon: "fa-brands fa-paypal",
                href: "/demo/paypal",
                color: "info",
                requiresAuth: true
            },
            {
                title: "LMS Integration",
                description: "Manage AI-powered LMS integrations (Ollama, Gemini, Tencent EDU).",
                icon: "fa-solid fa-graduation-cap",
                href: "/admin/lms",
                color: "info",
                requiresAdmin: true
            },
            {
                title: "User & Role Management",
                description: "Complete RBAC system with user profiles and role assignment.",
                icon: "fa-solid fa-users-cog",
                href: "/admin/users",
                color: "warning",
                requiresAdmin: true
            },
            {
                title: "Job Queue & Workers",
                description: "Monitor and manage background jobs with priority and retry logic.",
                icon: "fa-solid fa-list-check",
                href: "/admin/jobs",
                color: "success",
                requiresAdmin: true
            },
            {
                title: "Error & Activity Logs",
                description: "Centralized error reporting and monitoring for frontend and backend.",
                icon: "fa-solid fa-bug",
                href: "/admin/errors",
                color: "error",
                requiresAdmin: true
            },
            {
                title: "UI Components Library",
                description: "Explore the DaisyUI v5 components and project-specific UI elements.",
                icon: "fa-solid fa-layer-group",
                href: "/components",
                color: "ghost"
            }
        ];

        return m(".container.mx-auto.p-4.py-12", [
            m(".text-center.mb-12", [
                m("h1.text-5xl.font-bold.mb-4", [
                    m(Icon, { icon: "fa-solid fa-flask text-primary mr-4" }),
                    "Demo & Testing Dashboard"
                ]),
                m("p.text-xl.opacity-70", "Explore the core features and integrations of the Phalcon Stub project.")
            ]),

            m(".grid.grid-cols-1.md:grid-cols-2.lg:grid-cols-3.gap-8", 
                demoCards.map(card => {
                    const isDisabled = (card.requiresAuth && !isLoggedIn) || (card.requiresAdmin && !isAdmin);
                    
                    let lockIcon = null;
                    if (isDisabled) {
                        lockIcon = m(Icon, { icon: "fa-solid fa-lock text-error ml-2" });
                    }

                    let badge = null;
                    if (isDisabled) {
                        const badgeText = card.requiresAdmin ? "Admin required" : "Login required";
                        badge = m("span.text-xs.italic.text-error.mr-2", badgeText);
                    }

                    const linkClass = `btn btn-${card.color} ${isDisabled ? 'btn-disabled' : ''}`;

                    return m(".card.bg-base-100.shadow-xl.hover:shadow-2xl.transition-all", [
                        m(".card-body", [
                            m(".flex.items-center.mb-2", [
                                m(`.w-12.h-12.rounded-lg.bg-${card.color}.flex.items-center.justify-center.text-white.text-2xl.mr-4`, [
                                    m(Icon, { icon: card.icon })
                                ]),
                                m("h2.card-title", [card.title, lockIcon])
                            ]),
                            m("p.mb-6.opacity-80", card.description),
                            m(".card-actions.justify-end", [
                                badge,
                                m(m.route.Link, { 
                                    href: card.href, 
                                    class: linkClass 
                                }, [
                                    "Launch Demo",
                                    m(Icon, { icon: "fa-solid fa-arrow-right ml-2" })
                                ])
                            ])
                        ])
                    ]);
                })
            )
        ]);
    }
}
