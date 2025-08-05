# Oriented cameras integration

Once loaded the point cloud in the Web Viewer as described [here](pointclouds.md), it is possible to include in the Viewer oriented cameras. This is particularly useful for showing particular portions of the structure and highlighting details on pictures taken from the drone and used for the reconstruction of the 3D model.

In order to load the images in the viewer platform, first copy and paste in the **img_selected** the folder containing:

* *Oriented images files*: they could be in any desired file formats: jpg, tif etc. Be sure that images are undistorted.

* *camera_parameters.xml*: this file includes information on the parameters of the camera adopted for taking the pictures used for the photogrammetric reconstruction. Be sure that *width* and *height* values match the ones of the chosen pictures. All the other parameters are set to 0 except the focal length.

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

* *orientedimages.txt*: this file in the first row contains the information about the coordinate system in which the images and the model have been georeferenced. Then, information about position and rotation of each single image file are listed associated to the filenames. Be sure that rotation angles are defined as Omega, Phi and Kappa.

```
# CoordinateSystem: PROJCS["WGS 84 / UTM zone 32N",GEOGCS["WGS 84",DATUM["World Geodetic System 1984",SPHEROID["WGS 84",6378137,298.257223563,AUTHORITY["EPSG","7030"]],TOWGS84[0,0,0,0,0,0,0],AUTHORITY["EPSG","6326"]],PRIMEM["Greenwich",0,AUTHORITY["EPSG","8901"]],UNIT["degree",0.01745329251994328,AUTHORITY["EPSG","9102"]],AUTHORITY["EPSG","4326"]],PROJECTION["Transverse_Mercator",AUTHORITY["EPSG","9807"]],PARAMETER["latitude_of_origin",0],PARAMETER["central_meridian",9],PARAMETER["scale_factor",0.9996],PARAMETER["false_easting",500000],PARAMETER["false_northing",0],UNIT["metre",1,AUTHORITY["EPSG","9001"]],AUTHORITY["EPSG","32632"]]
#Label X Y Z Omega Phi Kappa X_est Y_est Z_est Omega_est Phi_est Kappa_est
DJI_20221123144400_0268.jpg       593656.566250 5089108.835697 909.422444 69.797275 -11.168079 -4.525146
DJI_20221123144407_0271.jpg       593656.275265 5089107.376767 908.167646 76.005119 -11.476563 -3.278955
DJI_20221123144437_0284.jpg       593667.208196 5089110.605787 904.912258 116.932215 51.336722 -22.059546
DJI_20221123142343_0084.jpg       593661.475374 5089132.074970 917.423793 -52.683726 25.008452 161.764827
```

Once the files are copied in the target folder - in this example *img_selected/chunk* - it's time to modify the dedicated [orientedcameras.js](https://github.com/labmgf-polimi/ponti/blob/main/assets/js/orientedcameras.js) file according to the need of the case.

In the first lines of the script it is needed to declare the paths of both the camera parameters and oriented images files.

Then, the *OrientedImageLoader* function is applied and the images chunk is added to the scene. A useful tip could be defining also an *images.name* associated to the loaded chunk: this will help if advanced functions for hiding specific images or elements are later implemented in the template.

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