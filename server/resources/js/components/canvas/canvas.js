const tmpl = require("./canvas.tmpl.html");
const brushPaint = require("./brushes/paint_brush");
const brushEraser = require("./brushes/eraser");

const MOUSE_BUTTON_1 = 0b01;

const CanvasController = function (
    $element,
    $scope,
) {
    const $ctrl = this;

    $ctrl.canvas = null;
    $ctrl.content = null;
    $ctrl.drawing = false;

    $ctrl.brush = {
        colour: "#3d34a5",
        latestPoint: null,
        strokeWidth: 25,
        selected: brushPaint,
    };

    // Drawing functions
    const startStroke = point => {
        $ctrl.drawing = true;
        $ctrl.brush.latestPoint = point;
    };

    const continueStroke = newPoint => {
        $ctrl.brush.selected.handler($ctrl.canvas, $ctrl.context, $ctrl.brush, newPoint);
    };

    const endStroke = evt => {
        if (!$ctrl.drawing) {
            return;
        }
        $ctrl.drawing = false;
        evt.currentTarget.removeEventListener("mousemove", mouseMove, false);
    };

    // Event helpers
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

    $scope.$on('changeColor', (event, color) => {
        console.log(color);
    });

    $scope.$on('selectLevel', (event, level) => {
        console.log('Drawing level on canvas', level.attributes);
        $ctrl.width = level.attributes.width;
        $ctrl.height = level.attributes.height;
        const image = new Image();
        image.onload = function () {
            $ctrl.context.drawImage(image, 0, 0);
        };
        image.src = level.attributes.url;
    });
};

CanvasController.$inject = [
    "$element",
    "$scope",
];

const Canvas = {
    template: tmpl,
    controller: CanvasController,
    bindings: {
        height: "<?gHeight",
        width: "<?gWidth",
    },
};

export default Canvas;
