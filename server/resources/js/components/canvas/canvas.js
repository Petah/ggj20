const tmpl = require('./canvas.tmpl.html');

const MOUSE_BUTTON_1 = 0b01;

// 740
// 740

const CanvasController = function (
    $element,
    $scope,
    $rootScope,
    $timeout,
    $http,
) {
    const $ctrl = this;

    $ctrl.canvas = null;
    $ctrl.content = null;
    $ctrl.drawing = false;

    $ctrl.color = null;
    $ctrl.lastPoint = null;


    const endStroke = (event) => {
        console.log('Canvas end stroke');
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
            x = event.targetTouches[0].pageX - rect.left;
            y = event.targetTouches[0].pageY - rect.top;
        } else {
            x = event.offsetX;
            y = event.offsetY;
        }
        return [x, y];
    }

    const mouseMove = (event) => {
        console.log('Canvas mouse move', $ctrl.drawing, event);
        if (!$ctrl.drawing) {
            return;
        }


        const point = getEventPosition(event);
        console.log('Point', point, $ctrl.lastPoint)
        if (!$ctrl.lastPoint) {
            $ctrl.lastPoint = point;
        }

        if ($ctrl.color) {
            $ctrl.context.beginPath();
            $ctrl.context.moveTo($ctrl.lastPoint[0], $ctrl.lastPoint[1]);
            $ctrl.context.strokeStyle = $ctrl.color.hex;
            $ctrl.context.lineWidth = 25;
            $ctrl.context.lineCap = 'round';
            $ctrl.context.lineJoin = 'round';
            $ctrl.context.lineTo(point[0], point[1]);
            $ctrl.context.stroke();

            if (!compareDebounce) {
                compareDebounce = $timeout(() => {
                    compareDebounce = null;
                    // console.log($ctrl.canvas.toDataURL());
                    console.log('compare');

                    $http
                        .post('/api/compare', {
                            data: {
                                attributes: {
                                    image: $ctrl.canvas.toDataURL(),
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
                            $rootScope.$broadcast('comparison', response.data.data);
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }, 100);
            }
        }

        $ctrl.lastPoint = point;
    };

    const mouseDown = (event) => {
        console.log('Canvas mouse down');
        $ctrl.drawing = true;
        $ctrl.lastPoint = getEventPosition(event);
        mouseMove(event);
    };

    const mouseEnter = (event) => {
        console.log('Canvas mouse enter');
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

        $ctrl.canvas = $element[0].querySelector('#g-canvas__canvas');
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
        $ctrl.height = 740;
        $ctrl.width = 740;

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
};

CanvasController.$inject = [
    '$element',
    '$scope',
    '$rootScope',
    '$timeout',
    '$http',
];

const Canvas = {
    template: tmpl,
    controller: CanvasController,
    bindings: {
        brush: '<?gBrush',
        height: '<?gHeight',
        width: '<?gWidth',
    },
};

export default Canvas;
