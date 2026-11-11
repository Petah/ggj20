import tmpl from './level-select.tmpl.html?raw';

const LevelSelectController = function ($http, $rootScope, AudioService) {
    const $ctrl = this;

    $ctrl.state = 'welcome';
    $ctrl.levelIndex = 0;

    $http
        .get('/api/levels')
        .then(response => {
            $ctrl.levels = response.data.data;
        })
        .catch(error => {
            console.error(error);
        });

    $ctrl.showLevelSelect = () => {
        AudioService.play("click");
        $ctrl.state = 'level-select';
    };

    $ctrl.selectLevel = level => {
        if ($ctrl.starting) {
            return;
        }
        $ctrl.starting = true;
        AudioService.play("click");

        // The server starts the clock and keeps the score for this round.
        $http
            .post('/api/rounds', { level_id: level.id })
            .then(response => {
                level.round = response.data.data;
                $('.g-level-select .modal').modal('hide');
                $rootScope.$broadcast('selectLevel', level);
            })
            .catch(error => {
                console.error(error);
            })
            .finally(() => {
                $ctrl.starting = false;
            });
    };

    $ctrl.nextLevel = () => {
        AudioService.play("scroll", { force: true });
        $ctrl.levelIndex++;
        if ($ctrl.levelIndex >= $ctrl.levels.length) {
            $ctrl.levelIndex = 0;
        }
    };

    $ctrl.prevLevel = () => {
        AudioService.play("scroll", { force: true });
        $ctrl.levelIndex--;
        if ($ctrl.levelIndex < 0) {
            $ctrl.levelIndex = $ctrl.levels.length - 1;
        }
    };

    $('.g-level-select .modal').modal({
        backdrop: 'static',
        keyboard: false,
    });
    $('.g-level-select .modal').modal('show');
};

LevelSelectController.$inject = [
    '$http',
    '$rootScope',
    'AudioService',
];

const LevelSelect = {
    template: tmpl,
    controller: LevelSelectController,
};

export default LevelSelect;
