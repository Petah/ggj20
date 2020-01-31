import Brush from "./brush";

export default class Eraser extends Brush {
    constructor(params) {
        super(params);
        this.type = "eraser";
    }

    handler (canvas, context, newPoint) {
        console.log("Erasing", newPoint);
    }
}
