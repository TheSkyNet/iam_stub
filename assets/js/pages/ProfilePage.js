import m from "mithril";
import { Icon } from "../components/Icon";
import { AuthService } from "../services/AuthserviceService";
import { Fieldset, FormField, SubmitButton } from "../components/Form";

const ProfilePage = {
    // FilePond instance
    pond: null,

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

    onremove: () => {
        if (ProfilePage.pond) {
            ProfilePage.pond.destroy();
            ProfilePage.pond = null;
        }
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
        if (!provider) {
            return;
        }

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

        const profileName = ProfilePage.profileData.name || "User";
        
        let avatarContent;
        if (ProfilePage.profileData.avatar) {
            avatarContent = m("img", { src: ProfilePage.profileData.avatar, alt: profileName });
        } else {
            avatarContent = m("span.text-2xl", profileName.charAt(0).toUpperCase());
        }

        // Section: Personal Information
        let oauthSection = null;
        if (ProfilePage.profileData.oauth_provider) {
            const providerName = ProfilePage.profileData.oauth_provider.charAt(0).toUpperCase() + ProfilePage.profileData.oauth_provider.slice(1);
            oauthSection = m(".mt-8.pt-8.border-t.border-base-300", [
                m("h3.text-lg.font-semibold.mb-4", "Connected Accounts"),
                m(".flex.items-center.justify-between.bg-base-200.p-4.rounded-lg", [
                    m(".flex.items-center.gap-3", [
                        m(Icon, { icon: `fa-brands fa-${ProfilePage.profileData.oauth_provider}` }),
                        m("span", [
                            "Connected via ",
                            m("span.font-bold", providerName)
                        ])
                    ]),
                    m("button.btn.btn-outline.btn-error.btn-sm", {
                        onclick: ProfilePage.handleUnlinkOAuth
                    }, "Unlink")
                ])
            ]);
        }

        const personalCard = m(".card.bg-base-100.shadow-xl.mb-8", [
            m(".card-body", [
                m("h2.card-title.mb-4", [
                    m(Icon, { icon: "fa-solid fa-id-card" }),
                    "Personal Information"
                ]),
                m("form", { onsubmit: ProfilePage.handleUpdateProfile }, [
                    m(FormField, {
                        label: "Full Name",
                        icon: "fa-solid fa-user",
                        value: ProfilePage.general.name,
                        oninput: (e) => ProfilePage.general.name = e.target.value,
                        required: true
                    }),
                    m(FormField, {
                        label: "Email Address",
                        icon: "fa-solid fa-envelope",
                        type: "email",
                        value: ProfilePage.general.email,
                        oninput: (e) => ProfilePage.general.email = e.target.value,
                        required: true
                    }),
                    m(SubmitButton, {
                        class: "btn-primary mt-6",
                        loading: ProfilePage.general.isLoading,
                        icon: "fa-solid fa-save"
                    }, " Save Changes")
                ]),
                oauthSection
            ])
        ]);

        // Section: Security
        const securityCard = m(".card.bg-base-100.shadow-xl.mb-8", [
            m(".card-body", [
                m("h2.card-title.mb-4", [
                    m(Icon, { icon: "fa-solid fa-shield-halved" }),
                    "Security"
                ]),
                m("form", { onsubmit: ProfilePage.handleChangePassword }, [
                    m(FormField, {
                        label: "Current Password",
                        icon: "fa-solid fa-lock",
                        type: "password",
                        value: ProfilePage.security.oldPassword,
                        oninput: (e) => ProfilePage.security.oldPassword = e.target.value,
                        required: true
                    }),
                    m(FormField, {
                        label: "New Password",
                        icon: "fa-solid fa-key",
                        type: "password",
                        value: ProfilePage.security.newPassword,
                        oninput: (e) => ProfilePage.security.newPassword = e.target.value,
                        required: true
                    }),
                    m(FormField, {
                        label: "Confirm New Password",
                        icon: "fa-solid fa-key",
                        type: "password",
                        value: ProfilePage.security.confirmPassword,
                        oninput: (e) => ProfilePage.security.confirmPassword = e.target.value,
                        required: true
                    }),
                    m(SubmitButton, {
                        class: "btn-warning mt-6",
                        loading: ProfilePage.security.isLoading,
                        icon: "fa-solid fa-shield-halved"
                    }, " Update Password")
                ])
            ])
        ]);

        // Section: Developer
        const developerCard = m(".card.bg-base-100.shadow-xl.mb-8", [
            m(".card-body", [
                m("h2.card-title.mb-4", [
                    m(Icon, { icon: "fa-solid fa-code" }),
                    "Developer Settings"
                ]),
                m(".alert.alert-info.mb-6", [
                    m(Icon, { icon: "fa-solid fa-circle-info" }),
                    m("span", "Your API key allows you to access our services programmatically. Keep it secret!")
                ]),
                m(FormField, {
                    label: "API Key",
                    icon: "fa-solid fa-key",
                    value: ProfilePage.profileData.apiKey || "No API key generated",
                    readonly: true
                }),
                m(SubmitButton, {
                    class: "btn-secondary mt-4",
                    onclick: ProfilePage.handleGenerateApiKey,
                    loading: ProfilePage.developer.isLoading,
                    icon: "fa-solid fa-sync"
                }, " Regenerate")
            ])
        ]);

        return m(".container.mx-auto.p-4.max-w-4xl", [
            // Profile Header
            m(".flex.flex-col.sm:flex-row.items-center.gap-6.mb-10.p-6.bg-base-100.rounded-2xl.shadow-sm", [
                m(".avatar.placeholder.cursor-pointer.relative.group", {
                    onclick: () => ProfilePage.pond && ProfilePage.pond.browse(),
                    title: "Click to change avatar"
                }, [
                    m(".bg-neutral.text-neutral-content.rounded-full.w-24.flex.items-center.justify-center.overflow-hidden.relative", [
                        avatarContent,
                        m(".absolute.inset-0.bg-black.bg-opacity-40.flex.items-center.justify-center.opacity-0.group-hover:opacity-100.transition-opacity", [
                            m(Icon, { icon: "fa-solid fa-camera", class: "text-white text-xl" })
                        ])
                    ]),
                    // Hidden FilePond input
                    m("input.hidden", {
                        type: "file",
                        oncreate: (vnode) => {
                            ProfilePage.pond = FilePond.create(vnode.dom, {
                                server: {
                                    process: '/api/v1/file',
                                    headers: AuthService.getAuthHeaders()
                                },
                                allowMultiple: false,
                                labelIdle: '',
                                acceptedFileTypes: ['image/*'],
                                onprocessfile: (error, file) => {
                                    if (error) {
                                        window.showToast("Upload failed", 'error');
                                        return;
                                    }
                                    AuthService.updateAvatar(file.serverId).then(response => {
                                        if (response.success) {
                                            window.showToast(response.message, 'success');
                                            ProfilePage.profileData.avatar = response.data.avatar;
                                            const user = AuthService.getUser();
                                            if (user) {
                                                user.avatar = response.data.avatar;
                                                localStorage.setItem('user', JSON.stringify(user));
                                                AuthService.user = user;
                                            }
                                            m.redraw();
                                        }
                                    }).catch(err => {
                                        window.showToast(err, 'error');
                                    });
                                }
                            });
                        }
                    })
                ]),
                m(".text-center.sm:text-left", [
                    m("h1.text-4xl.font-extrabold.mb-1", profileName),
                    m("p.text-lg.text-base-content.opacity-60", ProfilePage.profileData.email)
                ])
            ]),

            // Sections
            personalCard,
            securityCard,
            developerCard
        ]);
    }
};

export default ProfilePage;
