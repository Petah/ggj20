export default class Brush {
    constructor(params) {
        this._params = { ...params };
        this.type = null;
    }

    handler (canvas, context, oldPoint, newPoint, color, strokeWidth) {
        console.log("Handling stroke", newPoint, color, strokeWidth);
    }

    get params() {
        return this._params;
    }

    set params(params) {
        this._params = { ...params };

        return this;
    }
};
