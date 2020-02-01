const tmpl = require('./level-select.tmpl.html');

const LevelSelectController = function($http, $rootScope) {
    const $ctrl = this;

    $http
        .get('/api/level/list')
        .then(response => {
            $ctrl.levels = response.data.data;
        })
        .catch(error => {
            console.error(error);
        });

    $ctrl.selectLevel = level => {
        console.log('Select level', level);
        $('.g-level-select .modal').modal('hide');
        $rootScope.$broadcast('selectLevel', level);
    };

    $('.g-level-select .modal').modal('show');
};

LevelSelectController.$inject = [
    '$http',
    '$rootScope',
];

const LevelSelect = {
    template: tmpl,
    controller: LevelSelectController,
};

export default LevelSelect;
