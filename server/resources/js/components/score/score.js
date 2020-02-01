const tmpl = require("./score.tmpl.html");

const ScoreController = function (
    $interval,
    $scope,
    $rootScope,
    $http,
    AudioService,
) {
    const $ctrl = this;
    const audio = new AudioService();

    let interval = null;

    const timeUp = () => {
        $ctrl.timeRemaining = 0;
        $ctrl.timeUp = true;
        $interval.cancel(interval);
        $('.g-score .modal').modal('show');
        $http.get('/api/high-score/fetch/' + $ctrl.level.id).then((response) => {
            $ctrl.highScores = mapHighScores(response.data.data);
        }).catch((error) => {
            console.error(error);
        });
        audio.stop("tickTock", true);
        $rootScope.$broadcast('timeUp');
    };

    const mapHighScores = (highScores) => {
        return highScores.map((highScore) => {
            highScore.attributes.score = formatScore(highScore.attributes.score);
            return highScore;
        });
    };

    $scope.$on('selectLevel', (event, level) => {
        $ctrl.levelTime = level.attributes.timeLimit;
        $ctrl.timeRemaining = $ctrl.levelTime;
        $ctrl.startTime = new Date().getTime();
        $ctrl.score = 0;
        audio.play("tickTock", 1.0, true);

        // Start timer
        if (interval) {
            $interval.cancel(interval);
        }
        interval = $interval(() => {
            $ctrl.timeRemaining = ($ctrl.levelTime - ((new Date().getTime() - $ctrl.startTime) / 1000)).toFixed(2);
            $ctrl.timeRemainingFormatted = formatTimeRemaining($ctrl.timeRemaining);
            if ($ctrl.timeRemaining <= 30) {
                audio.changePlaybackRate("tickTock", 2.0);
            }
            if ($ctrl.timeRemaining <= 10) {
                audio.changePlaybackRate("tickTock", 3.0);
            }
            if ($ctrl.timeRemaining <= 0) {
                timeUp();
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
        $ctrl.realScore = comparison.attributes.score;
        $ctrl.score = formatScore(comparison.attributes.score);
    });

    $ctrl.submitScore = () => {
        $ctrl.submittingScore = true;
        $http.post('/api/high-score/submit', {
            data: {
                attributes: {
                    playerName: $ctrl.playerName,
                    levelId: $ctrl.level.id,
                    score: $ctrl.realScore,
                },
            },
        }).then((response) => {
            $ctrl.submittingScore = false;
            $ctrl.submittedScore = true;
            $ctrl.highScores = mapHighScores(response.data.data);
        }).catch((error) => {
            $ctrl.submittingScore = false;
            console.error(error);
        });
    };

    $ctrl.getArrowRotation = () => {
        const score = parseInt($ctrl.score);
        if (!$ctrl.realScore) {
            return 'rotate(0deg)';
        }
        const max = 130;
        const min = -125;
        if ($ctrl.realScore > 0) {
            return 'rotate(' + ($ctrl.realScore / 100 * max) + 'deg)';
        }
        let neg = -$ctrl.realScore / 100 * min;
        if (neg < min) {
            neg = min;
        }
        return 'rotate(' + neg + 'deg)';
    };

    $ctrl.stop = () => {
        timeUp();
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
    'AudioService',
];

const Score = {
    template: tmpl,
    controller: ScoreController,
};

export default Score;
