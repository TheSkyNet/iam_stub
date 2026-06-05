// WebSocketService.js - Native WebSocket client for IamLab
export const WebSocketService = {
    socket: null,
    status: "disconnected",
    messages: [],
    onMessageCallback: null,

    connect(host = window.location.hostname, port = 8081) {
        if (this.socket) {
            this.socket.close();
        }

        this.status = "connecting";
        const protocol = window.location.protocol === "https:" ? "wss:" : "ws:";
        
        // If we are on a port that is likely being proxied (not 80, 443, 8081), 
        // or if we want to support out-of-the-box Nginx proxying,
        // we can try to connect to /ws on the current port first.
        let url;
        if (window.location.port && window.location.port !== "80" && window.location.port !== "443" && window.location.port !== "8081") {
            // Likely a proxied environment (like Nginx on 8080)
            url = `${protocol}//${host}:${window.location.port}/ws`;
        } else {
            url = `${protocol}//${host}:${port}`;
        }

        this.socket = new WebSocket(url);

        this.socket.onopen = () => {
            console.log("WebSocket Connected");
            this.status = "connected";
            m.redraw();
        };

        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.messages.push(data);
            if (this.onMessageCallback) {
                this.onMessageCallback(data);
            }
            m.redraw();
        };

        this.socket.onclose = () => {
            console.log("WebSocket Disconnected");
            this.status = "disconnected";
            m.redraw();
        };

        this.socket.onerror = (error) => {
            console.error("WebSocket Error:", error);
            this.status = "error";
            m.redraw();
        };
    },

    send(message) {
        if (this.socket && this.status === "connected") {
            this.socket.send(JSON.stringify({ message }));
        }
    },

    disconnect() {
        if (this.socket) {
            this.socket.close();
        }
    },

    onMessage(callback) {
        this.onMessageCallback = callback;
    }
};
