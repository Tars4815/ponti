# **PONTI Template**

**Potree platfOrm for iNfrasTructure Inspection** (PONTI) is a custom Potree template for sharing survey products of provincial bridges.

The template is based on the open-source JavaScript library [Potree](https://github.com/potree/potree) by Markus Schütz.

![Ponti example](./assets/PONTI-cover-image.jpg "PONTI example")

## **About** ℹ

This template aims to simplify the procedure for building Potree-based platform for bridge survey data sharing.

The repository and template has been defined for implementing the following features:

- **Pointcloud visualisation** with both RGB and classification appearance;
- **Oriented images** on the model for direct exploration of drone images used for the reconstruction;
- **Annotations** definition to highlight specific bridge elements, possibly embedding multimedia or actions in their descriptions.

## **Table of contents** 📋

1. **[Getting started](#getting-started)**
2. **[GUI Definition](#gui-definition)**
3. **[Pointclouds integration](#pointcloud-integration)**
4. **[Oriented cameras integration](#oriented-cameras-integration)**
5. **[Annotations integration](#annotations-integration)**
6. **[Extra features](#extra)**

---

## Getting started

To start, sign in [Github](https://github.com/login)
and navigate to the [PONTI GitHub template](https://github.com/labmgf-polimi/ponti),
where you will see a green **Use this template** button.
Click it to open a new page that will ask you for some details:

- Introduce an appropriate "_Repository name_".
- Make sure the project is "_Public_", rather than "_Private_".

After that, click on the green **Create repository from template** button,
which will generate a new repository on your personal account
(or the one of your choosing).

To work locally on the project before loading it to a server, instead click on the **Code** button and then select the _Download ZIP_ option. After unzipping the downloaded folder, copy everything in the htdocs folder of the xampp directory of your device for working in your local development environment.

This repository is structured as follows:

```
ponti
|
│   assets
    |   [images and samples for README]
    css
    |   style.css
    database
    |   delete_annotation.php
    |   insert_annotation.php
    |   load_annotations.php
    |   update_annotation.php
│   img_selected
    |   chunk1
        |   camera_parameters.xml
        |   oientedimages.txt
        chunk2
        |   camera_P1.txt
        |   camera_P1.xml
    js
    |   annotation.js
    |   orientedcameras.js
    |   pointcloud.js
    libs
    |   [dependencies' libs for Potree]
    licenses
    |   license_brotli.txt
    |   license_json.txt
    |   license_laszip.txt
    |   license_potree_converter.txt
    poinclouds (files not included in GitHub. Folder that needs to be filled with output of pointcloud conversion as follows)
    |   hierarchy.bin
    |   metadata.json
    |   octree.bin
|	index.php
|   LICENSE
|   README.md

```

Important files:

**_README.md_**

Basic description of the repository with instructions on how to replicate the PONTI template.

**_[index.php](index.php)_**

This will be the homepage of the PONTI viewer. It contains the basic settings for the GUI and includes the paths to all the style and js files.

- _CSS_ with the stylesheet in CSS language defined for including in the GUI a header with a description and/or logo.

- _JS_ that includes JavaScript files for loading 3D products in the viewer.

**_img_selected_**

This folder is used to store and collect the oriented images that the viewer developer is willing to integrate on the platform. Together with the picture files, camera certificates and images orientation parameters are saved in this space.

**_libs_**

All libraries' dependencies for making functionable the viewer are saved in the sub-folders.

**_licenses_**

License specifications for the used libraries are descripted here.

**_pointclouds_**

Converted point clouds and ancillary files should be saved in this folder to preserve the template structure.

**_database_**
It contains .php files that allow for the interaction between the platform and a PostgreSQL database. In particular, saving annotations

---

## GUI definition

The [index.php](index.php) file includes the main settings for the web page that contains the custom Potree viewer. For example, information contained in this file defines the **title** that will appear on the browser window when the page is loaded as well as other important **metadata** regarding the content and/or the author(s) of the page. These settings are defined in the first lines in the _head_ element:

```
...
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
...
```

When creating a new custom Potree viewer, change the content description according to your need as well as the content author. Then, change the text between the _title_ tag by putting the name and/or location of the surveyed bridge. Leave everything else unchanges.

An additional decoration of the main page consist in a banner on the upper part of the window with a custom text and, optionally, a logo. This element require a simple addition to the HTML and CSS page codes to define its content and appearance.

![PONTI banner example](./assets/PONTI-banner-example.jpg "PONTI banner example")

To style the header banner, in the [assets/css/style.css](https://github.com/labmgf-polimi/ponti/blob/main/assets/css/style.css) file the following CSS code is defined:

```
#header_panel {
    width: 100%;
    height: 5%;
    background-color: #19282C;
}

#header_title {
    color: #FFFFFF;
    font-size: 80%;
    font-weight: bold;
    position: relative;
    left: 100px;
    Top: 20px;
    font-family: Georgia, "Times New Roman", Times, serif;
}
```

The _#_ simbol before each name allows to define a specific style for specific div elements (through the so called _id_) in the viewer page definition. In particular:

- **header_panel** is set by default as a dark blue-grey (_background-color_) banner whose _width_ is always equal to the entire width of a web page in which the viewer is loaded, while its _height_ correspond to the 5% of the web page height.

- **header_title** is by default defining a white bold Georgia text (_color_, _font-weight_, _font-family_) whose position always refers to the div element in which it is contained.

In the [index.php](https://github.com/labmgf-polimi/ponti/blob/main/index.php) file the previously styled header banner is defined in the body section. To change the title to be displayed on the top of the page, simply change the text included within the div _header_title_ element.

```
<!-- Defining header with title -->
	<div id="header_panel">
		<div id="header_title">
			PONTI Template - Example of a Bridge 3D data exploration
		</div>
	</div>
```

## Pointcloud integration

Before proceeding with this step, make sure you have finished the reconstruction processing of the 3D model of the bridge and obtained a point cloud of the structure in .las format. Once this product is obtained, you could convert the .las cloud using one of the method described in [this documentation of Potree](https://potree-templates.readthedocs.io/en/latest/pages/potree.html#pointcloud-conversion).

As a result, at the end of the procedure you will obtain a folder with the following structure:

```
converted_pointcloud_folder
|
│   hierarchy.bin
│   metadata.json
|	octree.bin

```

Copy the whole folder and paste it inside the _pointclouds_ folder. Then, open the [pointcloud.js](https://github.com/labmgf-polimi/ponti/blob/main/js/pointcloud.js) file with a text editor.

Now you need to refer to the newly converted file in this js code file, enabling its correct visualization in the Potree Viewer. In order to do so, look for the _Loading point cloud data and its setting for rendering in Potree Viewer_ comment section in the script.
This part of the file load the pointcloud in json format through the **_loadPointCloud_** function. In order to correctly refer to the newly converted cloud and visualise it in RGB mode, modify the code as below:

```

Potree.loadPointCloud("./pointclouds/converted_pointcloud_folder/metadata.json", "Bridge cloud", e => {
    let pointcloud = e.pointcloud;
    let material = pointcloud.material;
    material.size = 0.6;
    material.pointSizeType = Potree.PointSizeType.ADAPTIVE;
    material.shape = Potree.PointShape.CIRCLE;
    material.activeAttributeName = "rgba"; // change this value to "classification" and uncomment the next 2 lines if you desire to show the classified point cloud
    // material.intensityRange = [1, 100];
    // material.gradient = Potree.Gradients.RAINBOW;
    bridgescene.addPointCloud(pointcloud);
    viewer.setFrontView();
});

```

In this way the cloud will be correctly loaded. Change "_Bridge cloud_" to a name of your choice if you'd like to change its name as visualised in the sidebar scene section.

Additionally, in the pointcloud.js file, in the following section update the _INSERT TEXT HERE_ content if you're interested in mentioning author(s) of the point cloud survey and/or data processing in a dedicated _Credits_ section in the sidebar.

```
viewer.loadGUI(() => {
    viewer.setLanguage('en');
    viewer.toggleSidebar();
    $("#menu_appearance").next().show();
    $("#menu_tools").next().show();
    /* Creating a new sidebar section for credits */
    let section = $(`<h3 id="menu_meta" class="accordion-header ui-widget"><span>Credits</span></h3><div class="accordion-content ui-widget pv-menu-list"></div>`);
    let content = section.last();
    content.html(`
    <div class="pv-menu-list">
        <li>INSERT TEXT HERE</li>
    </div>
    `);
    content.hide();
    section.first().click(() => content.slideToggle());
    section.insertBefore($('#menu_appearance'));
});
```

## Oriented cameras integration

Once loaded the point cloud in the Web Viewer as described [here](#pointcloud-integration), it is possible to include in the Viewer oriented cameras. This is particularly useful for showing particular portions of the structure and highlighting details on pictures taken from the drone and used for the reconstruction of the 3D model.

In order to load the images in the viewer platform, first copy and paste in the **img_selected** the folder containing:

- _Oriented images files_: they could be in any desired file formats: jpg, tif etc. Be sure that images are undistorted.

- _camera_parameters.xml_: this file includes information on the parameters of the camera adopted for taking the pictures used for the photogrammetric reconstruction. Be sure that _width_ and _height_ values match the ones of the chosen pictures. All the other parameters are set to 0 except the focal length.

```
<?xml version="1.0" encoding="UTF-8"?>
<calibration>
  <projection>frame</projection>
  <width>8192</width>
  <height>5460</height>
  <f>8215.93777</f>
  <cx>0</cx>
  <cy>0</cy>
  <b1>0</b1>
  <b2>0</b2>
  <k1>0</k1>
  <k2>0</k2>
  <k3>0</k3>
  <date>2022-05-26T08:27:27Z</date>
</calibration>
```

- _orientedimages.txt_: this file in the first row contains the information about the coordinate system in which the images and the model have been georeferenced. Then, information about position and rotation of each single image file are listed associated to the filenames. Be sure that rotation angles are defined as Omega, Phi and Kappa.

```
# CoordinateSystem: PROJCS["WGS 84 / UTM zone 32N",GEOGCS["WGS 84",DATUM["World Geodetic System 1984",SPHEROID["WGS 84",6378137,298.257223563,AUTHORITY["EPSG","7030"]],TOWGS84[0,0,0,0,0,0,0],AUTHORITY["EPSG","6326"]],PRIMEM["Greenwich",0,AUTHORITY["EPSG","8901"]],UNIT["degree",0.01745329251994328,AUTHORITY["EPSG","9102"]],AUTHORITY["EPSG","4326"]],PROJECTION["Transverse_Mercator",AUTHORITY["EPSG","9807"]],PARAMETER["latitude_of_origin",0],PARAMETER["central_meridian",9],PARAMETER["scale_factor",0.9996],PARAMETER["false_easting",500000],PARAMETER["false_northing",0],UNIT["metre",1,AUTHORITY["EPSG","9001"]],AUTHORITY["EPSG","32632"]]
#Label X Y Z Omega Phi Kappa X_est Y_est Z_est Omega_est Phi_est Kappa_est
DJI_20221123144400_0268.jpg       593656.566250 5089108.835697 909.422444 69.797275 -11.168079 -4.525146
DJI_20221123144407_0271.jpg       593656.275265 5089107.376767 908.167646 76.005119 -11.476563 -3.278955
DJI_20221123144437_0284.jpg       593667.208196 5089110.605787 904.912258 116.932215 51.336722 -22.059546
DJI_20221123142343_0084.jpg       593661.475374 5089132.074970 917.423793 -52.683726 25.008452 161.764827
```

Once the files are copied in the target folder - in this example _img_selected/chunk_ - it's time to modify the dedicated [orientedcameras.js](https://github.com/labmgf-polimi/ponti/blob/main/js/orientedcameras.js) file according to the need of the case.

In the first lines of the script it is needed to declare the paths of both the camera parameters and oriented images files.

Then, the _OrientedImageLoader_ function is applied and the images chunk is added to the scene. A useful tip could be defining also an _images.name_ associated to the loaded chunk: this will help if advanced functions for hiding specific images or elements are later implemented in the template.

```
/* Loading oriented images chunks */
/* First chunk of images */
/* Setting the paths for camera parameters and images list */
const cameraParamsPathPila1 = "./img_selected/chunk1/camera_parameters.xml";
const imageParamsPathPila1 = "./img_selected/chunk1/orientedimages.txt";

Potree.OrientedImageLoader.load(cameraParamsPathPila1, imageParamsPathPila1, viewer).then(images => {
    images.visible = true; // change this to false if you'd like to hide images at first loading of the page
    viewer.scene.addOrientedImages(images);
    images.name = 'chunk1';
});
[...]
```

If you'd like to define another oriented images chunk, copy the entire code block of the first chunk and paste it right in first row below it in the js script. Then modify it according to your needs.

N.B.: Variable and constant names should be unique!

## Annotations integration

Once loaded the point cloud in the Web Viewer as described [here](#pointcloud-integration), it is possible to add custom annotations with simple tricks from the Potree sidebar. This functionality is particularly useful if it is needed to highlights particular parts of the structure or if it is necessary to integrate actions or media.

### Predefined annotations

Before working on the code, explore the point cloud in the viewer, activate the **[Point Measurement Tool](https://potree-templates.readthedocs.io/en/latest/pages/getting-started.html#measurements)** and double-click in correspondence of the point where you'd like to locate the annotation. Hence, explore the _Scene_ section in the Potree Sidebar and select the point measurement element. In the lower part of the section now you see the details of the measurement as well as the clicked point coordinates. Click on the copy icon next to the coordinates values: you will need this data to position your new annotation.

![Point measurement coords](./assets/point-measurement-coords.gif)

Then, it's time to open the [predefined-annotations.js](js/predefined-annotations.js) file with a text editor to modify the position of the first default annotation. In order to do so, paste the copied coords within the squared brackets after **_position:_** in the code snippet below:

If you'd like to change the name or the description of the annotation, insert the desired texts according to the comment in the code.

Hence, to complete the procedure, you need to define the camera view to be set when the annotation is clicked in Potree. In order to do this, rotate and move the model view and look for the desired perspective. Then, in the _scene_ section of the sidebar, click on **Camera**: you will make visible a new Properties panel in which the coordinates linked to the camera _position_ and camera _target_ location that defines the actual view in the scene will be displayed. Copy and paste these values in the code according to the comment.

![Camera view coordinates](./assets/ponti-camera-view.jpg "Camera view coordinates")

```
/* Annotations definition */
{// Annotation 1
    let Title01 = $(`
                <span>
                    Annotation 1
                </span>
                `); //Substitute "Annotation 1" with the desired Title text for your annotation
    let annotation01 = new Potree.Annotation({
        position: [593673.870, 5089120.772, 910.538],
        title: Title01,
        cameraPosition: [593661.279, 5089117.043, 907.581], //Substitute these values with the position ones obtained by clicking on the camera object in the scene sidebar section
        cameraTarget: [593673.870, 5089120.772, 910.538], //Substitute these values with the target ones obtained by clicking on the camera object in the scene sidebar section
        description: 'INSERT DESCRIPTION HERE' //Change the content of this according to the desired description
    })
    annotation01.visible = true; // Change this to false if you want to hide the annotations at first loading
    bridgescene.annotations.add(annotation01);
    Title01.toString = () => "Annotation 1"; //Substitute "Annotation 1" with the desired Title text for your annotation: this will be shown in the scene sidebar section
}
...
```

If you'd like to define another annotation, copy the entire code block of the first annotation and paste it right in first row below it in the js script. Then modify it according to your need.

N.B.: Variable names should be unique!

### Database-connected annotations

The workflow described in the previous section describes in details how to implement _a-priori_ annotations but limits the possibility of updating the viewer with users-provided information. Indeed, with such a setting, it is not possible to consistently store, update or delete existing annotations, as ny user interactions is resetted when a new web session in the web viewer is initialised.

In order to have _dynamic_ annotations that can be easily created, edited and delated by any user and whose modification are "remembered" by the viewer, it is needed to introduce a connection to a database. For this application, it has been used _PostgreSQL_. In particular, an _annotations_ table has to be defined inside the database schema having the following minimal structure made with SQL language:

```

-- Table: public.annotations

CREATE TABLE IF NOT EXISTS public.annotations
(
    id integer NOT NULL DEFAULT nextval('annotations_id_seq'::regclass),
    title character varying(100) COLLATE pg_catalog."default" NOT NULL,
    pos_x numeric,
    pos_y numeric,
    pos_z numeric,
    campos_x numeric,
    campos_y numeric,
    campos_z numeric,
    tarpos_x numeric,
    tarpos_y numeric,
    tarpos_z numeric,
    description character varying(255) COLLATE pg_catalog."default",
    typology character varying(50) COLLATE pg_catalog."default",
    CONSTRAINT annotations_pkey PRIMARY KEY (id)
)

```

Such table will contains all the needed information to define an annotation in a Potree scene. In particular:

- _title_
- _position_
- _camera position_
- _target position_
- _description_

Such fields of the table will be filled, read, edited or deleted according to any operations conducted on annotations by any users of P.O.N.T.I.. In particular, specific routine operation are linked to annotation objects:

- [Loading existing annotations](#loading-existing-annotations), dedicated to first look for existing records in the _annotations_ table and then loading of record found in the PONTI scene
- [Creating new annotations](#creating-new-annotations) to insert a new annotation object in the scene as well as on the dedicated table of the database
- [Updating existing annotations](#updating-existing-annotations) for updating annotations in the PONTI viewer and saving user edits in the database
- [Deleting annotations](#deleting-annotations), focused on aligning removal of annotations in the viewer with deletion of records in the database

#### Loading existing annotations

A first operation that does not require any user interactions in PONTI but that is needed to establish the connection with the database is the loading of the annotations information that already populate the _annotations_ table.

For this reason it is needed to first define a JS function in the dedicated _[annotations.js](js/annotations.js)_ file.

```

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
  // Set the annotation type-specific styles
  setAnnotationStyles(annotation, annotationType);
  // Set the annotation to be visible
  annotation.visible = true;
  // Add the annotation to the scene
  scene.annotations.add(annotation);
  // Override toString method for the title element
  titleElement.toString = () => titleText;
}

```

Such function is used everytime an annotation object, for any reasons, need to be initialised and visualised in the scene. In this particular case, it is called in cascade using the response of a query on the all the records present in the _annotations_ table. In order to do so, it is needed to set properly the _[load_annotations.php](database/load_annotations.php)_ file. In particular, this script fetches annotations from a PostgreSQL database, processes the data, and returns the result as a JSON-encoded response. Depending on your local/server setup hosting the db with the _annotations_ table, you need to edit to your needs the following part of the code:

```
...
$connection = pg_connect("host=yourhost port=yourport dbname=yourdbname user=username password=yourpassword);
...
```

Then, after checking a successful connection with the provided credentials, a query for obtaining all the records that populates the _annotations_ table in your database. The result of that is subsequently stored in the response and numerical values of different columns are properly interpreted as float values.

```
...
if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Fetch existing annotations from the database
$query = "SELECT * FROM annotations ORDER BY id";
$result = pg_query($connection, $query);

$annotations = array();
while ($row = pg_fetch_assoc($result)) {
    $row['pos_x'] = floatval($row['pos_x']);
    $row['pos_y'] = floatval($row['pos_y']);
    $row['pos_z'] = floatval($row['pos_z']);
    $row['campos_x'] = floatval($row['campos_x']);
    $row['campos_y'] = floatval($row['campos_y']);
    $row['campos_z'] = floatval($row['campos_z']);
    $row['tarpos_x'] = floatval($row['tarpos_x']);
    $row['tarpos_y'] = floatval($row['tarpos_y']);
    $row['tarpos_z'] = floatval($row['tarpos_z']);
    $annotations[] = $row;
}

// Close the database connection
pg_close($connection);

// Return the annotations as JSON
echo json_encode($annotations);
?>
```

The successfull response is dealt in the [annotation.js](js/annotations.js) file a set of operations for getting needed records from the _annotations_ table in the database. The different column values of the results are then used as arguments for creating annotation objects in the scene through the _createAnnotation()_ function.

```

// Load existing annotations from the server
$.ajax({
  type: "GET",
  url: "database/load_annotations.php", // Adjust the URL based on your file structure
  dataType: "json",
  success: function (existingAnnotations) {
    // Assuming bridgescene is available globally, adjust if needed
    let scene = bridgescene;

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

```

#### Creating new annotations

Users working on the PONTI interface might need to add new annotations, possibly referring to structural elements, defects or general comments. In order to save user interactions of this type, it is needed to include in the code CREATE functions able to store in the connected database the parameters associated to annotation objects newly created in the Potree scene.

First, it is needed to define a custom form in the main GUI, allowing users to choose within possible annotation types to be added and then to define annotation parameters (title, position, camera settings, description). A new button for this purpose is created in [index.php](index.php).

```
<img id="addAnnotationBtn" src="libs\potree\resources\icons\new-annotation.svg" style="filter: invert(0);"
		title="Add a new annotation" alt="Add a new annotation">

```

When clicked, it will show a 2-sections form structured as follow (always in index.php) inside the _potree_container_ div:

```
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

			<button id="submitAnnotation">Submit</button>
			<button id="editAnnotation">Edit</button>
		</div>
```

At the end of the form two types of buttons are present:

- **Submit**: it will be shown when a CREATE annotation operation is triggered;
- **Edit**: its visibility will be activated for UPDATE annotation operations (that will be described later).

Let's now define the appearance and the style of the Edit (and Delete too) buttons in [style.css](css/style.css):

```
/* Styling custom form for new annotation */
.custom-form {
    position: absolute;
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    margin: 0;
    width: 300px;
    height: 400px;
    cursor: pointer;
    background-color: #FFFFFF;
    right: 0%;
    bottom: 1.2em;
}

/* Style for the Submit button */
#submitAnnotation {
    background-color: green;
    color: white;
    border: 1px solid white; /* Border color when not hovered */
    padding: 5px 10px; /* Adjust padding as needed */
    cursor: pointer;
    transition: border 0.3s ease-in-out; /* Transition effect on hover */
    display: none;
}

/* Hover effect for the Submit button */
#submitAnnotation:hover {
    border: 2px solid yellow; /* Border color on hover */
}

/* Style for the Submit button */
#editAnnotation {
    background-color: blue;
    color: white;
    border: 1px solid white; /* Border color when not hovered */
    padding: 5px 10px; /* Adjust padding as needed */
    cursor: pointer;
    transition: border 0.3s ease-in-out; /* Transition effect on hover */
    display: none;
}

/* Hover effect for the Submit button */
#editAnnotation:hover {
    border: 2px solid yellow; /* Border color on hover */
}
```

Now, let's define the functions associated to the _addAnnotationBtn_ as well as for _submitTypeBtn_, _pickPointButton_ and _submitAnnotation_ inside the form.
In [annotations.js](js/annotations.js) the following code snippet is responsible for handling click events on the _addAnnotationBtn_ and changing the visibility and style of form panel:

```
//CODE FOR CUSTOM FORM//
$(document).ready(function () {
  // Add a click event handler to the #addAnnotationBtn button
  $("#addAnnotationBtn").click(function () {
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

      // Display the submit button
      submitButton = document.getElementById("submitAnnotation");
      submitButton.style.display = "flex";
    });
  });
});
```

Hence, the function to be triggered when _pickPointButton_ is clicked is defined. The handler initiates the measuring tool to pick a single point and updates the input box with the selected point's coordinates.

```
$("#pickPointButton").click(function () {
  const measurement = viewer.measuringTool.startInsertion({
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
```

Then, the _submitAnnotation_ button click event handler is coded as follows:

```
// Add a click event handler to the #submitAnnotation button
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
```

Such click event triggers a _saveAnnotation_ function that save the new object in the database and create a corresponding annotation in the scene.

```
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
        bridgescene, // Assuming bridgescene is accessible globally
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
```

Inside the function, a POST operation is triggered, recalling the [insert_annotation.php](database/insert_annotation.php) file that, connecting to the PostgreSQL database with the given credentials, store each defined annotation parameters in the corresponding table columns. As a result, it also echoes the newly created annotation id that will be used to univocally associated a new annotation in the Potree to its table rows in the database.

```
<?php
$connection = pg_connect("host=yourhost port=yourport dbname=yourdbname user=username password=yourpassword");

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = pg_escape_string($_POST["title"]);
    $description = pg_escape_string($_POST["description"]);
    $pos_x = (float)$_POST["pos_x"];
    $pos_y = (float)$_POST["pos_y"];
    $pos_z = (float)$_POST["pos_z"];
    $campos_x = (float)$_POST["campos_x"];
    $campos_y = (float)$_POST["campos_y"];
    $campos_z = (float)$_POST["campos_z"];
    $tarpos_x = (float)$_POST["tarpos_x"];
    $tarpos_y = (float)$_POST["tarpos_y"];
    $tarpos_z = (float)$_POST["tarpos_z"];
    $typology = pg_escape_string($_POST["typology"]);

    // Use prepared statements to prevent SQL injection
    $query = "INSERT INTO annotations (title, description, pos_x, pos_y, pos_z, campos_x, campos_y, campos_z, tarpos_x, tarpos_y, tarpos_z, typology) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12) RETURNING id";
    $result = pg_query_params($connection, $query, array(
        $title, $description, $pos_x, $pos_y, $pos_z,
        $campos_x, $campos_y, $campos_z, $tarpos_x, $tarpos_y, $tarpos_z, $typology
    ));

    if (!$result) {
        echo "Error inserting data: " . pg_last_error($connection);
    } else {
        // Fetch the inserted ID and echo it in the response
        $insertedRow = pg_fetch_assoc($result);
        $insertedId = $insertedRow['id'];
        echo $insertedId;
    }
}

pg_close($connection);
?>

```

The newly created annotation is now included in the database as well as in the scene, even after page refresh.

#### Updating existing annotations

Another useful operation for annotations is the UPDATE one. In order to do so, first an *Edit button* (as well as a *Delete button* that will be explained in the next DELETE annotation section) has been added in the Potree annotation constructor in [potree.js](libs/potree/potree.js) when the structure for the *annotation-description* is defined:

```
// Create Edit & Delete buttons
			this.domElement = $(`
			<div class="annotation" oncontextmenu="return false;">
				<div class="annotation-titlebar">
					<span class="annotation-label"></span>
				</div>
				<div class="annotation-description">
					<span class="annotation-description-close">
						<img src="${iconClose}" width="16px">
			
					</span>
					<span class="annotation-description-content">${this._description}</span><br>
					<button class="annotation-edit-button">Edit</button>
					<button class="annotation-delete-button">Delete</button>
				</div>
			</div>
		`);
    // Find the button after the element has been added to the DOM
			let editButton = this.domElement.find('.annotation-edit-button');
			editButton.click(() => showEditForm(this));
			// Find the button after the element has been added to the DOM
			let deleteButton = this.domElement.find('.annotation-delete-button');
			deleteButton.click(() => deleteAnnotation(this));
```

Let's now define the appearance and the style of the Edit (and Delete too) buttons in [style.css](css/style.css):

```
/* Style for the Edit button */
.annotation-edit-button {
    background-color: black;
    color: white;
    border: 1px solid white; /* Border color when not hovered */
    padding: 5px 10px; /* Adjust padding as needed */
    cursor: pointer;
    transition: border 0.3s ease-in-out; /* Transition effect on hover */
}

/* Hover effect for the Edit button */
.annotation-edit-button:hover {
    border: 1px solid yellow; /* Border color on hover */
}

/* Style for the Edit button */
.annotation-delete-button {
    background-color: red;
    color: white;
    border: 1px solid white; /* Border color when not hovered */
    padding: 5px 10px; /* Adjust padding as needed */
    cursor: pointer;
    transition: border 0.3s ease-in-out; /* Transition effect on hover */
}

/* Hover effect for the Edit button */
.annotation-delete-button:hover {
    border: 1px solid yellow; /* Border color on hover */
}
```

Then, let's define the set of functions needed when the annotation-edit button is clicked. The needed step for a proper UPDATE operation now are:
1. Open the Edit form and populate each input boxes with the current paramters values;
2. Remove from the scene the old annotation object and pass the new parameters to the database in order to update the annotation record in the corresponding table (_removeAnnotationFromScene()_ and _updateAnnotationInDatabase()_).

First, *showEditForm()*, defined in [annotations.js](js/annotations.js):

```
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
    document.getElementById("editAnnotation")
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
```

The _removeAnnotationFromScene()_ is simply defined as follows:

```
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
```

While the _updateAnnotationInDatabase()_ executes the following operations passing the parameters to the database:

```
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
        bridgescene, // Assuming bridgescene is accessible globally
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
```

The [update_annotation.php](database/update_annotation.php) file deals with the updated annotation parameters and, identifying the record to be modified through the annotation id, save the new values in the database.

```
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $newTitle = $_POST["newTitle"];
    $newDescription = $_POST["newDescription"];
    $newPosition = $_POST["newPositionArray"];
    $camPosition = $_POST["camPositionArray"];
    $camTarget = $_POST["camTargetArray"]; 
    $typology = $_POST["typology"];

    // Update data in the annotations table
    $query = "UPDATE annotations SET 
    title = '$newTitle', 
    description = '$newDescription', 
    pos_x = " . explode(',', $newPosition)[0] . ",
    pos_y = " . explode(',', $newPosition)[1] . ",
    pos_z = " . explode(',', $newPosition)[2] . ",
    campos_x = " . explode(',', $camPosition)[0] . ",
    campos_y = " . explode(',', $camPosition)[1] . ",
    campos_z = " . explode(',', $camPosition)[2] . ",
    tarpos_x = " . explode(',', $camTarget)[0] . ",
    tarpos_y = " . explode(',', $camTarget)[1] . ",
    tarpos_z = " . explode(',', $camTarget)[2] . ",
    typology = '$typology'
    WHERE id = $id";

    $result = pg_query($connection, $query);

    if (!$result) {
        echo "Error inserting data: " . pg_last_error($connection);
    } else {
        // Fetch the inserted ID and echo it in the response
        $insertedRow = pg_fetch_assoc($result);
        $insertedId = $insertedRow['id'];
        echo $insertedId;
    }
}
```

#### Deleting annotations

The last operation to deal with for saving user interactions on annotations is 
[TESTO]

## **Extra** 🌟

Features currently under development/improvement:

- [x] Database connection
- [x] DB sync of new annotations
- [x] DB sync of modified annotations
- [x] DB sync of deleted annotations
- [x] DB sync for loading existing annotations
- [x] Definition of custom form for annotation creation/modification
- [ ] Definition of different annotation classes

[⚠ Section under construction ⚠]

## **Acknowledgements**

This template and its functionalities are the results of the research activities conducted by the LabMGF group in the context of collaborations with Provincia di Piacenza and Provincia di Brescia.

## **References**

### **Publications**

- [Potree: Rendering Large Point Clouds in Web Browsers](https://www.cg.tuwien.ac.at/research/publications/2016/SCHUETZ-2016-POT/SCHUETZ-2016-POT-thesis.pdf) (2016)
- [Fast Out-of-Core Octree Generation for Massive Point Clouds](https://www.cg.tuwien.ac.at/research/publications/2020/SCHUETZ-2020-MPC/) (2020)
- Gaspari, F., Barbieri, F., Duque, J. P., Fascia, R., Ioli, F., Zani, G., Carrion, D., and Pinto, L.: **A GEO-DATABASE FOR 3D-AIDED MULTI-EPOCH DOCUMENTATION OF BRIDGE INSPECTIONS**, _Int. Arch. Photogramm. Remote Sens. Spatial Inf. Sci._, XLVIII-1/W2-2023, 299–306, https://doi.org/10.5194/isprs-archives-XLVIII-1-W2-2023-299-2023, 2023
- Fascia, R., Barbieri, F., Gaspari, F., Ioli, L., Pinto, L.: **PONTI: an open WebGL-based tool in support to defect analysis and 3D visualisation of bridges**, _Structure and Infrastructure Engineering - Maintenance, Management, Life-Cycle Design & Performance_, to be published in June 2024

### **Presentations**

- **GeoDaysIT 2023** Bari, June 16th 2023 - [Potree platform for infrastructure inspection: una soluzione WebGL open-source a supporto del rilievo e dell’analisi difettologica di ponti e viadotti](https://talks.osgeo.org/foss4g-it-2023/talk/YFKDDS/), *Gaspari, F., Fascia, R.*
- **SIFET Congress 2023** Arezzo, September 28th 2023 - Potree platform for infrastructure inspection: una soluzione WebGL open-source a supporto del rilievo e dell’analisi difettologica di ponti e viadotti, *Gaspari, F.*
- **3DMetrica Live Series** [ITA] -  [Web 3D Open Source per la geomatica - Rilievo e Monitoraggio](https://www.youtube.com/watch?v=bmTWKltLXgw&t=297s) live talk by Federica Gaspari on Paolo Corradeghini's YouTube channel in collaboration with SIFET.
- **IABMAS 2024** Copenhaghen, June 2024 - PONTI: an open WebGL-based tool in support to defect analysis and 3D visualisation of bridges