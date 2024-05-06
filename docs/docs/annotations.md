# Annotations integration

Once loaded the point cloud in the Web Viewer as described [here](#pointcloud-integration), it is possible to add custom annotations with simple tricks from the Potree sidebar. This functionality is particularly useful if it is needed to highlights particular parts of the structure or if it is necessary to integrate actions or media.

## Predefined annotations

Before working on the code, explore the point cloud in the viewer, activate the **[Point Measurement Tool](https://potree-templates.readthedocs.io/en/latest/pages/getting-started.html#measurements)** and double-click in correspondence of the point where you'd like to locate the annotation. Hence, explore the _Scene_ section in the Potree Sidebar and select the point measurement element. In the lower part of the section now you see the details of the measurement as well as the clicked point coordinates. Click on the copy icon next to the coordinates values: you will need this data to position your new annotation.

![Point measurement coords](img/point-measurement-coords.gif)

Then, it's time to open the [predefined-annotations.js](https://github.com/Tars4815/ponti/blob/main/js/predefined-annotations.js) file with a text editor to modify the position of the first default annotation. In order to do so, paste the copied coords within the squared brackets after **_position:_** in the code snippet below:

If you'd like to change the name or the description of the annotation, insert the desired texts according to the comment in the code.

Hence, to complete the procedure, you need to define the camera view to be set when the annotation is clicked in Potree. In order to do this, rotate and move the model view and look for the desired perspective. Then, in the _scene_ section of the sidebar, click on **Camera**: you will make visible a new Properties panel in which the coordinates linked to the camera _position_ and camera _target_ location that defines the actual view in the scene will be displayed. Copy and paste these values in the code according to the comment.

![Camera view coordinates](/img/ponti-camera-view.jpg "Camera view coordinates")

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