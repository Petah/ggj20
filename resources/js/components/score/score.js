import initComparisons from "./image-compare";

import tmpl from './score.tmpl.html?raw';

const ScoreController = function (
    $interval,
    $scope,
    $rootScope,
    $http,
    AudioService,
) {
    const $ctrl = this;
    $ctrl.state = 'scores';

    let interval = null;
    let isWarningSoundPlayed = false;

    const timeUp = () => {
        if ($ctrl.timeUp) {
            return;
        }
        $ctrl.bonus = 0;
        $ctrl.finalScore = formatScore($ctrl.realScore, 0);

        // The server has the final say on the score and works out the time bonus.
        $http.post('/api/rounds/' + $ctrl.level.round.id + '/finish').then((response) => {
            const round = response.data.data.attributes;
            $ctrl.realScore = round.score;
            $ctrl.bonus = round.bonus;
            $ctrl.finalScore = formatScore(round.score, round.bonus);
            $ctrl.originalUrl = round.original;
        }).catch((error) => {
            console.error(error);
        });

        $ctrl.timeRemaining = 0;
        $ctrl.timeUp = true;
        AudioService.play("gong");
        $interval.cancel(interval);
        $('.g-score .modal').modal({
            backdrop: 'static',
            keyboard: false,
        });

        const canvas = $('#g-compare').attr({
            width: $('#g-canvas-canvas').attr('width'),
            height: $('#g-canvas-canvas').attr('height'),
        })
        const context = canvas[0].getContext('2d');
        context.drawImage($('#g-canvas-canvas')[0], 0, 0, $('#g-canvas-canvas').attr('width'), $('#g-canvas-canvas').attr('height'));
        initComparisons();

        $('.g-score .modal').modal('show');

        $http.get('/api/levels/' + $ctrl.level.id + '/high-scores').then((response) => {
            $ctrl.highScores = mapHighScores(response.data.data);
        }).catch((error) => {
            console.error(error);
        });
        $rootScope.$broadcast('timeUp');
    };

    const mapHighScores = (highScores) => {
        return highScores.map((highScore) => {
            highScore.attributes.finalScore = formatScore(highScore.attributes.score, highScore.attributes.bonus);
            return highScore;
        });
    };

    let lastTick = true;
    let nextTick = null;
    const playTick = () => {
        if (lastTick) {
            AudioService.play("tick", { force: true, volume: 0.25 });
        } else {
            AudioService.play("tock", { force: true, volume: 0.25 });
        }
        lastTick = !lastTick;
        if ($ctrl.timeRemaining > 10) {
            nextTick = $ctrl.timeRemaining - 1;
        } else if ($ctrl.timeRemaining > 5) {
            nextTick = $ctrl.timeRemaining - 0.5;
        } else {
            nextTick = $ctrl.timeRemaining - 0.25;
        }
    };

    $scope.$on('selectLevel', (event, level) => {
        $ctrl.levelTime = level.attributes.timeLimit;
        $ctrl.timeRemaining = $ctrl.levelTime;
        nextTick = $ctrl.timeRemaining - 1;
        $ctrl.startTime = new Date().getTime();
        $ctrl.score = 0;

        // Start timer
        if (interval) {
            $interval.cancel(interval);
        }
        interval = $interval(() => {
            $ctrl.timeRemaining = ($ctrl.levelTime - ((new Date().getTime() - $ctrl.startTime) / 1000)).toFixed(2);
            if ($ctrl.timeRemaining <= nextTick) {
                playTick();
            }
            $ctrl.timeRemainingFormatted = formatTimeRemaining($ctrl.timeRemaining);
            if ($ctrl.timeRemaining <= 0) {
                timeUp();
            }

        }, 50);
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

    const formatScore = (score, bonus) => {
        score = parseFloat(score || 0);
        bonus = parseFloat(bonus || 0);
        if (score < 0) {
            return parseInt((score * 100).toFixed());
        }
        return parseInt((score * 1000 + bonus).toFixed());
    };

    $scope.$on('comparison', (event, comparison) => {
        console.log('Comparison', comparison);
        $ctrl.realScore = comparison.attributes.score;
        $ctrl.score = formatScore(comparison.attributes.score);

        if (isWarningSoundPlayed == false && $ctrl.realScore <= -99) {
            isWarningSoundPlayed = true;
            AudioService.play("winston", { volume: 0.5 });
        }
    });

    $ctrl.submitScore = () => {
        $ctrl.submittingScore = true;
        $http.post('/api/rounds/' + $ctrl.level.round.id + '/high-score', {
            player_name: $ctrl.playerName,
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

    $ctrl.getGrade = (highScore) => {
        const score = formatScore(highScore.attributes.score, highScore.attributes.bonus);
        if (score > 95000) {
            return 'A+';
        }
        if (score > 90000) {
            return 'A';
        }
        if (score > 75000) {
            return 'B';
        }
        if (score > 50000) {
            return 'C';
        }
        if (score > 25000) {
            return 'D';
        }
        return 'F';
    };

    $scope.$on('selectLevel', (event, level) => {
        $ctrl.level = level;
        $ctrl.timeUp = false;
        $ctrl.originalUrl = null;
        $ctrl.submittedScore = false;
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
