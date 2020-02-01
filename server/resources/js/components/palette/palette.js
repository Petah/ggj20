const tmpl = require('./palette.tmpl.html');

const PaletteController = function($scope, $rootScope, AudioService) {
    const $ctrl = this;
    $ctrl.colors = [];
    $ctrl.color = null;
    $ctrl.brush = 'thick';

    $ctrl.changeColor = color => {
        console.log('Change color', color);
        $ctrl.color = color;
        $rootScope.$broadcast('changeColor', color);
    };

    $ctrl.changeBrush = brush => {
        console.log('Change brush', brush);
        $ctrl.brush = brush;
        AudioService.play('brush');
        $rootScope.$broadcast('changeBrush', brush);
    };

    $scope.$on('selectLevel', (event, level) => {
        $ctrl.colors = level.attributes.colors;
        $ctrl.changeColor(level.attributes.colors[0]);
    });

    $scope.$on('timeUp', (event, level) => {
        $ctrl.colors = [];
        $ctrl.level = null;
    });

    $scope.$on('selectLevel', (event, level) => {
        $ctrl.level = level;
    });
};

PaletteController.$inject = ['$scope', '$rootScope', 'AudioService'];

const Palette = {
    template: tmpl,
    controller: PaletteController,
    bindings: {
        onColorChange: '&?onColorChange',
    },
};

export default Palette;
