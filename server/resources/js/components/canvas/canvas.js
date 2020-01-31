const tmpl = require("./canvas.tmpl.html");

const CanvasController = function() {
};

CanvasController.$inject = [];

const Canvas = {
    template: tmpl,
    controller: CanvasController,
};

export default Canvas;
