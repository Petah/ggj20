class DeviceService {
    constructor() {
        this._userAgent = navigator.userAgent;
    }

    get isPad() {
        return /iPad|iPhone|iPod/.test(this.userAgent) && !window.MSStream;
    }

    get userAgent() {
        return this._userAgent;
    }
}

DeviceService.$inject = [];

export default DeviceService;
