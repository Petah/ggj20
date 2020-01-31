const tmpl = require("./palette.tmpl.html");
import colors from "../../common/colors";

const PaletteController = function () {
    const $ctrl = this;

    $ctrl.colors = colors;

    $ctrl.changeColor = (color) => {
        console.log('Change color', color);
    };
};

PaletteController.$inject = [];

const Palette = {
    template: tmpl,
    controller: PaletteController,
    bindings: {
        onColorChange: "&?onColorChange",
    },
};

export default Palette;
