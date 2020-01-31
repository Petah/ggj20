const tmpl = require("./score.tmpl.html");

const ScoreController = function (
    $interval,
    $scope,
    $rootScope,
) {
    const $ctrl = this;

    let interval = null;

    $scope.$on('selectLevel', (event, level) => {
        $ctrl.levelTime = 10;
        $ctrl.timeRemaining = $ctrl.levelTime;
        $ctrl.startTime = new Date().getTime();
        $ctrl.score = 0;

        // Start timer
        if (interval) {
            $interval.cancel(interval);
        }
        interval = $interval(() => {
            $ctrl.timeRemaining = ($ctrl.levelTime - ((new Date().getTime() - $ctrl.startTime) / 1000)).toFixed(2);
            if ($ctrl.timeRemaining <= 0) {
                $ctrl.timeRemaining = 0;
                $interval.cancel(interval);
                $rootScope.$broadcast('timeUp');
            }
        }, 25);
    });
};

ScoreController.$inject = [
    '$interval',
    '$scope',
    '$rootScope',
];

const Score = {
    template: tmpl,
    controller: ScoreController,
};

export default Score;
