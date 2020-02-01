const tmpl = require('./level-select.tmpl.html');

const LevelSelectController = function ($http, $rootScope, AudioService) {
    const $ctrl = this;
    const audio = new AudioService();

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
        audio.play("click");
        $ctrl.state = 'level-select';
    };

    $ctrl.selectLevel = level => {
        console.log('Select level', level);
        $('.g-level-select .modal').modal('hide');

        audio.play("click");
        $rootScope.$broadcast('selectLevel', level);
    };

    $ctrl.nextLevel = () => {
        $ctrl.levelIndex++;
        if ($ctrl.levelIndex >= $ctrl.levels.length) {
            $ctrl.levelIndex = 0;
        }
    };

    $ctrl.prevLevel = () => {
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
