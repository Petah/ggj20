const brush = {
    type: "eraser",
    handler: (canvas, context, brush, newPoint) => {
        console.log("Erasing", newPoint);
    },
};

export default brush;
