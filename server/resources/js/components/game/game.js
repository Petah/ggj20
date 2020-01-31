import tmpl from "./game.tmpl.html";
import PaintBrush from "../../common/brushes/paint_brush";
import colors from "../../common/colors";

const GameController = function($scope) {
    const $ctrl = this;

    $ctrl.brush = {
        colour: colors.purple,
        latestPoint: [],
        strokeWidth: 25,
        selected: new PaintBrush(),
    };
    
    $scope.$on('brushEndStroke', (event, point) => {
        $ctrl.brush.latestPoint = point;
    });

    $scope.$on('brushContinueStroke', (event, canvas, context, point) => {
        $ctrl.brush.selected.handler(canvas, context, $ctrl.brush.latestPoint, point, $ctrl.brush.color);
        $ctrl.brush.latestPoint = point;
    });
    
    $scope.$on('brushEndStroke', (event) => {
        console.log('Stroke ended', event);
    });
    
    $scope.$on('changeColor', (event, color) => {
        console.log('BRUSH COLOR');
        $ctrl.brush.color = color;
    });

    $ctrl.$onInit = () => {
        console.log('Game Loaded.');
    };
};

GameController.$inject = ["$scope"];

const Game = {
    template: tmpl,
    controller: GameController,
};

export default Game;
