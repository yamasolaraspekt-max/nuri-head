import { Connector } from "laravel-echo";
import { io } from "socket.io-client";

export default class ReverbConnector extends Connector {
    constructor(options) {
        super(options);
        this.options = options;
        this.connect();
    }

    connect() {
        this.socket = this.options.client(this.options);
    }

    channel(name) {
        if (!this.channels[name]) {
            this.channels[name] = {
                listen: (event, callback) => {
                    this.socket.on(`broadcasting:${name}:${event}`, callback);
                    return this.channels[name];
                },
                whisper: (event, data) => {
                    this.socket.emit(`client-${event}`, data);
                    return this.channels[name];
                },
                leave: () => {
                    this.socket.emit("unsubscribe", { channel: name });
                },
            };
            this.socket.emit("subscribe", { channel: name });
        }
        return this.channels[name];
    }

    privateChannel(name) {
        return this.channel(`private-${name}`);
    }

    presenceChannel(name) {
        return this.channel(`presence-${name}`);
    }

    leave(name) {
        if (this.channels[name]) {
            this.channels[name].leave();
            delete this.channels[name];
        }
    }

    socketId() {
        return this.socket.id;
    }

    disconnect() {
        this.socket.disconnect();
    }
}
