import tmpl from "./game.tmpl.html";
import PaintBrush from "../../common/brushes/paint_brush";
import colors from "../../common/colors";

const GameController = function() {
    const $ctrl = this;

    $ctrl.brush = {
        colour: colors.purple.hex,
        latestPoint: null,
        strokeWidth: 25,
        selected: new PaintBrush(),
    };

    $ctrl.onPaletteColorChange = color => {
        $ctrl.brush.color = color;
    };

    $ctrl.onBrushStartStroke = point => {
        $ctrl.brush.latestPoint = point;
    };

    $ctrl.onBrushContinueStroke = (canvas, context, point) => {
        $ctrl.brush.selected.handler(canvas, context, $ctrl.brush.latestPoint, point);
        $ctrl.brush.latestPoint = point;
    };

    $ctrl.onBrushEndStroke = event => {
        console.log('Stroke ended', event);
    };

    $ctrl.$onInit = () => {
        console.log('Game Loaded.');
    };
};

GameController.$inject = [];

const Game = {
    template: tmpl,
    controller: GameController,
};

export default Game;
