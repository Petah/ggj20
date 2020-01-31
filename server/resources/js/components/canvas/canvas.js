const tmpl = require("./canvas.tmpl.html");

const MOUSE_BUTTON_1 = 0b01;

const CanvasController = function (
    $element,
    $scope,
    $rootScope,
) {
    const $ctrl = this;

    $ctrl.canvas = null;
    $ctrl.content = null;
    $ctrl.drawing = false;

    // Drawing functions
    const startStroke = point => {
        $ctrl.drawing = true;
        $rootScope.$broadcast('brushStartStroke', point);
    };

    const continueStroke = point => {
        $rootScope.$broadcast('brushContinueStroke', $ctrl.canvas, $ctrl.context, point);
    };

    const endStroke = event => {
        if (!$ctrl.drawing) {
            return;
        }

        $ctrl.drawing = false;
        event.currentTarget.removeEventListener("mousemove", mouseMove, false);
        $rootScope.$broadcast('brushEndStroke', event);
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
    "$rootScope",
];

const Canvas = {
    template: tmpl,
    controller: CanvasController,
    bindings: {
        brush: "<?gBrush",
        height: "<?gHeight",
        width: "<?gWidth",
    },
};

export default Canvas;
