/* Loading Potree viewer in the Potree Render Area defined in index.html */
window.viewer = new Potree.Viewer(document.getElementById("potree_render_area"));
/* Defining appearance settings for rendering in the viewer */
viewer.setEDLEnabled(true); // Enabling Eye-Dome-Lighting option
viewer.setFOV(60); // Defining Field of view
viewer.setPointBudget(2_000_000); // Defining point budget
viewer.setDescription("Explore the oriented images of the model on a desktop browser."); // Setting a description to be shown on top of viewer
/* Loading the settings for the Potree sidebar */
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
/* Define scene for the bridge */
let bridgescene = new Potree.Scene();
/* Set scene to be loaded in the Potree Viewer */
viewer.setScene(bridgescene);
/* Loading point cloud data and its setting for rendering in Potree Viewer */
Potree.loadPointCloud("./pointclouds/metadata.json", "Bridge cloud", e => {
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