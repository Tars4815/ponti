/* Annotations definition */
function createAnnotation(scene, titleText, position, cameraPosition, cameraTarget, description) {
    // Create title element
    let titleElement = $(`<span>${titleText}</span>`);
    // Create Potree.Annotation instance
    let annotation = new Potree.Annotation({
        position: position,
        title: titleElement,
        cameraPosition: cameraPosition,
        cameraTarget: cameraTarget,
        description: description
    });
    // Set the annotation to be visible
    annotation.visible = true;
    // Add the annotation to the scene
    scene.annotations.add(annotation);
    // Override toString method for title element
    titleElement.toString = () => titleText;
}
// Annotation example 1
createAnnotation(
    bridgescene,
    "Annotation 1",
    [593673.870, 5089120.772, 910.538],
    [593661.279, 5089117.043, 907.581],
    [593673.870, 5089120.772, 910.538],
    'Insert description here!'
)
//Annotation example 2
createAnnotation(
    bridgescene,
    "Annotation 2",
    [593610.154, 5089104.605, 913.118],
    [593618.588, 5089107.116, 910.433],
    [593610.154, 5089104.605, 913.118],
    'Insert description here!'
)