import { CodeEditor } from "./CodeEditor";

const AdminCodeEditor = {
    view: function() {
        return m("div.code-editor-page", {
            style: {
                height: "calc(100vh - 60px)",
                background: "#f0f4f8",
                margin: "-2rem", // Compensate for the main content padding
                display: "flex",
                flexDirection: "column"
            }
        }, [
            // Header
            m("div.editor-page-header", {
                style: {
                    padding: "1rem 2rem",
                    background: "white",
                    borderBottom: "1px solid #e2e8f0",
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center"
                }
            }, [
                m("h1", {
                    style: {
                        margin: 0,
                        fontSize: "1.5rem",
                        color: "#2d3748"
                    }
                }, "Code Editor"),
                m(m.route.Link, {
                    href: "/code",
                    style: {
                        padding: "0.5rem 1rem",
                        background: "#8198c4",
                        color: "white",
                        borderRadius: "4px",
                        textDecoration: "none"
                    }
                }, "Back to Code List")
            ]),

            // CodeEditor component
            m(CodeEditor)
        ]);
    }
};

export { AdminCodeEditor };