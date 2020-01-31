const tmpl = require("./palette.tmpl.html");

const PaletteController = function (
    $scope,
    $rootScope,
) {
    const $ctrl = this;

    $ctrl.colors = [
        {
            name: 'Red',
            hex: '#ff0000',
        },
        {
            name: 'Green',
            hex: '#00ff00',
        },
        {
            name: 'Yellow',
            hex: '#ffff00',
        },
        {
            name: 'Blue',
            hex: '#0000ff',
        },
    ];

    $ctrl.changeColor = (color) => {
        console.log('Change color', color);
        $rootScope.$broadcast('changeColor', color);
    };

    $scope.$on('selectLevel', (event, level) => {
        $ctrl.colors = level.attributes.colors;
    });
};

PaletteController.$inject = [
    '$scope',
    '$rootScope',
];

const Palette = {
    template: tmpl,
    controller: PaletteController,
};

export default Palette;
