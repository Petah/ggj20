const tmpl = require("./palette.tmpl.html");
import colors from "../../common/colors";

const PaletteController = function (
    $scope,
    $rootScope,
) {
    const $ctrl = this;

    $ctrl.colors = colors;

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
    bindings: {
        onColorChange: "&?onColorChange",
    },
};

export default Palette;
