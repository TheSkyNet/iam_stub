const SEOTools = {
    loading: false,
    message: null,
    easyMDE: null,

    view: function() {
        return m("div.seo-page", {
            style: {
                background: '#fff',
                borderRadius: '8px',
                padding: '1.5rem',
                boxShadow: '0 2px 4px rgba(0,0,0,0.05)'
            }
        }, [
            // Header
            m("div.header", {
                style: {
                    display: 'flex',
                    alignItems: 'center',
                    marginBottom: '2rem'
                }
            }, [
                m("h1", {
                    style: {
                        color: '#9BA5D0',
                        fontSize: '2rem',
                        margin: 0
                    }
                }, [
                    m("i.octicon.octicon-search", {
                        style: { marginRight: '0.75rem' }
                    }),
                    "SEO & Bot Management Tools"
                ])
            ]),

            // Message Display
            this.message && m("div.alert", {
                class: this.message.type === 'success' ? 'alert-success' : 'alert-danger',
                style: {
                    marginBottom: '1rem'
                }
            }, this.message.text),

            // Tools Grid
            m("div.row", [
                // File Generation Tools
                m("div.col-12", [
                    m("div.card", {
                        style: {
                            background: '#F8F9FE',
                            border: '1px solid #E6E9F4',
                            borderRadius: '6px',
                            marginBottom: '2rem'
                        }
                    }, [
                        m("div.card-header", {
                            style: {
                                background: '#E6E9F4',
                                color: '#6B7399',
                                padding: '1rem'
                            }
                        }, "SEO File Generation Tools"),
                        m("div.card-body", {
                            style: { padding: '1.5rem' }
                        }, [
                            // LLMs.txt Generator (First Option)
                            m("button.btn.btn-block.mb-4", {
                                style: {
                                    background: '#8E96C8',
                                    color: '#fff',
                                    border: 'none',
                                    padding: '1rem',
                                    borderRadius: '4px',
                                    width: '100%',
                                    marginBottom: '1rem',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    gap: '0.5rem'
                                },
                                onclick: () => {
                                    SEOTools.generateFile('llms');
                                }
                            }, [
                                m("i.octicon.octicon-hubot", { style: { fontSize: '1.2rem' } }),
                                m("div", { style: { textAlign: 'left' } }, [
                                    m("div", "Generate llms.txt"),
                                    m("small.d-block", { style: { opacity: 0.9 } },
                                        "AI/LLM crawler permissions and policies"
                                    )
                                ])
                            ]),

                            // Other Generators
                            m("div.row", [
                                // Sitemap Generator
                                m("div.col-md-6.mb-3",
                                    m("button.btn.btn-block", {
                                        style: {
                                            background: '#9BA5D0',
                                            color: '#fff',
                                            border: 'none',
                                            padding: '1rem',
                                            borderRadius: '4px',
                                            width: '100%',
                                            height: '100%',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            gap: '0.5rem'
                                        },
                                        onclick: () => SEOTools.generateFile('sitemap')
                                    }, [
                                        m("i.octicon.octicon-file-code"),
                                        m("div", { style: { textAlign: 'left' } }, [
                                            m("div", "Generate sitemap.xml"),
                                            m("small.d-block", { style: { opacity: 0.9 } },
                                                "Site structure for search engines"
                                            )
                                        ])
                                    ])
                                ),

                                // Robots.txt Generator
                                m("div.col-md-6.mb-3",
                                    m("button.btn.btn-block", {
                                        style: {
                                            background: '#9BA5D0',
                                            color: '#fff',
                                            border: 'none',
                                            padding: '1rem',
                                            borderRadius: '4px',
                                            width: '100%',
                                            height: '100%',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            gap: '0.5rem'
                                        },
                                        onclick: () => SEOTools.generateFile('robots')
                                    }, [
                                        m("i.octicon.octicon-file"),
                                        m("div", { style: { textAlign: 'left' } }, [
                                            m("div", "Generate robots.txt"),
                                            m("small.d-block", { style: { opacity: 0.9 } },
                                                "Crawler access control"
                                            )
                                        ])
                                    ])
                                ),

                                // Humans.txt Generator
                                m("div.col-md-6.mb-3",
                                    m("button.btn.btn-block", {
                                        style: {
                                            background: '#9BA5D0',
                                            color: '#fff',
                                            border: 'none',
                                            padding: '1rem',
                                            borderRadius: '4px',
                                            width: '100%',
                                            height: '100%',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            gap: '0.5rem'
                                        },
                                        onclick: () => SEOTools.generateFile('humans')
                                    }, [
                                        m("i.octicon.octicon-person"),
                                        m("div", { style: { textAlign: 'left' } }, [
                                            m("div", "Generate humans.txt"),
                                            m("small.d-block", { style: { opacity: 0.9 } },
                                                "Credits and team information"
                                            )
                                        ])
                                    ])
                                ),

                                // Security.txt Generator
                                m("div.col-md-6.mb-3",
                                    m("button.btn.btn-block", {
                                        style: {
                                            background: '#9BA5D0',
                                            color: '#fff',
                                            border: 'none',
                                            padding: '1rem',
                                            borderRadius: '4px',
                                            width: '100%',
                                            height: '100%',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            gap: '0.5rem'
                                        },
                                        onclick: () => SEOTools.generateFile('security')
                                    }, [
                                        m("i.octicon.octicon-shield"),
                                        m("div", { style: { textAlign: 'left' } }, [
                                            m("div", "Generate security.txt"),
                                            m("small.d-block", { style: { opacity: 0.9 } },
                                                "Security policy information"
                                            )
                                        ])
                                    ])
                                )
                            ])
                        ])
                    ])
                ]),

                // SEO Analysis Tools
                m("div.col-12", [
                    m("div.card", {
                        style: {
                            background: '#F8F9FE',
                            border: '1px solid #E6E9F4',
                            borderRadius: '6px'
                        }
                    }, [
                        m("div.card-header", {
                            style: {
                                background: '#E6E9F4',
                                color: '#6B7399',
                                padding: '1rem'
                            }
                        }, "SEO Analysis Tools"),
                        m("div.card-body", {
                            style: { padding: '1.5rem' }
                        }, [
                            m("div.row", [
                                // Meta Tags Analysis
                                m("div.col-md-4.mb-3",
                                    m("button.btn.btn-block", {
                                        style: {
                                            background: '#9BA5D0',
                                            color: '#fff',
                                            border: 'none',
                                            padding: '1rem',
                                            borderRadius: '4px',
                                            width: '100%',
                                            height: '100%',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            gap: '0.5rem'
                                        },
                                        onclick: () => SEOTools.analyzeSEO('meta')
                                    }, [
                                        m("i.octicon.octicon-search"),
                                        m("div", { style: { textAlign: 'left' } }, [
                                            m("div", "Analyze Meta Tags"),
                                            m("small.d-block", { style: { opacity: 0.9 } },
                                                "Check meta titles and descriptions"
                                            )
                                        ])
                                    ])
                                ),

                                // Image Analysis
                                m("div.col-md-4.mb-3",
                                    m("button.btn.btn-block", {
                                        style: {
                                            background: '#9BA5D0',
                                            color: '#fff',
                                            border: 'none',
                                            padding: '1rem',
                                            borderRadius: '4px',
                                            width: '100%',
                                            height: '100%',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            gap: '0.5rem'
                                        },
                                        onclick: () => SEOTools.analyzeSEO('images')
                                    }, [
                                        m("i.octicon.octicon-image"),
                                        m("div", { style: { textAlign: 'left' } }, [
                                            m("div", "Analyze Images"),
                                            m("small.d-block", { style: { opacity: 0.9 } },
                                                "Check alt tags and optimization"
                                            )
                                        ])
                                    ])
                                ),

                                // Content Analysis
                                m("div.col-md-4.mb-3",
                                    m("button.btn.btn-block", {
                                        style: {
                                            background: '#9BA5D0',
                                            color: '#fff',
                                            border: 'none',
                                            padding: '1rem',
                                            borderRadius: '4px',
                                            width: '100%',
                                            height: '100%',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            gap: '0.5rem'
                                        },
                                        onclick: () => SEOTools.analyzeSEO('content')
                                    }, [
                                        m("i.octicon.octicon-file-text"),
                                        m("div", { style: { textAlign: 'left' } }, [
                                            m("div", "Analyze Content"),
                                            m("small.d-block", { style: { opacity: 0.9 } },
                                                "Check headings and structure"
                                            )
                                        ])
                                    ])
                                )
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    },

    generateFile: function(type) {
        SEOTools.loading = true;
        return m.request({
            method: "POST",
            url: `/api/v1/seo/generate/${type}`,
            withCredentials: true
        })
            .then(result => {
                if (!result.success) {
                    throw new Error(result.message);
                }
                SEOTools.message = {
                    type: 'success',
                    text: result.message || `${type} file generated successfully!`
                };
            })
            .catch(error => {
                SEOTools.message = {
                    type: 'error',
                    text: error.message || `Error generating ${type} file`
                };
            })
            .finally(() => {
                setTimeout(() => {
                    SEOTools.message = null;
                    m.redraw();
                }, 30000);
                SEOTools.loading = false;
                m.redraw();
            });
    },
    analyzeSEO: function(type) {
        SEOTools.loading = true;
        return m.request({
            method: "POST",
            url: `/api/v1/seo/analyze/${type}`,
            withCredentials: true
        })
            .then(result => {
                SEOTools.message = { type: 'success', text: `${type} analysis completed!` };
                setTimeout(() => { SEOTools.message = null; m.redraw(); }, 3000);
            })
            .catch(error => {
                SEOTools.message = { type: 'error', text: `Error during ${type} analysis: ${error.message}` };
                setTimeout(() => { SEOTools.message = null; m.redraw(); }, 3000);
            })
            .finally(() => {
                SEOTools.loading = false;
                m.redraw();
            });
    }
};

module.exports = { SEOTools };