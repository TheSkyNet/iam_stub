// components/StatCard.js
const StatCard = {
    view: function({attrs}) {
        const {title, data, color} = attrs;
        return m("div.stat-card", {
            style: {
                background: "#ffffff",
                borderRadius: "0.5rem",
                padding: "1.5rem",
                boxShadow: "0 1px 3px rgba(0,0,0,0.1)"
            }
        }, [
            m("h3", {
                style: {
                    color: "#4a5568",
                    marginBottom: "0.5rem",
                    fontSize: "1rem",
                    fontFamily: "monospace"
                }
            }, title),
            m("div.stat-value", {
                style: {
                    color,
                    fontSize: "2rem",
                    fontWeight: "bold",
                    marginBottom: "0.5rem"
                }
            }, data.count),
            m("div.stat-trend", {
                style: {
                    color: data.status === 'up' ? '#48BB78' : '#F56565',
                    fontSize: "0.875rem",
                    display: "flex",
                    alignItems: "center"
                }
            }, [
                m("i.octicon", {
                    class: data.status === 'up' ? 'octicon-arrow-up' : 'octicon-arrow-down',
                    style: { marginRight: "0.25rem" }
                }),
                data.trend
            ])
        ]);
    }
};

// components/MonitorBar.js
const MonitorBar = {
    view: function({attrs}) {
        const {label, value, color} = attrs;
        return m("div.monitor-item", {
            style: {
                background: "#f7fafc",
                padding: "1rem",
                borderRadius: "0.375rem"
            }
        }, [
            m("div.monitor-label", {
                style: {
                    color: "#4a5568",
                    marginBottom: "0.5rem",
                    fontFamily: "monospace"
                }
            }, `${label}: ${value}%`),
            m("div.progress-bar", {
                    style: {
                        height: "0.5rem",
                        background: "#edf2f7",
                        borderRadius: "0.25rem",
                        overflow: "hidden"
                    }
                },
                m("div.progress", {
                    style: {
                        width: `${value}%`,
                        height: "100%",
                        background: color,
                        transition: "width 0.3s ease"
                    }
                })
            )
        ]);
    }
};

// components/ActivityItem.js
const ActivityItem = {
    view: function({attrs}) {
        const {activity} = attrs;
        const typeColors = {
            project: "#9F7AEA",
            post: "#4FD1C5",
            code: "#68D391",
            package: "#F6AD55"
        };

        return m("div.activity-item", {
            style: {
                padding: "0.75rem",
                borderBottom: "1px solid #edf2f7",
                display: "flex",
                alignItems: "center",
                gap: "1rem"
            }
        }, [
            m("div.activity-icon", {
                style: {
                    width: "2rem",
                    height: "2rem",
                    borderRadius: "50%",
                    background: typeColors[activity.type],
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    color: "#ffffff"
                }
            }, m("i.octicon", { class: `octicon-${activity.type}` })),
            m("div.activity-details", {
                style: {
                    flex: 1
                }
            }, [
                m("div.activity-main", {
                    style: {
                        color: "#2d3748",
                        fontFamily: "monospace"
                    }
                }, `${activity.name} ${activity.action}`),
                m("div.activity-time", {
                    style: {
                        color: "#718096",
                        fontSize: "0.875rem"
                    }
                }, activity.time)
            ])
        ]);
    }
};

// components/SystemMonitor.js
const SystemMonitor = {
    view: function({attrs}) {
        const {status} = attrs;
        return m("div.system-monitor", {
            style: {
                background: "#ffffff",
                borderRadius: "0.5rem",
                padding: "1.5rem",
                marginBottom: "2rem",
                boxShadow: "0 1px 3px rgba(0,0,0,0.1)"
            }
        }, [
            m("h3", {
                style: {
                    color: "#2d3748",
                    marginBottom: "1rem",
                    fontFamily: "monospace"
                }
            }, [
                m("i.octicon.octicon-pulse", { style: { marginRight: "0.5rem" } }),
                "System Monitor"
            ]),
            m("div.monitor-grid", {
                style: {
                    display: "grid",
                    gridTemplateColumns: "repeat(auto-fit, minmax(200px, 1fr))",
                    gap: "1rem"
                }
            }, [
                m(MonitorBar, {label: "CPU", value: status.cpu, color: "#4299E1"}),
                m(MonitorBar, {label: "Memory", value: status.memory, color: "#48BB78"}),
                m(MonitorBar, {label: "Storage", value: status.storage, color: "#ED8936"}),
                m(MonitorBar, {label: "Network", value: status.network, color: "#9F7AEA"})
            ])
        ]);
    }
};


module.exports = { 
    StatCard,
    MonitorBar,
    ActivityItem,
    SystemMonitor
};