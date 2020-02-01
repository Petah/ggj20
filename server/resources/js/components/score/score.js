const tmpl = require("./score.tmpl.html");

const ScoreController = function (
    $interval,
    $scope,
    $rootScope,
    $http,
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
            $ctrl.timeRemainingFormatted = formatTimeRemaining($ctrl.timeRemaining);
            if ($ctrl.timeRemaining <= 0) {
                $ctrl.timeRemaining = 0;
                $interval.cancel(interval);
                $('.g-score .modal').modal('show');
                $http.get('/api/high-score/fetch/' + $ctrl.level.id).then((response) => {
                    $ctrl.highScores = response.data.data;
                }).catch((error) => {
                    console.error(error);
                });
                $rootScope.$broadcast('timeUp');
            }

            let i = level.attributes.debuffs.length
            while (i--) {
                const debuff = level.attributes.debuffs[i]
                if (debuff.startTime > $ctrl.levelTime - $ctrl.timeRemaining) {
                    level.attributes.debuffs.splice(i, 1);
                    console.log('Debuff', debuff);
                    $rootScope.$broadcast('debuff', debuff);
                }
            }
        }, 25);
    });

    const formatTimeRemaining = (timeRemaining) => {
        var secNum = parseInt(timeRemaining, 10);
        var hours = Math.floor(secNum / 3600);
        var minutes = Math.floor((secNum - (hours * 3600)) / 60);
        var seconds = secNum - (hours * 3600) - (minutes * 60);

        if (minutes < 10) { minutes = "0" + minutes; }
        if (seconds < 10) { seconds = "0" + seconds; }
        return minutes + ':' + seconds;
    };

    const formatScore = (score) => {
        if (score < 0) {
            return (score * 100).toFixed();
        }
        return (score * 1000).toFixed();
    };

    $scope.$on('comparison', (event, comparison) => {
        console.log('Comparison', comparison);
        $ctrl.score = formatScore(comparison.attributes.score);
    });

    $ctrl.submitScore = () => {
        $ctrl.submittingScore = true;
        $http.post('/api/high-score/submit', {
            data: {
                attributes: {
                    playerName: $ctrl.playerName,
                    levelId: $ctrl.level.id,
                    score: $ctrl.score,
                },
            },
        }).then((response) => {
            $ctrl.submittingScore = false;
            $ctrl.submittedScore = true;
            $ctrl.highScores = response.data.data;
        }).catch((error) => {
            $ctrl.submittingScore = false;
            console.error(error);
        });
    };
    $scope.$on('selectLevel', (event, level) => {
        $ctrl.level = level;
    });
};

ScoreController.$inject = [
    '$interval',
    '$scope',
    '$rootScope',
    '$http',
];

const Score = {
    template: tmpl,
    controller: ScoreController,
};

export default Score;
