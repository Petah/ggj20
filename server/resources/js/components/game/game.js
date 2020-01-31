import tmpl from "./game.tmpl.html";
import PaintBrush from "../brushes/paint_brush";
import Eraser from "../brushes/eraser";

const GameController = function() {
    const $ctrl = this;

    $ctrl.brush = {
        colour: "#3d34a5",
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
        $ctrl.brush.selected.handler(canvas, context, $ctrl.brush, point);
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
