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
        $rootScope.$broadcast('selectLevel', level);
    };
};

LevelSelectController.$inject = ['$http', '$rootScope'];

const LevelSelect = {
    template: tmpl,
    controller: LevelSelectController,
};

export default LevelSelect;
