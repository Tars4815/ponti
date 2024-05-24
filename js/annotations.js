/**
 * Create and add a Potree annotation to the scene with the provided information.
 *
 * @param {number} id - Unique identifier for the annotation.
 * @param {object} scene - The Potree scene in which the annotation will be added.
 * @param {string} titleText - Text for the title of the annotation.
 * @param {number[]} position - Array containing x, y, z coordinates of the annotation position.
 * @param {number[]} cameraPosition - Array containing x, y, z coordinates of the camera position.
 * @param {number[]} cameraTarget - Array containing x, y, z coordinates of the camera target.
 * @param {string} descriptionText - Text for the description of the annotation.
 * @throws {Error} Will throw an error if there's an issue creating or adding the annotation to the scene.
 */
function createAnnotation(
  id,
  scene,
  titleText,
  position,
  cameraPosition,
  cameraTarget,
  descriptionText,
  annotationType
) {
  // Create title and description elements
  let titleElement = $(`<span>${titleText}</span>`);
  // Create Potree.Annotation instance
  let annotation = new Potree.Annotation({
    position: position,
    title: titleElement,
    cameraPosition: cameraPosition,
    cameraTarget: cameraTarget,
    description: descriptionText,
  });
  // Assigning unique ID from database
  annotation.customId = id;
  // Classifying annotation based on assigned type
  annotation.typology = annotationType;
  // Set the annotation type-specific styles
  setAnnotationStyles(annotation, annotationType);
  // Set the annotation to be visible
  annotation.visible = true;
  // Add the annotation to the scene
  scene.annotations.add(annotation);
  // Override toString method for the title element
  titleElement.toString = () => titleText;
}

function setAnnotationStyles(annotation, annotationType) {
  // Define styles for different annotation types
  let styles = {};

  switch (annotationType) {
    case "comments":
      styles = {
        backgroundColor: "lightblue",
        titleColor: "blue",
      };
      break;
    case "structural element":
      styles = {
        backgroundColor: "lightgreen",
        titleColor: "green",
      };
      break;
    case "defect":
      styles = {
        backgroundColor: "lightcoral",
        titleColor: "red",
      };
      break;
    default:
      // Default styles for unknown types
      styles = {
        backgroundColor: "lightgray",
        titleColor: "black",
      };
  }

  // Apply styles to the annotation
  annotation.title.css("background-color", styles.backgroundColor);
  annotation.title.css("color", styles.titleColor);
}

/**
 * Save annotation in the database and create a corresponding annotation in the scene.
 *
 * @param {string} title - Title of the annotation.
 * @param {string} description - Description of the annotation.
 * @param {number[]} positionArray - Array containing x, y, z coordinates of the annotation position.
 * @param {number[]} camPositionArray - Array containing x, y, z coordinates of the camera position.
 * @param {number[]} camTargetArray - Array containing x, y, z coordinates of the camera target.
 * @throws {Error} Will throw an error if there's an issue saving the annotation.
 */
function saveAnnotation(
  title,
  description,
  positionArray,
  camPositionArray,
  camTargetArray,
  annotationType
) {
  // Use AJAX to send data to the PHP script for insertion
  $.ajax({
    type: "POST",
    url: "database/insert_annotation.php", // Adjust the URL based on your file structure
    data: {
      title: title,
      description: description,
      pos_x: positionArray[0],
      pos_y: positionArray[1],
      pos_z: positionArray[2],
      campos_x: camPositionArray[0],
      campos_y: camPositionArray[1],
      campos_z: camPositionArray[2],
      tarpos_x: camTargetArray[0],
      tarpos_y: camTargetArray[1],
      tarpos_z: camTargetArray[2],
      typology: annotationType,
      // Add additional parameters as needed
    },
    success: function (id) {
      // Use the returned ID to create the annotation
      createAnnotation(
        id,
        viewer.scene, // Assuming scene is accessible globally
        title,
        positionArray,
        camPositionArray,
        camTargetArray, // You can set camera target to camera position or adjust as needed
        description,
        annotationType
      );
    },
    error: function (error) {
      console.error("Error saving annotation:", error);
    },
  });

  console.log("Annotation created");
}

