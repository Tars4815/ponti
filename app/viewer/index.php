<!--- Connect to Database -->
<?php
header('Content-Type: text/html');

// Database connection parameters
$host = 'localhost';
$db = 'bridges';
$user = 'postgres';
$pass = 'mysecretpassword';

// Create a connection to the PostgreSQL database
$dsn = "pgsql:host=$host;dbname=$db";
try {
	$pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
	echo json_encode(['error' => $e->getMessage()]);
	exit();
}

// Get the inspectionId from the query parameter
$inspectionId = isset($_GET['inspectionId']) ? intval($_GET['inspectionId']) : 0;

// SQL query to get the pointcloud filepath for the specific inspection
$sql = "SELECT filepath FROM pointclouds WHERE fkinspection = :inspectionId";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':inspectionId', $inspectionId, PDO::PARAM_INT);
$stmt->execute();

$pointcloud = $stmt->fetch(PDO::FETCH_ASSOC);

$filepath = $pointcloud ? $pointcloud['filepath'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="description" content="Bridge digital twin">
	<meta name="author" content="Federica Gaspari">
	<meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="theme-color" content="#000000">
	<title>PONTI | 3D Viewer</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="../assets/css/app.css">
	<link rel="stylesheet" type="text/css" href="./libs/potree/potree.css">
	<link rel="stylesheet" type="text/css" href="./libs/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="./libs/openlayers3/ol.css">
	<link rel="stylesheet" type="text/css" href="./libs/spectrum/spectrum.css">
	<link rel="stylesheet" type="text/css" href="./libs/jstree/themes/mixed/style.css">
	<link rel="stylesheet" type="text/css" href="./libs/Cesium/Widgets/CesiumWidget/CesiumWidget.css">
	<link rel="apple-touch-icon" sizes="76x76" href="../assets/img/favicon-76.png">
	<link rel="apple-touch-icon" sizes="120x120" href="../assets/img/favicon-120.png">
	<link rel="apple-touch-icon" sizes="152x152" href="../assets/img/favicon-152.png">
	<link rel="icon" sizes="196x196" href="../assets/img/favicon-196.png">
	<link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico">
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
	<!-- Custom styles for this template -->
	<link rel="stylesheet" type="text/css" href="./css/style.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Import main functions-->
	<script src="js/main.js"></script>
	<!-- Import POINTCLOUD-->
	<script type="module" src="js/pointcloud.js?filepath=<?php echo urlencode($filepath); ?>"></script>
	<!-- Import ANNOTATIONS-->
	<script src="js/annotations-mod.js"></script>
	<!-- Import Structural Elements info-->
	<script src="js/structural-elements.js"></script>
	<!--Import ORIENTED IMAGES-->
	<script src="js/orientedcameras.js"></script>
	<!--Loading settings for Potree viewer-->
	<div id="container">
		<!--<div class="potree_container" style="position: relative;"> -->
		<div class="potree_container">
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

				<label for="position">Position (format: x, y, z) & element:</label>
				<div class="position-input-container">
					<input type="text" id="position" name="position">
					<button id="pickPointButton">Pick point</button>
				</div>
				<!-- Defect type dropdown (initially hidden) -->
				<div id="defectTypeContainer">
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
				<div id="defectSeverityContainer">
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
			<!-- Structural Element menu -->
			<div id="structural-element-panel" class="custom-form">
				<div><b>Add structural element</b></div>
				<!--Element details-->
				<div id="elementTypeContainer">
					<label for="elementTypeDropdown">Type:</label>
					<select id="elementTypeDropdown">
						<option value="aboutment">Aboutment</option>
						<option value="pile">Pile</option>
						<option value="bearing">Bearing</option>
						<option value="pier">Pier</option>
						<option value="arch">Arch</option>
						<option value="beam">Beam</option>
						<option value="insole">Insole</option>
						<option value="ancillary">Ancillary</option>
					</select>
				</div>
				<!--Material details-->
				<div id="materialContainer">
					<label for="materialDropdown">Material:</label>
					<select id="materialDropdown">
						<option value="ca">Reinforced concrete</option>
						<option value="cap">Prestressed reinforced concrete</option>
						<option value="masonry">Masonry</option>
						<option value="metal">Steel/Metal</option>
						<option value="wood">Wood</option>
					</select>
				</div>
				<label for="el_position">Geometry:</label>
				<div class="position-input-container">
					<!--<input type="text" id="elpos" name="elpos">-->
					<button id="pickElButton">Define element</button>
				</div>
				<button id="addNewElement">Add</button>
			</div>
		</div>
	</div>
	<!-- Filtering icon -->
	<img id="filter_icon" onclick="toggleFilter()" src="./assets/icons/filter.svg" title="Filter Options" />
	<!-- Toggle images icon -->
	<img id="toggle_images_icon" onclick="toggleImages()" src="./assets/icons/toggle_images.svg" title="Toggle Images" />
	<!-- Full screen mode -->
	<img id="fullscreen_icon" onclick="toggleFullScreen()" src="./assets/icons/fullscreen.svg" title="Fullscreen" />
	<!-- Navigation help -->
	<img id="question_icon" src="./assets/icons/question.svg" title="Tutorial" />
	<!-- Navigation instructions panel -->
	<div id="question_panel" class="hidden">
		<!-- Panel with instructions for navigation -->
		<img id="nav_instructions" src="./assets/icons/navigation3d.png" alt="">
	</div>
	<!-- Create annotation button -->
	<img id="addAnnotationBtn" src="./assets/icons/annotation-form.svg" style="filter: invert(0);"
		title="Add a new annotation" alt="Add a new annotation">
	<ul>
		<b>Annotations</b>
		<li>
			<div id="legend-structel" style="color:green;">Structural Elements</div>
		</li>
		<li>
			<div id="legend-defects" style="color: red;">Defects</div>
		</li>
	</ul>
	</div>
	<!-- Elements list -->
	<!-- Structural elements icon -->
	<img id="addElementBtn" src="./assets/icons/structural-element.svg" title="Add a new structural element"
		alt="Add a new structural element">


</body>

</html>