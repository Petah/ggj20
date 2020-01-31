import Brush from './brush';
import colors from '../../common/colors';

export default class PaintBrush extends Brush {
    constructor(params) {
        super(params);
        this.type = 'paint_brush';
    }

    handler(canvas, context, oldPoint, newPoint, color, strokeWidth) {
        context.beginPath();
        context.moveTo(oldPoint[0], oldPoint[1]);
        context.strokeStyle = color ? color.hex : colors.purple.hex;
        context.lineWidth = strokeWidth || 25;
        context.lineCap = 'round';
        context.lineJoin = 'round';
        context.lineTo(newPoint[0], newPoint[1]);
        context.stroke();
    }
}
