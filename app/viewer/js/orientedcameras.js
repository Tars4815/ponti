async function loadOrientedImagesFromDB(
  inspectionId,
  cameraParamsPath,
  imageSetName,
  visibility = true
) {
  try {
    const response = await fetch(
      `./database/fetch_oriented_images.php?inspectionId=${inspectionId}`
    );
    if (!response.ok) throw new Error("Failed to load oriented image data");
    const imagesData = await response.json();
    
    // Build the TXT content with relative paths
    let txtContent = `# CoordinateSystem: PROJCS["WGS 84 / UTM zone 32N" GEOGCS["WGS 84" DATUM["World Geodetic System 1984 ensemble" SPHEROID["WGS 84" 6378137 298.257223563 AUTHORITY["EPSG" "7030"]] TOWGS84[0 0 0 0 0 0 0] AUTHORITY["EPSG" "6326"]] PRIMEM["Greenwich" 0 AUTHORITY["EPSG" "8901"]] UNIT["degree" 0.01745329251994328 AUTHORITY["EPSG" "9102"]] AUTHORITY["EPSG" "4326"]] PROJECTION["Transverse_Mercator" AUTHORITY["EPSG" "9807"]] PARAMETER["latitude_of_origin" 0] PARAMETER["central_meridian" 9] PARAMETER["scale_factor" 0.9996] PARAMETER["false_easting" 500000] PARAMETER["false_northing" 0] UNIT["metre" 1 AUTHORITY["EPSG" "9001"]] AUTHORITY["EPSG" "32632"]]\n`;
    txtContent += `#Label X/Easting Y/Northing Z/Altitude Omega Phi Kappa X_est Y_est Z_est Omega_est Phi_est Kappa_est\n`;
    
    const padSpaces = (count) => " ".repeat(count);
    
    imagesData.forEach((img) => {
      // Use relative path that will work when served from the server
      let fullPath = `../assets/oriented-images/${img.filename}`;
      let paddedFilename = fullPath.padEnd(50, " ");
      
      const line =
        `${paddedFilename}${padSpaces(7)}${img.x} ${img.y} ${img.z} ` +
        `${img.omega} ${img.phi} ${img.kappa} ` +
        `${img.x} ${img.y} ${img.z} ` +
        `${img.omega} ${img.phi} ${img.kappa}\n`;
   
      txtContent += line;
    });
    
    // Send the content to PHP to create a temporary file
    const saveResponse = await fetch('./database/save_temp_txt.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        content: txtContent,
        filename: `oriented_images_${inspectionId}.txt`
      })
    });
    
    const saveResult = await saveResponse.json();
    
    // Use the server-generated file path
    Potree.OrientedImageLoader.load(cameraParamsPath, saveResult.filepath, viewer).then(
      (images) => {
        images.visible = visibility;
        images.name = imageSetName;
        viewer.scene.addOrientedImages(images);
      }
    );
  } catch (error) {
    console.error("Error loading oriented images:", error);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const inspectionId = 8; // Replace with your dynamic ID
  const cameraParamsPath = "./img_selected/lugagnano.xml";
  const imageSetName = "drone-images";

  loadOrientedImagesFromDB(inspectionId, cameraParamsPath, imageSetName, true);
});
