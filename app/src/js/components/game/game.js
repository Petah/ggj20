const tmpl = require("./game.tmpl.html");

const GameController = function() {
    const $ctrl = this;

    $ctrl.$onInit = changes => {
        console.log('Game Loaded.');
    };
};

GameController.$inject = [];

const Game = {
    template: tmpl,
    controller: GameController,
};

export default Game;
