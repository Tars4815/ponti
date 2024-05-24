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
	<link rel="stylesheet" type="text/css" href="./libs/Cesium/Widgets/CesiumWidget/CesiumWidget.css">
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
	<script src="./libs/Cesium/Cesium.js"></script>
	<!-- INCLUDE ADDITIONAL DEPENDENCIES HERE -->
	<!-- Custom styles for this template -->
	<link rel="stylesheet" type="text/css" href="./css/style.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Import main functions-->
	<script src="js/main.js"></script>
	<!-- Import POINTCLOUD-->
	<script type="module" src="js/pointcloud.js"></script>
	<!-- Import ANNOTATIONS-->
	<script src="js/annotations.js"></script>
	<!--Import ORIENTED IMAGES-->
	<!--<script src="js/orientedcameras.js"></script>-->
	<!--Loading settings for Potree viewer-->
	<div class="potree_container" style="position: relative; height:100%; width: 100%;">
		<div id="potree_render_area">
			<div id="cesiumContainer" style="position: absolute; width: 100%; height: 100%; background-color:black">
			</div>
		</div>
		<div id="potree_sidebar_container" style="width: 50%; height: 100%;"> </div>
		<!-- Custom form panel -->
		<!--Annotation type selection-->
		<div id="annotationTypeSelection" class="custom-form">
			<div><b>Select Annotation Type</b></div>
			<label for="annotationTypeDropdown">Choose type:</label>
			<select id="annotationTypeDropdown">
				<option value="comments">Comments</option>
				<option value="structural element">Structural Element</option>
				<option value="defect">Defect</option>
			</select>
			<button id="submitTypeBtn">Next</button>
		</div>
		<div id="customAnnotationForm" class="custom-form">
			<div><b>Create/Edit annotation</b></div>
			<!--Annotation details-->
			<label for="title">Title:</label>
			<input type="text" id="title" name="title">

			<label for="description">Description:</label>
			<textarea id="description" name="description"></textarea>

			<label for="position">Position (format: x, y, z):</label>
			<div class="position-input-container">
				<input type="text" id="position" name="position">
				<button id="pickPointButton">Pick point</button>
			</div>
			<!-- Defect type dropdown (initially hidden) -->
			<div id="defectTypeContainer" class="hidden">
				<label for="defectTypeDropdown">Defect type:</label>
				<select id="defectTypeDropdown">
					<option value="crack">Crack</option>
					<option value="corrosion">Corrosion</option>
					<option value="spalling">Spalling</option>
					<option value="deformation">Deformation</option>
					<option value="stains">Stains</option>
					<option value="other">Other</option>
				</select>
			</div>
			<!-- Defect severity dropdown (initially hidden) -->
			<div id="defectSeverityContainer" class="hidden">
				<label for="defectSeverityDropdown">Severity level:</label>
				<select id="defectSeverityDropdown">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="ND">ND</option>
				</select>
			</div>

			<button id="submitAnnotation">Submit</button>
			<button id="editAnnotation">Edit</button>
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
	<!--GUI Buttons-->
	<!-- Full screen mode -->
	<img id="fullscreen_icon" onclick="toggleFullScreen()" src="./assets/icons/fullscreen.svg" title="Fullscreen" />
	<!-- Navigation help -->
	<img id="question_icon" src="./assets/icons/question.svg" title="Tutorial" />
	<div id="question_panel" class="hidden">
		<!-- Content for the panel goes here -->
		<img id="nav_instructions" src="./assets/icons/navigation3d.png" alt="">
	</div>
	<img id="addAnnotationBtn" src="./assets/icons/annotation-form.svg" style="filter: invert(0);"
		title="Add a new annotation" alt="Add a new annotation">
	<!-- Layers filter list -->
	<img id="layers_icon" src="./assets/icons/layers.svg" title="Layers" />
	<div id="layers_panel" style="display: none;">
		<ul>
			<b>Annotations</b>
			<li>
				<div id="legend-structel" style="color:green;">Structural Elements</div>
			</li>
			<li>
				<div id="legend-defects" style="color: red;">Defects</div>
			</li>
			<li>
				<div id="legend-comments" style="color: cyan;">Comments</div>
			</li>
		</ul>
	</div>


</body>

</html>