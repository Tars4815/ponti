# **protree**

A custom Potree template for sharing survey products of provincial bridges.

![Protree example](./assets/protree-cover-image.jpg "Protree example")

## **About**

This template aims to simplify the procedure for building Potree-based platform for bridge survey data sharing.

The repository and template has been defined for implementing the following features:

* **Pointcloud visualisation** with both RGB and classification appearance;
* **Oriented images** on the model for direct exploration of drone images used for the reconstruction;
* **Annotations** definition to highlight specific bridge elements, possibly embedding multimedia or actions in their descriptions.

------------------------

## **Getting started**

To start, sign in [Github](https://github.com/login)
and navigate to the [Protree GitHub template](https://github.com/Tars4815/protree),
where you will see a green **Use this template** button.
Click it to open a new page that will ask you for some details:

* Introduce an appropriate "*Repository name*".
* Make sure the project is "*Public*", rather than "*Private*".

After that, click on the green **Create repository from template** button,
which will generate a new repository on your personal account
(or the one of your choosing).

To work locally on the project before loading it to a server, instead click on the **Code** button and then select the *Download ZIP* option. After unzipping the downloaded folder, copy everything in the htdocs folder of the xampp directory of your device for working in your local development environment. 

This repository contains the following files:

***README.md***

Basic description of the repository with instructions on how to replicate the Protree template.

***index.html***

This will be the homepage of the Protree viewer. It contains the basic settings for the GUI and includes the paths to all the style and js files.

***assets***

Decorative images and icons are collected in this folder. Additionally, two others subfolders contain files that are important to define the appearance and the custom functionalities of the viewer:

* *CSS* with the stylesheet in CSS language defined for including in the GUI a header with a description and/or logo.

* *JS* that includes JavaScript files for loading 3D products in the viewer.

***img_selected***

This folder is used to store and collect the oriented images that the viewer developer is willing to integrate on the platform. Together with the picture files, camera certificates and images orientation parameters are saved in this space.

***libs***

All libraries' dependencies for making functionable the viewer are saved in the sub-folders.

***licenses***

License specifications for the used libraries are descripted here.

***pointclouds***

Converted point clouds and ancillary files should be saved in this folder to preserve the template structure.

---------------------

## **GUI definition**

The [index.html](https://github.com/Tars4815/protree/blob/main/index.html) file includes the main settings for the web page that contains the custom Potree viewer. For example, information contained in this file defines the **title** that will appear on the browser window when the page is loaded as well as other important **metadata** regarding the content and/or the author(s) of the page. These settings are defined in the first lines in the *head* element:

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
When creating a new custom Potree viewer, change the content description according to your need as well as the content author. Then, change the text between the *title* tag by putting the name and/or location of the surveyed bridge. Leave everything else unchanges.

An additional decoration of the main page consist in a banner on the upper part of the window with a custom text and, optionally, a logo. This element require a simple addition to the HTML and CSS page codes to define its content and appearance.

![Protree banner example](./assets/protree-banner-example.jpg "Protree banner example")

To style the header banner, in the [assets/css/style.css](https://github.com/Tars4815/protree/blob/main/assets/css/style.css) file the following CSS code is defined:

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
The *#* simbol before each name allows to define a specific style for specific div elements (through the so called *id*) in the viewer page definition. In particular:

* **header_panel** is set by default as a dark blue-grey (*background-color*) banner whose *width* is always equal to the entire width of a web page in which the viewer is loaded, while its *height* correspond to the 5% of the web page height.

* **header_title** is by default defining a white bold Georgia text (*color*, *font-weight*, *font-family*) whose position always refers to the div element in which it is contained. 

In the [index.html](https://github.com/Tars4815/protree/blob/main/index.html) file the previously styled header banner is defined in the body section. To change the title to be displayed on the top of the page, simply change the text included within the div *header_title* element. 

```
<!-- Defining header with title -->
	<div id="header_panel">
		<div id="header_title">
			Protree Template - Example of a Bridge 3D data exploration
		</div>
	</div>
```

## **Pointcloud integration**

Before proceeding with this step, make sure you have finished the reconstruction processing of the 3D model of the bridge and obtained a point cloud of the structure in .las format. Once this product is obtained, you could convert the .las cloud using one of the method described in [this documentation of Potree](https://potree-templates.readthedocs.io/en/latest/pages/potree.html#pointcloud-conversion).

As a result, at the end of the procedure you will obtain a folder with the following structure:

```
converted_pointcloud_folder
|
│   hierarchy.bin
│   metadata.json
|	octree.bin    

```

Copy the whole folder and paste it inside the *pointclouds* folder. Then, open the [pointcloud.js](https://github.com/Tars4815/protree/blob/main/assets/js/pointcloud.js) file with a text editor.

Now you need to refer to the newly converted file in this js code file, enabling its correct visualization in the Potree Viewer. In order to do so, look for the *Loading point cloud data and its setting for rendering in Potree Viewer* comment section in the script.
This part of the file load the pointcloud in json format through the ***loadPointCloud*** function. In order to correctly refer to the newly converted cloud and visualise it in RGB mode, modify the code as below:

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

In this way the cloud will be correctly loaded. Change "*Bridge cloud*" to a name of your choice if you'd like to change its name as visualised in the sidebar scene section.

Additionally, in the pointcloud.js file, in the following section update the *INSERT TEXT HERE* content if you're interested in mentioning author(s) of the point cloud survey and/or data processing in a dedicated *Credits* section in the sidebar.

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

## **Oriented cameras integration**

[TO DO]

## **Annotations integration**

Once loaded the point cloud in the Web Viewer as described [here](#pointcloud-integration), it is possible to add custom annotations with simple tricks from the Potree sidebar. This functionality is particularly useful if it is needed to highlights particular parts of the structure or if it is necessary to integrate actions or media.

Before working on the code, explore the point cloud in the viewer, activate the **[Point Measurement Tool](https://potree-templates.readthedocs.io/en/latest/pages/getting-started.html#measurements)** and double-click in correspondence of the point where you'd like to locate the annotation. Hence, explore the *Scene* section in the Potree Sidebar and select the point measurement element. In the lower part of the section now you see the details of the measurement as well as the clicked point coordinates. Click on the copy icon next to the coordinates values: you will need this data to position your new annotation.

![Point measurement coords](./assets/point-measurement-coords.mp4)

<video src="./assets/point-measurement-coords.mp4" controls="controls" style="max-width: 400px;"></video>

Then, it's time to open the [annotations.js](https://github.com/Tars4815/protree/blob/main/assets/js/annotations.js) file with a text editor to modify the position of the first default annotation. In order to do so, paste the copied coords within the squared brackets after ***position:*** in the code snippet below:

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
        cameraPosition: [593661.279, 5089117.043, 907.581],
        cameraTarget: [593673.870, 5089120.772, 910.538],
        description: 'Visualizza tutte le foto di questo elemento cliccando sul simbolo <img src="./libs/potree/resources/icons/orbit_controls.svg" name="pila1_foto" class="annotation-action-icon" /></a> '
    })
    annotation01.visible = true; // Change this to false if you want to hide the annotations at first loading
    bridgescene.annotations.add(annotation01); 
    Title01.toString = () => "Annotation 1"; //Substitute "Annotation 1" with the desired Title text for your annotation: this will be shown in the scene sidebar section
}
...
```

If you'd like to change the name or the description of the annotation, insert the desired texts according to the comment in the code.

[TO DO]

## **Extra**

[TO DO]

## **Acknowledgements**

This template and its functionalities are the results of the research activities conducted by the LabMGF group in the context of collaborations with Provincia di Piacenza and Provincia di Brescia.