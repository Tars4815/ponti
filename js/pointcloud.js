import * as THREE from "../libs/three.js/build/three.module.js";

/* Initializing Cesium Viewer */
window.cesiumViewer = new Cesium.Viewer("cesiumContainer", {
  useDefaultRenderLoop: false,
  animation: false,
  baseLayerPicker: false,
  fullscreenButton: false,
  geocoder: false,
  homeButton: false,
  infoBox: false,
  sceneModePicker: false,
  selectionIndicator: false,
  timeline: false,
  navigationHelpButton: false,
  imageryProvider: Cesium.createOpenStreetMapImageryProvider({
    url: "https://a.tile.openstreetmap.org/",
  }),
});

/* Loading 3D terrain from Maptiler */
cesiumViewer.terrainProvider = new Cesium.CesiumTerrainProvider({
  url: "https://api.maptiler.com/tiles/terrain-quantized-mesh/?key=2hTOFLPdXApzq9gVeMKq", // get your own key at https://cloud.maptiler.com/
});

/* Camera settings for Cesium viewer */
let cp = new Cesium.Cartesian3(
  4303414.154026048,
  552161.235598733,
  4660771.704035539
);
cesiumViewer.camera.setView({
  destination: cp,
  orientation: {
    heading: 10,
    pitch: -Cesium.Math.PI_OVER_TWO * 0.5,
    roll: 0.0,
  },
});

/* Loading Potree viewer in the Potree Render Area defined in index.html */
window.viewer = new Potree.Viewer(
  document.getElementById("potree_render_area"),
  {
    useDefaultRenderLoop: false,
  }
);

/* Defining appearance settings for rendering in the viewer */
viewer.setEDLEnabled(true); // Enabling Eye-Dome-Lighting option
viewer.setFOV(60); // Defining Field of view
viewer.setPointBudget(3_000_000); // Defining point budget
viewer.setDescription(""); // Setting a description to be shown on top of viewer
viewer.setMinNodeSize(50);
viewer.loadSettingsFromURL();
viewer.setBackground(null);
viewer.useHQ = true;
/* Loading the settings for the Potree sidebar */
viewer.loadGUI(() => {
  viewer.setLanguage("en");
  // viewer.toggleSidebar();
  // $("#menu_appearance").next().show();
  // $("#menu_tools").next().show();
  /* Creating a new sidebar section for credits */
  let section = $(
    `<h3 id="menu_meta" class="accordion-header ui-widget"><span>Credits</span></h3><div class="accordion-content ui-widget pv-menu-list"></div>`
  );
  let content = section.last();
  content.html(`
    <div class="pv-menu-list">
        <li>INSERT TEXT HERE</li>
    </div>
    `);
  content.hide();
  section.first().click(() => content.slideToggle());
  section.insertBefore($("#menu_appearance"));
});
/* Loading point cloud data and its setting for rendering in Potree Viewer */
Potree.loadPointCloud("./pointclouds/metadata.json", "Bridge cloud", (e) => {
  let pointcloud = e.pointcloud;
  let material = pointcloud.material;
  /* Define scene for the bridge */
  let scene = viewer.scene;
  material.size = 0.6;
  material.pointSizeType = Potree.PointSizeType.ADAPTIVE;
  material.shape = Potree.PointShape.CIRCLE;
  material.activeAttributeName = "rgba"; // change this value to "classification" and uncomment the next 2 lines if you desire to show the classified point cloud
  // material.intensityRange = [1, 100];
  // material.gradient = Potree.Gradients.RAINBOW;
  scene.addPointCloud(pointcloud);
  let pointcloudProjection =
    "+proj=utm +zone=32 +datum=WGS84 +units=m +no_defs +type=crs";
  let mapProjection = proj4.defs("WGS84");
  window.toMap = proj4(pointcloudProjection, mapProjection);
  window.toScene = proj4(mapProjection, pointcloudProjection);
  {
    let bb = viewer.getBoundingBox();

    let minWGS84 = proj4(pointcloudProjection, mapProjection, bb.min.toArray());
    let maxWGS84 = proj4(pointcloudProjection, mapProjection, bb.max.toArray());
  }

  viewer.setFrontView();
});

function loop(timestamp) {
  requestAnimationFrame(loop);

  viewer.update(viewer.clock.getDelta(), timestamp);

  viewer.render();

  if (window.toMap !== undefined) {
    {
      let camera = viewer.scene.getActiveCamera();

      let pPos = new THREE.Vector3(0, 0, 0).applyMatrix4(camera.matrixWorld);
      let pRight = new THREE.Vector3(600, 0, 0).applyMatrix4(
        camera.matrixWorld
      );
      let pUp = new THREE.Vector3(0, 600, 0).applyMatrix4(camera.matrixWorld);
      let pTarget = viewer.scene.view.getPivot();

      let toCes = (pos) => {
        let xy = [pos.x, pos.y];
        let height = pos.z;
        let deg = toMap.forward(xy);
        let cPos = Cesium.Cartesian3.fromDegrees(...deg, height);

        return cPos;
      };

      let cPos = toCes(pPos);
      let cUpTarget = toCes(pUp);
      let cTarget = toCes(pTarget);

      let cDir = Cesium.Cartesian3.subtract(
        cTarget,
        cPos,
        new Cesium.Cartesian3()
      );
      let cUp = Cesium.Cartesian3.subtract(
        cUpTarget,
        cPos,
        new Cesium.Cartesian3()
      );

      cDir = Cesium.Cartesian3.normalize(cDir, new Cesium.Cartesian3());
      cUp = Cesium.Cartesian3.normalize(cUp, new Cesium.Cartesian3());

      cesiumViewer.camera.setView({
        destination: cPos,
        orientation: {
          direction: cDir,
          up: cUp,
        },
      });
    }

    let aspect = viewer.scene.getActiveCamera().aspect;
    if (aspect < 1) {
      let fovy = Math.PI * (viewer.scene.getActiveCamera().fov / 180);
      cesiumViewer.camera.frustum.fov = fovy;
    } else {
      let fovy = Math.PI * (viewer.scene.getActiveCamera().fov / 180);
      let fovx = Math.atan(Math.tan(0.5 * fovy) * aspect) * 2;
      cesiumViewer.camera.frustum.fov = fovx;
    }
  }

  cesiumViewer.render();
}

requestAnimationFrame(loop);
