import m from "mithril";
import { Icon } from "../../components/Icon";

const TestPage = {
    triggerValidationError: () => {
        const mockResponse = {
            success: false,
            message: "Validation failed",
            errors: {
                email: ["Invalid email address"],
                password: ["Password must be at least 8 characters", "Must contain a number"]
            }
        };
        window.showToast(mockResponse, 'error');
    },

    view: () => {
        return m(".container.mx-auto.p-4", [
            m("h1.text-3xl.font-bold.mb-6", "Generic Test Page"),
            m(".grid.grid-cols-1.md:grid-cols-2.gap-6", [
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", "Toast & Error Service"),
                        m("p", "Test the notification system."),
                        m(".flex.gap-2.flex-wrap.mt-4", [
                            m("button.btn.btn-info", { onclick: () => window.showToast('Info message', 'info') }, "Show Info"),
                            m("button.btn.btn-success", { onclick: () => window.showToast('Success message', 'success') }, "Show Success"),
                            m("button.btn.btn-warning", { onclick: () => window.showToast('Warning message', 'warning') }, "Show Warning"),
                            m("button.btn.btn-error", { onclick: () => window.showToast('Error message', 'error') }, "Show Error"),
                        ]),
                        m(".flex.gap-2.flex-wrap.mt-2", [
                            m("button.btn.btn-outline", { onclick: () => TestPage.triggerValidationError() }, "Test Validation Error"),
                            m("button.btn.btn-outline.btn-error", { onclick: () => { throw new Error('Sync Error Test'); } }, "Trigger Sync Error"),
                            m("button.btn.btn-outline.btn-error", { onclick: () => Promise.reject('Async Rejection Test') }, "Trigger Async Error"),
                        ])
                    ])
                ]),
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", "Route Tests"),
                        m("p", "Testing SPA routing and parameters."),
                        m(m.route.Link, { href: "/test?param=value", class: "btn btn-outline" }, "Test with Params")
                    ])
                ])
            ])
        ]);
    }
};

export default TestPage;
