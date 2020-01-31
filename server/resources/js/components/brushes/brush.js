export default class Brush {
    constructor(params) {
        this._params = { ...params };
        this.type = null;
    }

    handler (canvas, context, newPoint) {
        console.log("Handling stroke", newPoint);
    }

    get params() {
        return this._params;
    }

    set params(params) {
        this._params = { ...params };

        return this;
    }
};