/**
 * Display the custom form for editing an existing annotation and update the annotation in the scene and database.
 *
 * @param {object} annotation - The annotation object containing information to be edited.
 * @throws {Error} Will throw an error if there's an issue updating the annotation in the scene or database.
 */
function showEditForm(annotation) {
  // Populate the custom form fields with existing annotation data
  document.getElementById("title").value = annotation.title;
  document.getElementById("description").value = annotation.description;
  document.getElementById("position").value = annotation.position
    .toArray()
    .join(", ");
  // Display the type selection panel
  typeSelectionPanel = document.getElementById("annotationTypeSelection");
  typeSelectionPanel.style.display = "flex";
  // Add a click event handler to the #submitTypeBtn button
  $("#submitTypeBtn").click(function () {
    // Get the selected annotation type
    const selectedType = $("#annotationTypeDropdown").val();

    // Hide the type selection panel
    typeSelectionPanel.style.display = "none";

    // Display the custom form panel
    annoForm = document.getElementById("customAnnotationForm");
    annoForm.style.display = "flex";

    // Set the selected type in a hidden field or variable for later use
    // You can use this information when creating the annotation
    selectedAnnotationType = selectedType;
    // Show edit button
    editButton = document.getElementById("editAnnotation");
    editButton.style.display = "flex";
    // Attach an event listener to the edit button
    document
      .getElementById("editAnnotation")
      .addEventListener("click", function () {
        // Retrieve values from the form
        let newTitle = document.getElementById("title").value;
        let newDescription = document.getElementById("description").value;
        let newPositionInput = $("#position").val();

        // Split position input into an array
        let positionArray = newPositionInput
          .split(",")
          .map((value) => parseFloat(value.trim()));
        console.log(positionArray);

        // Update the annotation in the scene
        annotation.title.text(newTitle);
        annotation.description = newDescription;

        // Retrieve camera positions and targets
        let camPositionArray;
        let camTargetArray;

        // Check if window.viewer is defined before attempting to access the camera position
        if (
          window.viewer &&
          window.viewer.scene &&
          window.viewer.scene.getActiveCamera
        ) {
          try {
            camPositionArray = window.viewer.scene
              .getActiveCamera()
              .position.toArray();
            console.log("Camera Position:", camPositionArray);
            camTargetArray = window.viewer.scene.view.getPivot().toArray();
            console.log("Target Position:", camTargetArray);
          } catch (error) {
            console.error("Error getting camera position:", error);
            console.error("Error getting target position:", error);
          }
        } else {
          console.error(
            "Viewer not properly initialized. Make sure 'window.viewer' is defined."
          );
        }

        // Remove the existing annotation from the scene
        removeAnnotationFromScene(annotation);

        // Update the annotation in the database
        updateAnnotationInDatabase(
          annotation.customId,
          newTitle,
          newDescription,
          positionArray,
          camPositionArray,
          camTargetArray,
          selectedAnnotationType
        );

        // Hide the custom form after submission
        document.getElementById("customAnnotationForm").style.display = "none";
      });
  });
}

/**
 * Update an existing annotation in the database and create a corresponding annotation in the scene.
 *
 * @param {number} id - Unique identifier of the annotation to be updated.
 * @param {string} newTitle - New title for the updated annotation.
 * @param {string} newDescription - New description for the updated annotation.
 * @param {number[]} newPositionArray - Array containing x, y, z coordinates of the updated annotation position.
 * @param {number[]} camPositionArray - Array containing x, y, z coordinates of the camera position.
 * @param {number[]} camTargetArray - Array containing x, y, z coordinates of the camera target.
 * @throws {Error} Will throw an error if there's an issue updating the annotation in the database or creating it in the scene.
 */
