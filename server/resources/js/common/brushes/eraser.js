import Brush from "./brush";

export default class Eraser extends Brush {
    constructor(params) {
        super(params);
        this.type = "eraser";
    }

    handler (canvas, context, oldPoint, newPoint, color, strokeWidth) {
        console.log("Erasing", newPoint);
    }
}
