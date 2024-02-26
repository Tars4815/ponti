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
		<!-- Custom form panel -->
		<div id="customAnnotationForm">
			<div><b>Create new annotation</b></div>

			<label for="title">Title:</label>
			<input type="text" id="title" name="title">

			<label for="description">Description:</label>
			<textarea id="description" name="description"></textarea>

			<label for="position">Position (format: x, y, z):</label>
			<div class="position-input-container">
				<input type="text" id="position" name="position">
				<button id="pickPointButton">Pick point</button>
			</div>

			<button id="submitAnnotation">Submit</button>
		</div>
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
	<img id="addAnnotationBtn" src="libs\potree\resources\icons\new-annotation.svg" style="filter: invert(0);"
		title="Add a new annotation" alt="Add a new annotation">


</body>

</html>