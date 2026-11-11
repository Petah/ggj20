import tmpl from './game.tmpl.html?raw';

const GameController = function ($scope) {
    const $ctrl = this;

    $ctrl.$onInit = () => {
        console.log('Game Loaded.');
    };

    $scope.$on('selectLevel', (event, level) => {
        console.log('Setting game level', level);
        $ctrl.level = level;
    });
};

GameController.$inject = ['$scope'];

const Game = {
    template: tmpl,
    controller: GameController,
};

export default Game;