function updateAnnotationInDatabase(
  id,
  newTitle,
  newDescription,
  newPositionArray,
  camPositionArray,
  camTargetArray,
  annotationType
) {
  // Use AJAX to send data to the PHP script for updating
  $.ajax({
    type: "POST",
    url: "database/update_annotation.php",
    data: {
      id: id,
      newTitle: newTitle,
      newDescription: newDescription,
      newPositionArray: newPositionArray.join(","),
      camPositionArray: camPositionArray.join(","),
      camTargetArray: camTargetArray.join(","),
      typology: annotationType,
    },
    success: function (response) {
      console.log("Annotation id: ", id);
      console.log("Annotation updated in the database");
      // Use the returned ID to create the annotation
      createAnnotation(
        id,
        viewer.scene, // Assuming scene is accessible globally
        newTitle,
        newPositionArray,
        camPositionArray,
        camTargetArray,
        newDescription,
        annotationType
      );
    },
    error: function (error) {
      console.error("Error updating annotation in the database:", error);
    },
  });
}

/**
 * Prompt the user for confirmation and delete an annotation from the scene and database if confirmed.
 *
 * @param {object} annotation - The annotation object to be deleted.
 * @throws {Error} Will throw an error if there's an issue removing the annotation from the scene or deleting it from the database.
 */
function deleteAnnotation(annotation) {
  let confirmation = confirm(
    "Are you sure you want to delete this annotation?"
  );
  if (confirmation) {
    // Assuming you have an 'id' property assigned to the annotation
    let annotationId = annotation.customId;
    // Call a function to remove the annotation from the scene
    removeAnnotationFromScene(annotation);
    // Call a function to delete the record from the database
    deleteAnnotationFromDatabase(annotationId);
  }
}

/**
 * Remove an annotation from the Potree scene.
 *
 * @param {object} annotation - The annotation object to be removed from the scene.
 * @throws {Error} Will throw an error if there's an issue removing the annotation from the scene.
 */
function removeAnnotationFromScene(annotation) {
  // Code to remove the annotation from the Potree scene
  viewer.scene.annotations.remove(annotation);
}

/**
 * Delete an annotation record from the database using AJAX.
 *
 * @param {number} annotationId - The unique identifier of the annotation record to be deleted.
 * @throws {Error} Will throw an error if there's an issue deleting the annotation record from the database.
 */
function deleteAnnotationFromDatabase(annotationId) {
  // Use AJAX to send a request to delete the record from the database
  $.ajax({
    type: "POST",
    url: "database/delete_annotation.php",
    data: {
      id: annotationId,
    },
    success: function (response) {
      console.log("Annotation deleted from the database");
    },
    error: function (error) {
      console.error("Error deleting annotation:", error);
    },
  });
}

// Wait for the viewer to be ready
document.addEventListener("DOMContentLoaded", function () {
  // Load existing annotations from the server
  $.ajax({
    type: "GET",
    url: "database/load_annotations.php", // Adjust the URL based on your file structure
    dataType: "json",
    success: function (existingAnnotations) {
      // Assuming scene is available globally, adjust if needed
      let scene = viewer.scene;

      // Create Potree annotations for each existing record
      existingAnnotations.forEach((annotation) => {
        createAnnotation(
          annotation.id,
          scene,
          annotation.title,
          [annotation.pos_x, annotation.pos_y, annotation.pos_z],
          [annotation.campos_x, annotation.campos_y, annotation.campos_z],
          [annotation.tarpos_x, annotation.tarpos_y, annotation.tarpos_z],
          annotation.description,
          annotation.typology
        );
      });
    },
    error: function (error) {
      console.error("Error loading existing annotations:", error);
    },
  });
});

