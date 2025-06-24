import {Terminal} from "./Dashbord/Terminal";

const AdminTerminal = {
    view: function() {
        return m("div.terminal-page", {
            style: {
                padding: "2rem",
                height: "calc(100vh - 60px)", // Subtract navbar height
                background: "#8198c4",
                display: "flex",
                flexDirection: "column"
            }
        }, [
            // Header
            m("div.terminal-header", {
                style: {
                    marginBottom: "1.5rem",
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center",
                    color: "#2e3035",
                    padding: "0 1rem"
                }
            }, [
                m("h2", {
                    style: {
                        color: "#2e3035",
                        fontFamily: "monospace",
                        margin: 0,
                        fontSize: "1.75rem"
                    }
                }, "Laboratory Control Terminal"),
            ]),

            // Terminal container with elevated design
            m("div.terminal-container", {
                style: {
                    flex: 1,
                    minHeight: 0,
                    background: "#8198c4",
                    borderRadius: "12px",
                    padding: "0.1rem",
                    boxShadow: "0 10px 25px rgba(0, 0, 0, 0.2)",
                    border: "1px solid rgba(255, 255, 255, 0.1)",
                    position: "relative",
                    overflow: "hidden"
                }
            }, [
                // Background gradient overlay
                m("div.terminal-background", {
                    style: {
                        position: "absolute",
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        background: "linear-gradient(145deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%)",
                        pointerEvents: "none"
                    }
                }),

                // Terminal component
                m(Terminal, {
                    maxHeight: "100%",
                    textColor: "#2e3035",
                    backgroundColor: "#8198c4",
                    commands: {
                        status: () => "All systems operational ✓",
                        uptime: () => "System uptime: " + Math.floor(Math.random() * 100) + " days",
                        memory: () => "Memory usage: " + Math.floor(Math.random() * 100) + "%",
                        cpu: () => "CPU usage: " + Math.floor(Math.random() * 100) + "%",
                        network: () => "Network status: Connected",
                        processes: () => "Active processes: " + Math.floor(Math.random() * 50),
                        users: () => "Active users: " + Math.floor(Math.random() * 10),
                        version: () => "System Version: 2025.2",
                        time: () => new Date().toLocaleString(),
                    }
                })
            ])
        ]);
    }
};
export { AdminTerminal };