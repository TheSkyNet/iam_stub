# Frontend Services Approach

This document describes the recommended approach for using services in Mithril.js components within the IamLab project, as exemplified by `EditUserPage.js`.

## Service Definition

Services should be defined as classes (or constructors) that encapsulate API calls using `m.request`. This allows for instantiation within components, providing each component with its own service instance.

### Example Service (`assets/js/services/UsersService.js`)

```javascript
import m from "mithril";
import { AuthService } from "./AuthserviceService";

export class UsersService {
    constructor() {
        this.baseUrl = '/api/users';
    }

    getAuthHeaders() {
        return AuthService.getAuthHeaders();
    }

    get(id) {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/${id}`,
            headers: this.getAuthHeaders()
        });
    }

    update(id, data) {
        return m.request({
            method: 'PUT',
            url: `${this.baseUrl}/${id}`,
            body: data,
            headers: this.getAuthHeaders()
        });
    }
}
```

## Component Usage

Components should instantiate the required services during the `oninit` lifecycle hook.

### Example Component (`assets/js/pages/Admin/EditUserPage.js`)

```javascript
import { UsersService } from "../../services/UsersService";

const EditUserPage = {
    usersService: null,
    user: null,

    oninit: function(vnode) {
        this.usersService = new UsersService();
        this.loadUser(vnode.attrs.id);
    },

    loadUser: function(id) {
        return this.usersService.get(id).then((response) => {
            if (response.success) {
                this.user = response.data;
            } else {
                window.showToast(response, "error");
            }
        });
    },

    save: function() {
        this.saving = true;
        this.usersService.update(this.user.id, this.user)
            .then((response) => {
                if (response.success) {
                    window.showToast("Updated successfully", "success");
                } else {
                    window.showToast(response, "error");
                }
                this.saving = false;
            })
            .catch((err) => {
                // IMPORTANT: This is how to show toasts for caught errors
                window.showToast(err.response, "error");
                this.saving = false;
            });
    }
};
```

## Error Handling and Toasts

### The `.catch` Pattern

When a service call fails (e.g., due to a network error or a non-2xx/3xx response), the promise is rejected. To show a human-readable error message from the response, use `window.showToast(err.response, "error")` in the `.catch` block.

```javascript
this.someService.doAction(data)
    .then((response) => {
        // Handle success
    })
    .catch((err) => {
        // Show error message from response
        window.showToast(err.response, "error");
        this.loading = false;
    });
```

The `window.showToast` function is part of the global error handling system (see `ERROR_SERVICE.md`) and automatically formats the error into a user-friendly message.

### Why `err.response`?

Mithril's `m.request` rejects the promise with the response body when an error occurs. Depending on your backend implementation and how the error is captured, `err.response` or `err` itself might contain the error message. Using `err.response` is the convention established in this project's updated components.
