/**
 * Loads sensors from the database and adds them to the Potree scene.
 */
function loadSensors(structureId) {
  $.ajax({
    type: "GET",
    url: "database/load_sensors.php",
    data: { structureId: structureId },
    dataType: "json",
    success: function (sensors) {
      const listContainer = $("#sensor-list");
      listContainer.empty();

      sensors.forEach((sensor) => {
        const pos = [sensor.x, sensor.y, sensor.z];

        // 1. Create the 3D Annotation
        createSensorAnnotation(
          sensor.id,
          viewer.scene,
          `SAR point ${sensor.id}`,
          pos,
          "sensor",
        );

        // 2. Add to the Sidebar List
        const listItem =
          $(`<li style="cursor:pointer; padding: 5px; border-bottom: 1px solid #eee;">
                    <i class="fa fa-crosshairs"></i> Sensor #${sensor.id}
                </li>`);

        listItem.click(() => flyToSensor(pos));
        listContainer.append(listItem);
      });
    },
    error: function (error) {
      console.error("Error loading sensors:", error);
    },
  });
}

function flyToSensor(position) {
  // Move the camera to the sensor position
  // We offset the camera slightly so we aren't inside the point
  const cameraPos = [position[0] + 5, position[1] + 5, position[2] + 5];

  viewer.scene.view.setView(
    cameraPos, // Camera position
    position, // Target (the sensor)
    2000, // Animation duration in milliseconds
  );
}

function createSensorAnnotation(id, scene, titleText, position) {
  // Define where the camera should go (slightly offset) and look (at the sensor)
  const cameraPosition = [position[0] + 5, position[1] + 5, position[2] + 5];
  const cameraTarget = position;

  let titleElement = $(
    `<span><i class="fa fa-microchip"></i> ${titleText}</span>`,
  );

  let annotation = new Potree.Annotation({
    position: position,
    title: titleElement,
    cameraPosition: cameraPosition, // Required to avoid the 'x' of undefined error
    cameraTarget: cameraTarget, // Required to avoid the 'x' of undefined error
  });

  // Handle the chart logic on click
  annotation.addEventListener("click", () => {
    fetchObservationData(id);
  });

  // Custom Styling (Optional but recommended for consistency)
  annotation.title.css({
    "background-color": "gold",
    color: "black",
    padding: "5px",
    "border-radius": "5px",
    cursor: "pointer",
  });

  annotation.customId = id;
  scene.annotations.add(annotation);
}

// Function to close the panel
$(document).ready(function () {
  $("#closeChartBtn").click(() => {
    $("#chart-panel").hide();
  });
});
// Variable to store the current chart instance globally in this script
let myChart = null;

function fetchObservationData(sensorId) {
  const panel = document.getElementById("chart-panel");
  const content = document.getElementById("chart-content");

  panel.style.display = "flex";

  // 1. Clear the container and show a loader
  content.innerHTML =
    '<div style="color:white; padding:20px;">Loading data...</div>';

  fetch(`database/fetch_observations.php?sensorId=${sensorId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data && data.length > 0) {
        // 2. Clear the "Loading..." text
        content.innerHTML = "";

        // 3. Generate the graph
        generateEChartsGraph(data, content, sensorId);
      } else {
        content.innerHTML = `<div style="color:white; padding:20px;">No data found for Sensor #${sensorId}</div>`;
      }
    })
    .catch((err) => {
      console.error("Error:", err);
      content.innerHTML =
        '<div style="color:red; padding:20px;">Error loading data.</div>';
    });
}

function generateEChartsGraph(data, container, sensorId) {
  // 4. Dispose of the existing chart instance if it exists
  if (myChart != null) {
    myChart.dispose();
  }

  // 5. Initialize a fresh instance
  myChart = echarts.init(container);

  // 6. Calculate the uncertainty range for the value series
  const dates = data.map((d) => d.date);
  const mainValues = data.map((d) => d.value);
  const upperBounds = data.map((d) => d.value + d.uncertainty);
  const lowerBounds = data.map((d) => d.value - d.uncertainty);

  const option = {
    title: {
      text: "Deformation Over Time - Point " + sensorId,
      textStyle: { color: "#fff" },
      left: "center",
    },
    tooltip: {
      trigger: "axis",
      formatter: function (params) {
        let res = `<b>Date: ${params[0].name}</b><br/>`;
        params.forEach((item) => {
          if (item.seriesName === "Deformation") {
            res += `${item.marker} ${item.seriesName}: ${item.value.toFixed(3)} mm<br/>`;
          }
        });
        // Find data index to show uncertainty in tooltip
        const dataIndex = params[0].dataIndex;
        res += `± Uncertainty: ${data[dataIndex].uncertainty.toFixed(3)} mm`;
        return res;
      },
    },
    legend: {
      data: ["Deformation", "Uncertainty Range"],
      bottom: 0,
      textStyle: { color: "#fff" },
    },
    grid: {
      bottom: "20%", // Give space for slanted labels
      left: "15%",
    },
    xAxis: {
      type: "category",
      data: dates,
      axisLabel: {
        color: "#fff",
        rotate: 45, // Slant dates to prevent overlapping
      },
    },
    yAxis: {
      type: "value",
      name: "Deformation (mm)",
      axisLabel: { color: "#fff" },
      nameTextStyle: { color: "#fff" },
      splitLine: { lineStyle: { color: "#444" } },
    },
    series: [
      {
        data: mainValues,
        name: "Deformation",
        type: "line",
        smooth: true,
        symbol: "circle",
        symbolSize: 8,
        itemStyle: { color: "gold" },
        lineStyle: { width: 2 },
        zIndex: 10,
      },
      {
        data: upperBounds,
        name: "Upper bound",
        type: "line",
        smooth: true,
        symbol: "none",
        lineStyle: { opacity: 1, width: 1.5, type: "dashed", color: "#ffdd78" },
      },
      {
        name: "Lower Bound",
        data: lowerBounds,
        type: "line",
        smooth: true,
        symbol: "none",
        lineStyle: { opacity: 1, width: 1.5, type: "dashed", color: "#ffdd78" },
      },
    ],
  };

  myChart.setOption(option);

  // 6. Ensure the chart resizes if the window changes
  window.addEventListener("resize", () => {
    if (myChart) myChart.resize();
  });
}
