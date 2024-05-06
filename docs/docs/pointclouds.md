# Pointclouds integration

Before proceeding with this step, make sure you have finished the reconstruction processing of the 3D model of the bridge and obtained a point cloud of the structure in .las format. Once this product is obtained, you could convert the .las cloud using one of the method described in [this documentation of Potree](https://potree-templates.readthedocs.io/en/latest/pages/potree.html#pointcloud-conversion).

As a result, at the end of the procedure you will obtain a folder with the following structure:

```
converted_pointcloud_folder
|
│   hierarchy.bin
│   metadata.json
|	octree.bin    

```

Copy the whole folder and paste it inside the *pointclouds* folder. Then, open the [pointcloud.js](https://github.com/labmgf-polimi/ponti/blob/main/assets/js/pointcloud.js) file with a text editor.

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
