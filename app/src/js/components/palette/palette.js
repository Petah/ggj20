const tmpl = require("./palette.tmpl.html");

const PaletteController = function() {
};

PaletteController.$inject = [];

const Palette = {
    template: tmpl,
    controller: PaletteController,
};

export default Palette;
