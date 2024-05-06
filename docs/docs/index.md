# Potree platfOrm for iNfrastructure Inspection (PONTI)

*Potree platfOrm for iNfrasTructure Inspection* (PONTI) is a custom Potree template for sharing survey products of provincial bridges.

The template is based on the open-source JavaScript library Potree by Markus Schütz.



## About

This template aims to simplify the procedure for building Potree-based platform for bridge survey data sharing.

The repository and template has been defined for implementing the following core features:

* **Pointcloud visualisation** with both RGB and classification appearance;
* **Oriented images on the model** for direct exploration of drone images used for the reconstruction;
* **Annotations definition** to highlight specific bridge elements, possibly embedding multimedia or actions in their descriptions.
* **Database integration** in a fully open source framework


## Getting started

To start, [sign in GitHub](https://github.com/login/) and navigate to the [**PONTI GitHub template**](https://github.com/labmgf-polimi/ponti), where you will see a green Use this template button. Click it to open a new page that will ask you for some details:

* Introduce an appropriate "Repository name".
* Make sure the project is "Public", rather than "Private".

After that, click on the green Create repository from template button, which will generate a new repository on your personal account (or the one of your choosing).

To work locally on the project before loading it to a server, instead click on the Code button and then select the Download ZIP option. After unzipping the downloaded folder, copy everything in the htdocs folder of the xampp directory of your device for working in your local development environment.

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

**_[index.php](https://github.com/Tars4815/ponti/blob/main/index.php)_**

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
