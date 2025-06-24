const Welcome = {
    view: function() {
        return m("div.container.mt-5", [
            m("div.row.justify-content-center", [
                m("div.col-md-8.text-center", [
                    m("h1.display-4.mb-4", "Welcome to Your Phalcon Stub Project"),
                    m("p.lead.mb-4", "This is a clean, minimal Phalcon PHP framework stub project designed to serve as a foundation for generating new applications."),
                    m("div.row.mt-5", [
                        m("div.col-md-6.mb-4", [
                            m("div.card.h-100", [
                                m("div.card-body", [
                                    m("h5.card-title", "🔐 Authentication"),
                                    m("p.card-text", "Complete user authentication system with login, logout, and session management."),
                                    m("a.btn.btn-primary[href='/login']", {oncreate: m.route.link}, "Login")
                                ])
                            ])
                        ]),
                        m("div.col-md-6.mb-4", [
                            m("div.card.h-100", [
                                m("div.card-body", [
                                    m("h5.card-title", "⚙️ Settings"),
                                    m("p.card-text", "Flexible site settings management system for configuration and customization."),
                                    m("p.text-muted.small", "Available after login")
                                ])
                            ])
                        ])
                    ]),
                    m("div.mt-5", [
                        m("h3.mb-3", "Ready to Build Something Amazing?"),
                        m("p.text-muted", "Start customizing this stub for your next PHfalcon or Miral project!")
                    ])
                ])
            ])
        ]);
    }
};

export {Welcome};