//CODE FOR CUSTOM FORM//
$(document).ready(function () {
  // Add a click event handler to the #addAnnotationBtn button
  $("#addAnnotationBtn").click(function () {
    // Display the type selection panel
    typeSelectionPanel = document.getElementById("annotationTypeSelection");
    // Check if the panel is currently open in the viewer
    if (typeSelectionPanel.style.display === "flex") {
      // Hide the panel
      typeSelectionPanel.style.display = "none";
    } else {
      // Make the panel visible
      typeSelectionPanel.style.display = "flex";
      annoForm = document.getElementById("customAnnotationForm");
      annoForm.style.display = "none";
      // Add a click event handler to the #submitTypeBtn button
      $("#submitTypeBtn").click(function () {
        // Get the selected annotation type
        const selectedType = $("#annotationTypeDropdown").val();
        // Hide the type selection panel
        typeSelectionPanel.style.display = "none";
        // Display the custom form panel
        annoForm.style.display = "flex";
        // Set the selected type in a hidden field or variable for later use
        // You can use this information when creating the annotation
        selectedAnnotationType = selectedType;
        // Show or hide the defect type and severity dropdowns based on the selected annotation type
        const defectTypeContainer = document.getElementById(
          "defectTypeContainer"
        );
        const defectSeverityContainer = document.getElementById(
          "defectSeverityContainer"
        );
        if (selectedAnnotationType === "defect") {
          defectTypeContainer.style.display = "block";
          defectSeverityContainer.style.display = "block";
        } else {
          defectTypeContainer.style.display = "none";
          defectSeverityContainer.style.display = "none";
        }
        // Display the submit button
        submitButton = document.getElementById("submitAnnotation");
        submitButton.style.display = "flex";
      });
    }
  });
});

// Add a click event handler to the #submitAnnotation button
// Wait for the viewer to be ready
document.addEventListener("DOMContentLoaded", function () {
  $("#submitAnnotation").click(function () {
    // Get values from the form fields
    let title = $("#title").val();
    let description = $("#description").val();
    let positionInput = $("#position").val();
    let selectedType = $("#annotationTypeDropdown").val();

    // Split position input into an array
    let positionArray = positionInput
      .split(",")
      .map((value) => parseFloat(value.trim()));
    console.log(positionArray);

    let camPositionArray;

    // Check if window.viewer is defined before attempting to access the camera position
    if (
      window.viewer &&
      window.viewer.scene &&
      window.viewer.scene.getActiveCamera
    ) {
      try {
        camPositionArray = window.viewer.scene
          .getActiveCamera()
          .position.toArray();
        console.log("Camera Position:", camPositionArray);
        camTargetArray = window.viewer.scene.view.getPivot().toArray();
        console.log("Target Position:", camTargetArray);
      } catch (error) {
        console.error("Error getting camera position:", error);
        console.error("Error getting target position:", error);
      }
    } else {
      console.error(
        "Viewer not properly initialized. Make sure 'window.viewer' is defined."
      );
    }

    // Save the annotation with the values from the form
    saveAnnotation(
      title,
      description,
      positionArray,
      camPositionArray,
      camTargetArray,
      selectedType
    );

    // Hide the custom form panel
    annoForm = document.getElementById("customAnnotationForm");
    annoForm.style.display = "none";
    submitButton = document.getElementById("submitAnnotation");
    submitButton.style.display = "none";
  });
});

/**
 * Add a click event handler to the #pickPointButton button to pick a point in the Potree viewer.
 *
 * The handler initiates the measuring tool to pick a single point and updates the input box with the selected point's coordinates.
 *
 * @listens click
 * @throws {Error} Will throw an error if there's an issue initiating the measuring tool or updating the input box.
 */
document.addEventListener("DOMContentLoaded", function () {
  $("#pickPointButton").click(function () {
    const measurement = window.viewer.measuringTool.startInsertion({
      showDistances: false,
      showAngles: false,
      showCoordinates: true,
      showArea: false,
      closed: true,
      maxMarkers: 1,
      name: "Point",
    });
    // Listen for the marker_dropped event
    measurement.addEventListener("marker_dropped", (e) => {
      // Get the coordinates of the picked point
      const coordinates = e.measurement.points[0].position.toArray();

      // Format the coordinates as a string (format: x, y, z)
      const selectedPoint = coordinates.join(", ");

      // Update the input box for the position with the selected point
      $("#position").val(selectedPoint);

      // Remove the measurement from the scene
      viewer.scene.removeMeasurement(measurement);
    });
  });
});
