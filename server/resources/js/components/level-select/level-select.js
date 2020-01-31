const tmpl = require("./level-select.tmpl.html");

const LevelSelectController = function ($http) {
    const $ctrl = this;

    $http.get('/api/level/list')
        .then((response) => {
            $ctrl.levels = response.data.data;
        })
        .catch((error) => {
            console.error(error);
        });

    $ctrl.selectLevel = (level) => {
        console.log('Select level', level);
    };
};

LevelSelectController.$inject = ['$http'];

const LevelSelect = {
    template: tmpl,
    controller: LevelSelectController,
};

export default LevelSelect;
