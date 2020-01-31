import Brush from "./brush";

export default class PaintBrush extends Brush {
    constructor(params) {
        super(params);
        this.type = "paint_brush";
    }

    handler (canvas, context, brush, newPoint) {
        context.beginPath();
        context.moveTo(brush.latestPoint[0], brush.latestPoint[1]);
        context.strokeStyle = brush.colour;
        context.lineWidth = brush.strokeWidth;
        context.lineCap = "round";
        context.lineJoin = "round";
        context.lineTo(newPoint[0], newPoint[1]);
        context.stroke();

        brush.latestPoint = newPoint;
    }
}
