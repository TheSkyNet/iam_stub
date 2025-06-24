// Terminal.js
const Terminal = {
    oninit: (vnode) => {
        Terminal.state = {
            history: [],
            currentCommand: "",
            prompt: vnode.attrs.prompt || ">",
            commands: {
                ...Terminal.defaultCommands,
                ...(vnode.attrs.commands || {})
            }
        };
        // Add MOTD on init
        Terminal.state.history.push({
            response: `Welcome to Laboratory Control Terminal
System Version: 2025.2
Type 'help' for available commands
`,
            timestamp: new Date().toISOString()
        });
    },

    state: {
        history: [],
        currentCommand: "",
        prompt: ">",
        commands: {}
    },

    defaultCommands: {
        help: () => "Available commands: " + Object.keys(Terminal.state.commands).join(", "),
        clear: () => {
            Terminal.state.history = [];
            return null;
        },
        echo: (args) => args.join(" "),
        date: () => new Date().toLocaleString(),
        whoami: () => "lab-user@terminal",
    },

    processCommand(input) {
        const args = input.trim().split(/\s+/);
        const cmd = args[0].toLowerCase();
        const cmdArgs = args.slice(1);

        let response;
        if (this.state.commands[cmd]) {
            try {
                response = this.state.commands[cmd](cmdArgs);
            } catch (e) {
                response = `Error executing command: ${e.message}`;
            }
        } else {
            response = `Command not found: ${cmd}. Type 'help' for available commands.`;
        }

        this.state.history.push({
            cmd: input,
            response,
            timestamp: new Date().toISOString()
        });
        this.state.currentCommand = "";

        // Auto scroll to bottom after command execution
        setTimeout(() => {
            const content = document.querySelector('.terminal-content');
            if (content) {
                content.scrollTop = content.scrollHeight;
            }
        }, 0);
    },

    view: (vnode) => {
        const { backgroundColor = "#8198c4", textColor = "#e2e8f0", maxHeight = "400px" } = vnode.attrs;

        return m("div.terminal", {
            style: {
                background: backgroundColor,
                borderRadius: "8px",
                padding: "1rem",
                color: textColor,
                fontFamily: "monospace",
                maxHeight: maxHeight,
                overflowY: "auto",
                display: "flex",
                flexDirection: "column"
            }
        }, [
            // Terminal content area
            m("div.terminal-content", {
                style: {
                    flex: 1,
                    overflowY: "auto"
                }
            }, [
                // Command History
                ...Terminal.state.history.map(entry => [
                    entry.cmd && m("div.command-line", {
                        style: {
                            display: "flex",
                            gap: "0.5rem",
                            color: "#68d391",
                            marginBottom: "0.25rem"
                        }
                    }, [
                        m("span.prompt", Terminal.state.prompt),
                        m("span.command", entry.cmd)
                    ]),
                    entry.response && m("div.response", {
                        style: {
                            marginBottom: "0.75rem",
                            paddingLeft: entry.cmd ? `calc(1rem + ${Terminal.state.prompt.length}ch)` : "0",
                            whiteSpace: "pre-wrap",
                            color: textColor
                        }
                    }, entry.response)
                ]),

                // Command Input Line
                m("div.command-input", {
                    style: {
                        display: "flex",
                        alignItems: "center",
                        gap: "0.5rem"
                    }
                }, [
                    m("span.prompt", {
                        style: { color: "#68d391" }
                    }, Terminal.state.prompt),
                    m("input[type=text]", {
                        value: Terminal.state.currentCommand,
                        style: {
                            background: "transparent",
                            border: "none",
                            color: textColor,
                            fontFamily: "monospace",
                            flex: 1,
                            outline: "none",
                            fontSize: "inherit"
                        },
                        autocomplete: "off",
                        spellcheck: false,
                        onkeydown: (e) => {
                            if (e.key === "Enter") {
                                e.preventDefault();
                                if (Terminal.state.currentCommand.trim()) {
                                    Terminal.processCommand(Terminal.state.currentCommand);
                                    m.redraw();
                                }
                            }
                        },
                        oninput: (e) => {
                            Terminal.state.currentCommand = e.target.value;
                        }
                    })
                ])
            ])
        ]);
    }
};

export { Terminal };