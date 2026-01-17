import m from "mithril";
import { Icon } from "../components/Icon";
import { AuthService } from "../services/AuthserviceService";

const ProfilePage = {
    activeTab: 'general',
    
    // Profile Data from Server
    profileData: {
        name: '',
        email: '',
        avatar: null,
        oauth_provider: null,
        apiKey: '',
        isLoading: true
    },

    // Form States
    general: {
        name: '',
        email: '',
        isLoading: false
    },

    security: {
        oldPassword: '',
        newPassword: '',
        confirmPassword: '',
        isLoading: false
    },

    developer: {
        isLoading: false
    },

    oninit: () => {
        ProfilePage.loadProfile();
    },

    loadProfile: () => {
        ProfilePage.profileData.isLoading = true;
        AuthService.getProfile().then(response => {
            if (response.success && response.data) {
                const data = response.data;
                ProfilePage.profileData = {
                    ...data,
                    apiKey: data.api_key,
                    isLoading: false
                };
                ProfilePage.general.name = data.name || '';
                ProfilePage.general.email = data.email || '';
            }
        }).finally(() => {
            ProfilePage.profileData.isLoading = false;
            m.redraw();
        });
    },

    handleUpdateProfile: (e) => {
        e.preventDefault();
        ProfilePage.general.isLoading = true;
        
        AuthService.updateProfile({
            name: ProfilePage.general.name,
            email: ProfilePage.general.email
        }).then(response => {
            if (response.success) {
                window.showToast(response.message, "success");
                // Update local user data
                const user = AuthService.getUser();
                if (user) {
                    user.name = ProfilePage.general.name;
                    user.email = ProfilePage.general.email;
                    localStorage.setItem('user', JSON.stringify(user));
                    AuthService.user = user;
                }
                ProfilePage.profileData.name = ProfilePage.general.name;
                ProfilePage.profileData.email = ProfilePage.general.email;
            }
        }).finally(() => {
            ProfilePage.general.isLoading = false;
            m.redraw();
        });
    },

    handleChangePassword: (e) => {
        e.preventDefault();
        ProfilePage.security.isLoading = true;
        
        AuthService.changePassword(
            ProfilePage.security.oldPassword,
            ProfilePage.security.newPassword,
            ProfilePage.security.confirmPassword
        ).then(response => {
            if (response.success) {
                window.showToast(response.message, "success");
                ProfilePage.security.oldPassword = '';
                ProfilePage.security.newPassword = '';
                ProfilePage.security.confirmPassword = '';
            }
        }).finally(() => {
            ProfilePage.security.isLoading = false;
            m.redraw();
        });
    },

    handleGenerateApiKey: () => {
        ProfilePage.developer.isLoading = true;
        AuthService.generateApiKey().then(response => {
            if (response.success) {
                window.showToast(response.message, "success");
                ProfilePage.profileData.apiKey = response.data.api_key;
            }
        }).finally(() => {
            ProfilePage.developer.isLoading = false;
            m.redraw();
        });
    },

    handleUnlinkOAuth: () => {
        const provider = ProfilePage.profileData.oauth_provider;
        if (!provider) return;

        if (confirm(`Are you sure you want to unlink your ${provider} account?`)) {
            AuthService.unlinkOAuth(provider).then(response => {
                if (response.success) {
                    window.showToast(response.message, "success");
                    ProfilePage.profileData.oauth_provider = null;
                }
            });
        }
    },

    view: () => {
        if (ProfilePage.profileData.isLoading) {
            return m(".container.mx-auto.p-4.flex.justify-center.items-center.min-h-screen", [
                m("span.loading.loading-spinner.loading-lg")
            ]);
        }

        // Helper for active tab class
        const getTabClass = (tab) => {
            let base = "tab";
            if (ProfilePage.activeTab === tab) base += " tab-active";
            return base;
        };

        // Tab Content: General
        let oauthSection = null;
        if (ProfilePage.profileData.oauth_provider) {
            oauthSection = m(".mt-8.pt-8.border-t", [
                m("h3.text-lg.font-semibold.mb-4", "Connected Accounts"),
                m(".flex.items-center.justify-between.bg-base-200.p-4.rounded-lg", [
                    m(".flex.items-center.gap-3", [
                        m(Icon, { icon: `fa-brands fa-${ProfilePage.profileData.oauth_provider}` }),
                        m("span", [
                            "Connected via ",
                            m("span.font-bold", ProfilePage.profileData.oauth_provider.charAt(0).toUpperCase() + ProfilePage.profileData.oauth_provider.slice(1))
                        ])
                    ]),
                    m("button.btn.btn-outline.btn-error.btn-sm", {
                        onclick: ProfilePage.handleUnlinkOAuth
                    }, "Unlink")
                ])
            ]);
        }

        let generalContent = m(".p-6", [
            m("form", { onsubmit: ProfilePage.handleUpdateProfile }, [
                m(".form-control.mb-4", [
                    m("label.label", m("span.label-text", "Full Name")),
                    m("input.input.input-bordered", {
                        type: "text",
                        value: ProfilePage.general.name,
                        oninput: (e) => ProfilePage.general.name = e.target.value,
                        required: true
                    })
                ]),
                m(".form-control.mb-6", [
                    m("label.label", m("span.label-text", "Email Address")),
                    m("input.input.input-bordered", {
                        type: "email",
                        value: ProfilePage.general.email,
                        oninput: (e) => ProfilePage.general.email = e.target.value,
                        required: true
                    })
                ]),
                m("button.btn.btn-primary", {
                    type: "submit",
                    disabled: ProfilePage.general.isLoading
                }, [
                    ProfilePage.general.isLoading && m("span.loading.loading-spinner"),
                    m(Icon, { icon: "fa-solid fa-save" }),
                    " Save Changes"
                ])
            ]),
            oauthSection
        ]);

        // Tab Content: Security
        let securityContent = m(".p-6", [
            m("form", { onsubmit: ProfilePage.handleChangePassword }, [
                m(".form-control.mb-4", [
                    m("label.label", m("span.label-text", "Current Password")),
                    m("input.input.input-bordered", {
                        type: "password",
                        value: ProfilePage.security.oldPassword,
                        oninput: (e) => ProfilePage.security.oldPassword = e.target.value,
                        required: true
                    })
                ]),
                m(".form-control.mb-4", [
                    m("label.label", m("span.label-text", "New Password")),
                    m("input.input.input-bordered", {
                        type: "password",
                        value: ProfilePage.security.newPassword,
                        oninput: (e) => ProfilePage.security.newPassword = e.target.value,
                        required: true
                    })
                ]),
                m(".form-control.mb-6", [
                    m("label.label", m("span.label-text", "Confirm New Password")),
                    m("input.input.input-bordered", {
                        type: "password",
                        value: ProfilePage.security.confirmPassword,
                        oninput: (e) => ProfilePage.security.confirmPassword = e.target.value,
                        required: true
                    })
                ]),
                m("button.btn.btn-warning", {
                    type: "submit",
                    disabled: ProfilePage.security.isLoading
                }, [
                    ProfilePage.security.isLoading && m("span.loading.loading-spinner"),
                    m(Icon, { icon: "fa-solid fa-key" }),
                    " Update Password"
                ])
            ])
        ]);

        // Tab Content: Developer
        let developerContent = m(".p-6", [
            m(".alert.alert-info.mb-6", [
                m(Icon, { icon: "fa-solid fa-circle-info" }),
                m("span", "Your API key allows you to access our services programmatically. Keep it secret!")
            ]),
            m(".form-control.mb-6", [
                m("label.label", m("span.label-text", "API Key")),
                m(".flex.gap-2", [
                    m("input.input.input-bordered.flex-grow", {
                        type: "text",
                        value: ProfilePage.profileData.apiKey || "No API key generated",
                        readonly: true
                    }),
                    m("button.btn.btn-secondary", {
                        onclick: ProfilePage.handleGenerateApiKey,
                        disabled: ProfilePage.developer.isLoading
                    }, [
                        ProfilePage.developer.isLoading && m("span.loading.loading-spinner"),
                        m(Icon, { icon: "fa-solid fa-sync" }),
                        " Regenerate"
                    ])
                ])
            ])
        ]);

        // Active tab content helper
        let activeContent;
        if (ProfilePage.activeTab === 'general') {
            activeContent = generalContent;
        } else if (ProfilePage.activeTab === 'security') {
            activeContent = securityContent;
        } else if (ProfilePage.activeTab === 'developer') {
            activeContent = developerContent;
        }

        let avatarContent;
        const profileName = ProfilePage.profileData.name || "User";
        if (ProfilePage.profileData.avatar) {
            avatarContent = m("img", { key: "avatar-img", src: ProfilePage.profileData.avatar, alt: profileName });
        } else {
            avatarContent = m("span.text-2xl", { key: "avatar-initials" }, profileName.charAt(0).toUpperCase());
        }

        return m(".container.mx-auto.p-4.max-w-4xl", { key: "profile-container" }, [
            m(".flex.items-center.gap-4.mb-8", [
                m(".avatar.placeholder", [
                    m(".bg-neutral.text-neutral-content.rounded-full.w-20", { key: "avatar-wrapper" }, avatarContent)
                ]),
                m("div", [
                    m("h1.text-3xl.font-bold", profileName),
                    m("p.text-base-content.opacity-70", ProfilePage.profileData.email)
                ])
            ]),

            m(".card.bg-base-100.shadow-xl.overflow-hidden", { key: "profile-card" }, [
                m(".card-body.p-0", [
                    m(".tabs.tabs-lifted.w-full", [
                        m("button", { 
                            key: "tab-gen",
                            class: getTabClass('general'), 
                            onclick: () => ProfilePage.activeTab = 'general' 
                        }, [
                            m(Icon, { icon: "fa-solid fa-id-card", class: "mr-2" }),
                            "General"
                        ]),
                        m("button", { 
                            key: "tab-sec",
                            class: getTabClass('security'), 
                            onclick: () => ProfilePage.activeTab = 'security' 
                        }, [
                            m(Icon, { icon: "fa-solid fa-shield-halved", class: "mr-2" }),
                            "Security"
                        ]),
                        m("button", { 
                            key: "tab-dev",
                            class: getTabClass('developer'), 
                            onclick: () => ProfilePage.activeTab = 'developer' 
                        }, [
                            m(Icon, { icon: "fa-solid fa-code", class: "mr-2" }),
                            "Developer"
                        ])
                    ]),
                    m("div", { key: `tab-content-${ProfilePage.activeTab}` }, activeContent)
                ])
            ])
        ]);
    }
};

export default ProfilePage;
