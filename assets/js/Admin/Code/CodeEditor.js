const CodeEditor = {
    initial: {
        code: '',
        title: '',
        description: ''
    },

    oninit: function(vnode) {
        CodeEditor.state = {
            ...CodeEditor.initial,
            isPreviewMode: false
        };
    },

    view: function(vnode) {
        return m("div.code-editor", {
            style: {
                display: "flex",
                flexDirection: "column",
                height: "calc(100vh - 80px)",
                padding: "1rem",
                background: "#f0f4f8",
                gap: "1rem"
            }
        }, [
            // Header with title and controls
            m("div.editor-header", {
                style: {
                    display: "flex",
                    gap: "1rem",
                    alignItems: "center",
                    padding: "1rem",
                    background: "white",
                    borderRadius: "8px",
                    boxShadow: "0 2px 4px rgba(0,0,0,0.05)"
                }
            }, [
                m("input[type=text]", {
                    placeholder: "Enter title",
                    value: CodeEditor.state.title,
                    style: {
                        flex: "1",
                        padding: "0.5rem",
                        border: "1px solid #e2e8f0",
                        borderRadius: "4px",
                        fontSize: "1rem"
                    },
                    onchange: (e) => CodeEditor.state.title = e.target.value
                }),
                m("button", {
                    onclick: () => CodeEditor.state.isPreviewMode = !CodeEditor.state.isPreviewMode,
                    style: {
                        padding: "0.5rem 1rem",
                        background: "#8198c4",
                        color: "white",
                        border: "none",
                        borderRadius: "4px",
                        cursor: "pointer"
                    }
                }, CodeEditor.state.isPreviewMode ? "Edit" : "Preview"),
                m("button", {
                    onclick: () => CodeEditor.saveCode(),
                    style: {
                        padding: "0.5rem 1rem",
                        background: "#68d391",
                        color: "white",
                        border: "none",
                        borderRadius: "4px",
                        cursor: "pointer"
                    }
                }, "Save")
            ]),

            // Main editor area
            m("div.editor-main", {
                style: {
                    display: "flex",
                    gap: "1rem",
                    flex: 1,
                    minHeight: 0
                }
            }, [
                // Code editor (hidden in preview mode)
                !CodeEditor.state.isPreviewMode && m("textarea", {
                    value: CodeEditor.state.code,
                    onchange: (e) => CodeEditor.state.code = e.target.value,
                    style: {
                        flex: 1,
                        padding: "1rem",
                        background: "white",
                        border: "1px solid #e2e8f0",
                        borderRadius: "8px",
                        fontFamily: "monospace",
                        fontSize: "14px",
                        resize: "none",
                        boxShadow: "0 2px 4px rgba(0,0,0,0.05)"
                    }
                }),

                // Preview panel
                m("div.preview-panel", {
                    style: {
                        flex: CodeEditor.state.isPreviewMode ? 1 : 0.5,
                        background: "white",
                        borderRadius: "8px",
                        overflow: "hidden",
                        boxShadow: "0 2px 4px rgba(0,0,0,0.05)"
                    }
                }, [
                    m("iframe", {
                        srcdoc: CodeEditor.state.code,
                        style: {
                            width: "100%",
                            height: "100%",
                            border: "none"
                        }
                    })
                ])
            ])
        ]);
    },

    saveCode() {

    }
};

export { CodeEditor };