const tmpl = require('./level-select.tmpl.html');

const LevelSelectController = function ($http, $rootScope, AudioService) {
    const $ctrl = this;

    $ctrl.state = 'welcome';
    $ctrl.levelIndex = 0;

    $http
        .get('/api/level/list')
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
        console.log('Select level', level);
        $('.g-level-select .modal').modal('hide');

        AudioService.play("click");
        $rootScope.$broadcast('selectLevel', level);
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
