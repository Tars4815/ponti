/* Annotations definition */
function createAnnotation(id, scene, titleText, position, cameraPosition, cameraTarget, descriptionText) {
    // Create title and description elements
    let titleElement = $(`<span>${titleText}</span>`);
    // Create Potree.Annotation instance
    let annotation = new Potree.Annotation({
        position: position,
        title: titleElement,
        cameraPosition: cameraPosition,
        cameraTarget: cameraTarget,
        description: descriptionText
    });
    // Assigning unique ID from database
    annotation.customId = id;
    // Set the annotation to be visible
    annotation.visible = true;
    // Add the annotation to the scene
    scene.annotations.add(annotation);
    // Override toString method for the title element
    titleElement.toString = () => titleText;
}

function showEditForm(annotation) {
    // You can create a panel or modal to get user input for editing
    // For simplicity, here is an example of using prompts
    let newTitle = prompt("Enter new title for the annotation:", annotation.title);
    let newDescription = prompt("Enter new description for the annotation:", annotation.description);

    // Update the annotation in the scene
    annotation.title.text(newTitle);
    annotation.description = newDescription;

    // Update the annotation in the database
    updateAnnotationInDatabase(annotation.customId, newTitle, newDescription);
}

function updateAnnotationInDatabase(id, newTitle, newDescription) {
    // Use AJAX to send data to the PHP script for updating
    $.ajax({
        type: "POST",
        url: "update_annotation.php",
        data: {
            id: id,
            newTitle: newTitle,
            newDescription: newDescription
        },
        success: function (response) {
            console.log("Annotation id: ", id)
            console.log("Annotation updated in the database");
        },
        error: function (error) {
            console.error("Error updating annotation in the database:", error);
        }
    });
}

function deleteAnnotation(annotation) {
    let confirmation = confirm("Are you sure you want to delete this annotation?");
    
    if (confirmation) {
        // Assuming you have an 'id' property assigned to the annotation
        let annotationId = annotation.customId;

        // Call a function to remove the annotation from the scene
        removeAnnotationFromScene(annotation);

        // Call a function to delete the record from the database
        deleteAnnotationFromDatabase(annotationId);
    }
}

function removeAnnotationFromScene(annotation) {
    // Code to remove the annotation from the Potree scene
    viewer.scene.annotations.remove(annotation);
}

function deleteAnnotationFromDatabase(annotationId) {
    // Use AJAX to send a request to delete the record from the database
    $.ajax({
        type: "POST",
        url: "delete_annotation.php",
        data: {
            id: annotationId,
        },
        success: function (response) {
            console.log("Annotation deleted from the database");
        },
        error: function (error) {
            console.error("Error deleting annotation:", error);
        }
    });
}
