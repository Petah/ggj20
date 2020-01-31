const tmpl = require("./score.tmpl.html");

const ScoreController = function (
    $interval,
) {
    const $ctrl = this;

    $ctrl.levelTime = 60;
    $ctrl.timeRemaining = $ctrl.levelTime;
    $ctrl.startTime = new Date().getTime();

    $interval(() => {
        $ctrl.timeRemaining = ($ctrl.levelTime - ((new Date().getTime() - $ctrl.startTime) / 1000)).toFixed(2);
    }, 25);
};

ScoreController.$inject = [
    '$interval',
];

const Score = {
    template: tmpl,
    controller: ScoreController,
};

export default Score;
