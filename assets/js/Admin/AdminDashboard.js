import { Terminal } from "./Dashbord/Terminal.js";
import {ActivityItem, StatCard, SystemMonitor} from "./Dashbord/StatCard";  // add .js extension

const AdminDashboard = {
    loading: true,
    stats: {
        projects: { count: 42, trend: '+15%', status: 'up' },
        posts: { count: 156, trend: '+8%', status: 'up' },
        packages: { count: 73, trend: '-3%', status: 'down' },
        codes: { count: 284, trend: '+12%', status: 'up' }
    },
    systemStatus: {
        cpu: 45,
        memory: 68,
        storage: 32,
        network: 76
    },
    recentActivity: [
        { type: 'project', action: 'created', name: 'Quantum Analysis', time: '2 mins ago' },
        { type: 'post', action: 'updated', name: 'Lab Results 2025', time: '15 mins ago' },
        { type: 'code', action: 'deleted', name: 'Legacy Algorithm', time: '1 hour ago' },
        { type: 'package', action: 'published', name: 'Data Processor v2', time: '3 hours ago' }
    ],

    oninit: function() {
        setTimeout(() => {
            this.loading = false;
            m.redraw();
        }, 1000);
    },

    view: function() {
        if (this.loading) {
            return m("div.loading-screen", {
                style: {
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    height: '100vh',
                    background: '#f0f4f8'
                }
            }, [
                m("div.spinner", {
                    style: {
                        width: '50px',
                        height: '50px',
                        border: '5px solid #e0e8f0',
                        borderTop: '5px solid #8198c4',
                        borderRadius: '50%',
                        animation: 'spin 1s linear infinite'
                    }
                })
            ]);
        }

        return m("div.dashboard", {
            style: {
                padding: "1.5rem",
                background: "#f0f4f8",
                minHeight: "100vh"
            }
        }, [
            // Stats Grid
            m("div.stats-grid", {
                style: {
                    display: "grid",
                    gridTemplateColumns: "repeat(auto-fit, minmax(250px, 1fr))",
                    gap: "1rem",
                    marginBottom: "2rem"
                }
            }, [
                m(StatCard, {title: "Projects", data: this.stats.projects, color: "#9F7AEA"}),
                m(StatCard, {title: "Posts", data: this.stats.posts, color: "#4FD1C5"}),
                m(StatCard, {title: "Packages", data: this.stats.packages, color: "#F6AD55"}),
                m(StatCard, {title: "Code Snippets", data: this.stats.codes, color: "#68D391"})
            ]),

            // System Monitor
            m(SystemMonitor, {status: this.systemStatus}),

            // Activity Log
            m("div.activity-log", {
                style: {
                    background: "#ffffff",
                    borderRadius: "0.5rem",
                    padding: "1.5rem",
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
                    m("i.octicon.octicon-history", { style: { marginRight: "0.5rem" } }),
                    "Recent Activity"
                ]),
                m("div.activity-list",
                    this.recentActivity.map(activity =>
                        m(ActivityItem, {activity})
                    )
                )
            ]),

            // Terminal
            m(Terminal, {
                title: "Laboratory Control Terminal",
                prompt: "lab>",
                commands: {
                    status: () => "All systems operational ✓",
                    experiments: () => "Currently running experiments: " + this.stats.projects.count,
                    uptime: () => "System uptime: 47 days, 13 hours",
                    stats: () => `Total Projects: ${this.stats.projects.count}\nTotal Posts: ${this.stats.posts.count}\nTotal Packages: ${this.stats.packages.count}\nTotal Code Snippets: ${this.stats.codes.count}`,
                },
                maxHeight: "400px"
            })
        ]);
    }
};

export default AdminDashboard;
