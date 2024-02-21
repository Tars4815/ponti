<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="description" content="Bridge digital twin">
	<meta name="author" content="Federica Gaspari">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<title>Bridge name</title>
	<link rel="stylesheet" type="text/css" href="./libs/potree/potree.css">
	<link rel="stylesheet" type="text/css" href="./libs/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="./libs/openlayers3/ol.css">
	<link rel="stylesheet" type="text/css" href="./libs/spectrum/spectrum.css">
	<link rel="stylesheet" type="text/css" href="./libs/jstree/themes/mixed/style.css">
</head>

<body>
	<script src="./libs/jquery/jquery-3.1.1.min.js"></script>
	<script src="./libs/spectrum/spectrum.js"></script>
	<script src="./libs/jquery-ui/jquery-ui.min.js"></script>
	<script src="./libs/other/BinaryHeap.js"></script>
	<script src="./libs/tween/tween.min.js"></script>
	<script src="./libs/d3/d3.js"></script>
	<script src="./libs/proj4/proj4.js"></script>
	<script src="./libs/openlayers3/ol.js"></script>
	<script src="./libs/i18next/i18next.js"></script>
	<script src="./libs/jstree/jstree.js"></script>
	<script src="./libs/potree/potree.js"></script>
	<script src="./libs/plasio/js/laslaz.js"></script>
	<!-- INCLUDE ADDITIONAL DEPENDENCIES HERE -->
	<!-- Custom styles for this template -->
	<link rel="stylesheet" type="text/css" href="./css/style.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Defining header with title -->
	<div id="header_panel">
		<div id="header_title">
			Protree Template - Example of a Bridge 3D data exploration
		</div>
	</div>
	<!--Loading settings for Potree viewer-->
	<div class="potree_container" style="position: relative; height:100%; width: 100%;">
		<div id="potree_render_area">
		</div>
		<div id="potree_sidebar_container" style="width: 50%; height: 100%;"> </div>
	</div>
	<!--- Connect to Database -->
	<?php
	$connection = pg_connect("host=localhost port=5432 dbname=bridges user=postgres password=root");
	if (!$connection) {
		echo "An error occurred.<br>";
		exit;
	}



	// Close the database connection
	pg_close($connection);

	?>
	<!-- Import POINTCLOUD-->
	<script src="js/pointcloud.js"></script>
	<!-- Import ANNOTATIONS-->
	<script src="js/annotations.js"></script>
	<!--Import ORIENTED IMAGES-->
	<!--<script src="js/orientedcameras.js"></script>-->
	<!--Testing new annotation form-->
	<!-- Your existing HTML structure -->
	<button id="addAnnotationBtn">Add Annotation</button>
	<!-- Add this script to handle the button click and show the form -->
	<script>
		// Load existing annotations from the server
		$.ajax({
			type: "GET",
			url: "load_annotations.php", // Adjust the URL based on your file structure
			dataType: "json",
			success: function (existingAnnotations) {
				// Assuming bridgescene is available globally, adjust if needed
				let scene = bridgescene;

				// Create Potree annotations for each existing record
				existingAnnotations.forEach(annotation => {
					createAnnotation(
						scene,
						annotation.title,
						[annotation.pos_x, annotation.pos_y, annotation.pos_z],
						[annotation.campos_x, annotation.campos_y, annotation.campos_z],
						[annotation.tarpos_x, annotation.tarpos_y, annotation.tarpos_z],
						annotation.description
					);
				});
			},
			error: function (error) {
				console.error("Error loading existing annotations:", error);
			}
		});

		$(document).ready(function () {
			$("#addAnnotationBtn").click(function () {
				// Display the annotation form panel
				showAnnotationForm();
			});

			function showAnnotationForm() {
				// You can create a panel or modal to get user input for title, description, and position
				// For simplicity, here is an example of using a basic prompt
				let title = prompt("Enter title for the annotation:");
				let description = prompt("Enter description for the annotation:");
				let positionInput = prompt("Enter position for the annotation (format: x, y, z):");

				// Split position input into an array
				let positionArray = positionInput.split(',').map(value => parseFloat(value.trim()));
				console.log(positionArray);

				let camPositionArray;

				// Check if window.viewer is defined before attempting to access the camera position
				if (window.viewer && window.viewer.scene && window.viewer.scene.getActiveCamera) {
					try {
						camPositionArray = window.viewer.scene.getActiveCamera().position.toArray();
						console.log("Camera Position:", camPositionArray);
					} catch (error) {
						console.error("Error getting camera position:", error);
					}
				} else {
					console.error("Viewer not properly initialized. Make sure 'window.viewer' is defined.");
				}

				saveAnnotation(title, description, positionArray, camPositionArray);
			}

			function saveAnnotation(title, description, positionArray, camPositionArray) {
				// Use AJAX to send data to the PHP script for insertion
				$.ajax({
					type: "POST",
					url: "insert_annotation.php", // Adjust the URL based on your file structure
					data: {
						title: title,
						description: description,
						pos_x: positionArray[0],
						pos_y: positionArray[1],
						pos_z: positionArray[2],
						campos_x: camPositionArray[0],
						campos_y: camPositionArray[1],
						campos_z: camPositionArray[2],
						tarpos_x: positionArray[0],
						tarpos_y: positionArray[1],
						tarpos_z: positionArray[2],
						// Add additional parameters as needed
					},
					success: function (response) {
						createAnnotation(
							bridgescene,  // Assuming bridgescene is accessible globally
							title,
							positionArray,
							camPositionArray,
							positionArray,  // You can set camera target to camera position or adjust as needed
							description
						);
					},
					error: function (error) {
						console.error("Error saving annotation:", error);
					}
				});

				console.log('Annotation created')
			}
		});
	</script>
</body>

</html>