import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";

export default class DemoPage {
    renderCard(card, isLoggedIn, isAdmin) {
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
    }

    view() {
        const isAdmin = AuthService.isAdmin();
        const isLoggedIn = AuthService.isLoggedIn();

        const generalDemos = [
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
                title: "Payments Management",
                description: "Unified dashboard to view payment history and manage active subscriptions.",
                icon: "fa-solid fa-credit-card",
                href: "/payments",
                color: "accent",
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
                title: "UI Components Library",
                description: "Explore the DaisyUI v5 components and project-specific UI elements.",
                icon: "fa-solid fa-layer-group",
                href: "/components",
                color: "ghost"
            }
        ];

        const paymentDemos = [
            {
                title: "PayPal Integration",
                description: "Deep dive into PayPal-specific features, SDK v6 integration, and sandbox testing.",
                icon: "fa-brands fa-paypal",
                href: "/demo/paypal",
                color: "info",
                requiresAuth: true
            },
            {
                title: "Stripe Integration",
                description: "Demonstrate Stripe Elements, Apple/Google Pay, and complex subscription flows.",
                icon: "fa-brands fa-stripe",
                href: "/demo/stripe",
                color: "primary",
                requiresAuth: true
            },
            {
                title: "Square Integration",
                description: "Test Square's modern payment fields and recurring billing capabilities.",
                icon: "fa-brands fa-square",
                href: "/demo/square",
                color: "secondary",
                requiresAuth: true
            },
            {
                title: "Pace Integration",
                description: "UK-market focused payment integration with fast settlement and low fees.",
                icon: "fa-solid fa-credit-card",
                href: "/demo/pace",
                color: "accent",
                requiresAuth: true
            },
            {
                title: "Mollie Integration",
                description: "Simple-to-setup payment provider for UK and Europe with a great developer experience.",
                icon: "fa-solid fa-credit-card",
                href: "/demo/mollie",
                color: "secondary",
                requiresAuth: true
            },
            {
                title: "Revolut Pay",
                description: "Popular UK-based digital banking and payment solution with high conversion rates.",
                icon: "fa-solid fa-credit-card",
                href: "/demo/revolut",
                color: "primary",
                requiresAuth: true
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

            m("h2.text-2xl.font-bold.mb-6.flex.items-center.gap-2", [
                m(Icon, { icon: "fa-solid fa-gears text-primary" }),
                "General Feature Demos"
            ]),
            m(".grid.grid-cols-1.md:grid-cols-2.lg:grid-cols-3.gap-8.mb-16", 
                generalDemos.map(card => this.renderCard(card, isLoggedIn, isAdmin))
            ),

            m("h2.text-2xl.font-bold.mb-6.flex.items-center.gap-2", [
                m(Icon, { icon: "fa-solid fa-wallet text-secondary" }),
                "Payment Provider Demos"
            ]),
            m(".grid.grid-cols-1.md:grid-cols-2.lg:grid-cols-3.gap-8", 
                paymentDemos.map(card => this.renderCard(card, isLoggedIn, isAdmin))
            )
        ]);
    }
}
