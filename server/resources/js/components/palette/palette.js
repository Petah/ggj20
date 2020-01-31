const tmpl = require('./palette.tmpl.html');

const PaletteController = function($scope, $rootScope) {
    const $ctrl = this;
    $ctrl.colors = [];

    $ctrl.changeColor = color => {
        console.log('Change color', color);
        $rootScope.$broadcast('changeColor', color);
    };

    $scope.$on('selectLevel', (event, level) => {
        $ctrl.colors = level.attributes.colors;
        $ctrl.changeColor(level.attributes.colors[0]);
    });

    $scope.$on('timeUp', (event, level) => {
        $ctrl.colors = [];
    });
};

PaletteController.$inject = ['$scope', '$rootScope'];

const Palette = {
    template: tmpl,
    controller: PaletteController,
    bindings: {
        onColorChange: '&?onColorChange',
    },
};

export default Palette;
