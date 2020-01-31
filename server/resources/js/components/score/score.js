const tmpl = require("./score.tmpl.html");

const ScoreController = function (
    $interval,
    $scope,
    $rootScope,
) {
    const $ctrl = this;

    let interval = null;

    $scope.$on('selectLevel', (event, level) => {
        $ctrl.levelTime = level.attributes.timeLimit;
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

            let i = level.attributes.debuffs.length
            while (i--) {
                const debuff = level.attributes.debuffs[i]
                if (debuff.startTime > $ctrl.levelTime - $ctrl.timeRemaining) {
                    level.attributes.debuffs.splice(i, 1);
                    $rootScope.$broadcast('debuff', debuff);
                }
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
