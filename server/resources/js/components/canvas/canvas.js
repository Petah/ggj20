const tmpl = require('./canvas.tmpl.html');

const MOUSE_BUTTON_1 = 0b01;

const CanvasController = function (
    $element,
    $scope,
    $rootScope,
    $timeout,
    $http,
    AudioService,
) {
    const $ctrl = this;

    $ctrl.canvas = null;
    $ctrl.content = null;
    $ctrl.drawing = false;

    $ctrl.color = null;
    $ctrl.brush = 'thick';
    $ctrl.lastPoint = null;

    const endStroke = (event) => {
        console.log('Canvas end stroke');
        event.preventDefault();
        AudioService.stop("paintBrush");
        $ctrl.drawing = false;
    };

    const mouseButtonIsDown = buttons => {
        return (MOUSE_BUTTON_1 & buttons) === MOUSE_BUTTON_1;
    };

    let compareDebounce = null;

    const getEventPosition = (event) => {
        let x;
        let y;
        if (event.targetTouches && event.targetTouches.length) {
            const rect = event.target.getBoundingClientRect();
            x = event.targetTouches[0].clientX - rect.left;
            y = event.targetTouches[0].clientY - rect.top;
        } else {
            x = event.offsetX;
            y = event.offsetY;
        }
        return [x, y];
    }

    $ctrl.getBrushSize = () => {
        return $ctrl.brush == 'thick' ? 25 : 5;
    };

    const mouseMove = (event) => {
        // console.log('Canvas mouse move', $ctrl.drawing, event);
        event.preventDefault();


        const point = getEventPosition(event);
        // console.log('Point', point, $ctrl.lastPoint)
        if (!$ctrl.lastPoint) {
            $ctrl.lastPoint = point;
        }

        $('.g-canvas-brush').css('left', point[0] + 75 - ($ctrl.getBrushSize() / 2));
        $('.g-canvas-brush').css('top', point[1] + 102 - ($ctrl.getBrushSize() / 2));
        // $ctrl.context.beginPath();
        // $ctrl.context.arc(point[0], point[1], $ctrl.getBrushSize(), 0, 2 * Math.PI);
        // $ctrl.context.stroke();
        // $ctrl.context.endPath();

        if (!$ctrl.drawing) {
            return;
        }

        if ($ctrl.color) {
            AudioService.play("paintBrush", { loop: true });
            $ctrl.context.beginPath();
            $ctrl.context.moveTo($ctrl.lastPoint[0], $ctrl.lastPoint[1]);
            $ctrl.context.strokeStyle = $ctrl.color.hex;
            $ctrl.context.lineWidth = $ctrl.getBrushSize();
            $ctrl.context.lineCap = 'round';
            $ctrl.context.lineJoin = 'round';
            $ctrl.context.lineTo(point[0], point[1]);
            $ctrl.context.stroke();

            if (!compareDebounce) {
                compareDebounce = $timeout(() => {
                    console.log('Start compare');

                    const resizedCanvas = document.createElement("canvas");
                    const resizedContext = resizedCanvas.getContext("2d");

                    resizedCanvas.height = "100";
                    resizedCanvas.width = "100";

                    resizedContext.drawImage($ctrl.canvas, 0, 0, 100, 100);
                    // $('body').prepend(resizedCanvas);
                    const dataUrl = resizedCanvas.toDataURL('image/png');
                    // const dataUrl = $ctrl.canvas.toDataURL('image/png');

                    $http
                        .post('/api/compare', {
                            data: {
                                attributes: {
                                    image: dataUrl,
                                },
                                relationships: {
                                    level: {
                                        data: {
                                            id: $ctrl.level.id,
                                            type: $ctrl.level.type,
                                        },
                                    },
                                },
                            },
                        })
                        .then(response => {
                            compareDebounce = null;
                            $rootScope.$broadcast('comparison', response.data.data);
                        })
                        .catch(error => {
                            compareDebounce = null;
                            console.error(error);
                        });
                }, 200);
            }
        }

        $ctrl.lastPoint = point;
    };

    const mouseDown = (event) => {
        console.log('Canvas mouse down');
        event.preventDefault();
        $ctrl.drawing = true;
        $ctrl.lastPoint = getEventPosition(event);
        mouseMove(event);
    };

    const mouseEnter = (event) => {
        console.log('Canvas mouse enter');
        event.preventDefault();
        if (!mouseButtonIsDown(event.buttons) || $ctrl.drawing) {
            return;
        }
        mouseDown(event);
    };

    // Init helpers
    const initCanvas = () => {
        if ($ctrl.canvas) {
            return;
        }

        $ctrl.canvas = $element[0].querySelector('#g-canvas-canvas');
        $ctrl.context = $ctrl.canvas.getContext('2d');

        // Register event handlers
        $ctrl.canvas.addEventListener('mousedown', mouseDown, false);
        $ctrl.canvas.addEventListener('touchstart', mouseDown, false);
        $ctrl.canvas.addEventListener('mouseup', endStroke, false);
        $ctrl.canvas.addEventListener('touchend', endStroke, false);
        $ctrl.canvas.addEventListener('mouseout', endStroke, false);
        $ctrl.canvas.addEventListener('mouseenter', mouseEnter, false);
        $ctrl.canvas.addEventListener('mousemove', mouseMove, false);
        $ctrl.canvas.addEventListener('touchmove', mouseMove, false);
    };

    $ctrl.$onInit = () => {
        $ctrl.height = 650;
        $ctrl.width = 650;

        initCanvas();
    };

    $scope.$on('selectLevel', (event, level) => {
        console.log('Drawing level on canvas', level);
        $ctrl.level = level;
        // $ctrl.width = level.attributes.width;
        // $ctrl.height = level.attributes.height;

        $ctrl.context.clearRect(0, 0, $ctrl.canvas.width, $ctrl.canvas.height);

        const image = new Image();
        image.onload = () => {
            $ctrl.context.drawImage(image, 0, 0, $ctrl.width, $ctrl.height);
            console.log('Canvas ready');
        };
        image.src = level.attributes.url;
    });

    $scope.$on('debuff', (event, debuff) => {
        console.log('Canvas debuff', debuff);
    });

    $scope.$on('changeColor', (event, color) => {
        $ctrl.color = color;
    });

    $scope.$on('changeBrush', (event, brush) => {
        $ctrl.brush = brush;
    });

    $scope.$on('timeUp', (event, level) => {
        $ctrl.level = null;
    });
};

CanvasController.$inject = [
    '$element',
    '$scope',
    '$rootScope',
    '$timeout',
    '$http',
    'AudioService',
];

const Canvas = {
    template: tmpl,
    controller: CanvasController,
};

export default Canvas;
