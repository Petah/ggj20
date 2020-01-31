const tmpl = require("./palette.tmpl.html");

const PaletteController = function () {
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
