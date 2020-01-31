const tmpl = require("./canvas.tmpl.html");

const MOUSE_BUTTON_1 = 0b01;

const CanvasController = function($element) {
    const $ctrl = this;

    $ctrl.canvas = null;
    $ctrl.content = null;
    $ctrl.drawing = false;

    $ctrl.brush = {
        colour: "#3d34a5",
        latestPoint: null,
        strokeWidth: 25,
    };

    // Drawing functions
    const continueStroke = newPoint => {
        $ctrl.context.beginPath();
        $ctrl.context.moveTo($ctrl.brush.latestPoint[0], $ctrl.brush.latestPoint[1]);
        $ctrl.context.strokeStyle = $ctrl.brush.colour;
        $ctrl.context.lineWidth = $ctrl.brush.strokeWidth;
        $ctrl.context.lineCap = "round";
        $ctrl.context.lineJoin = "round";
        $ctrl.context.lineTo(newPoint[0], newPoint[1]);
        $ctrl.context.stroke();

        $ctrl.brush.latestPoint = newPoint;
    };

    // Event helpers
    const startStroke = point => {
        $ctrl.drawing = true;
        $ctrl.brush.latestPoint = point;
    };

    const mouseButtonIsDown = buttons => {
        return (MOUSE_BUTTON_1 & buttons) === MOUSE_BUTTON_1;
    };

    const mouseMove = evt => {
        if (!$ctrl.drawing) {
            return;
        }
        continueStroke([evt.offsetX, evt.offsetY]);
    };

    const mouseDown = evt => {
        if ($ctrl.drawing) {
            return;
        }

        evt.preventDefault();
        $ctrl.canvas.addEventListener("mousemove", mouseMove, false);
        startStroke([evt.offsetX, evt.offsetY]);
    };

    const mouseEnter = evt => {
        if (!mouseButtonIsDown(evt.buttons) || $ctrl.drawing) {
            return;
        }
        mouseDown(evt);
    };

    const endStroke = evt => {
        if (!$ctrl.drawing) {
            return;
        }
        $ctrl.drawing = false;
        evt.currentTarget.removeEventListener("mousemove", mouseMove, false);
    };

    // Init helpers
    const initCanvas = () => {
        if ($ctrl.canvas) {
            return;
        }

        $ctrl.canvas = $element[0].querySelector("#g-canvas__canvas");
        $ctrl.context = $ctrl.canvas.getContext("2d");

        // Register event handlers
        $ctrl.canvas.addEventListener("mousedown", mouseDown, false);
        $ctrl.canvas.addEventListener("mouseup", endStroke, false);
        $ctrl.canvas.addEventListener("mouseout", endStroke, false);
        $ctrl.canvas.addEventListener("mouseenter", mouseEnter, false);
    };

    $ctrl.$onInit = () => {
        $ctrl.height = $ctrl.height || 800;
        $ctrl.width = $ctrl.width || 540;

        initCanvas();
    };
};

CanvasController.$inject = ["$element"];

const Canvas = {
    template: tmpl,
    controller: CanvasController,
    bindings: {
        height: "<?gHeight",
        width: "<?gWidth",
    },
};

export default Canvas;
