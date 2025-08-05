var map,
  featureList,
  municipalitiesSearch = [],
  bridgeSearch = [];

$(window).resize(function () {
  sizeLayerControl();
});

$(document).on("click", ".feature-row", function (e) {
  $(document).off("mouseout", ".feature-row", clearHighlight);
  sidebarClick(parseInt($(this).attr("id"), 10));
});

if (!("ontouchstart" in window)) {
  $(document).on("mouseover", ".feature-row", function (e) {
    highlight
      .clearLayers()
      .addLayer(
        L.circleMarker(
          [$(this).attr("lat"), $(this).attr("lng")],
          highlightStyle
        )
      );
  });
}

// $(document).on("mouseout", ".feature-row", clearHighlight);

// Modified //
$(document).on("click", ".feature-row", function (e) {
  $(document).off("mouseout", ".feature-row", clearHighlight);
  sidebarClick(parseInt($(this).attr("id"), 10));
});

if (!("ontouchstart" in window)) {
  $(document).on("mouseover", ".feature-row", function (e) {
    highlight
      .clearLayers()
      .addLayer(
        L.circleMarker(
          [$(this).attr("lat"), $(this).attr("lng")],
          highlightStyle
        )
      );
  });
}

$(document).on("mouseout", ".feature-row", clearHighlight);

$("#about-btn").click(function () {
  $("#aboutModal").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

$("#full-extent-btn").click(function () {
  map.fitBounds(municipalities.getBounds());
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

$("#legend-btn").click(function () {
  $("#legendModal").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

$("#login-btn").click(function () {
  $("#loginModal").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

$("#list-btn").click(function () {
  animateSidebar();
  return false;
});

$("#nav-btn").click(function () {
  $(".navbar-collapse").collapse("toggle");
  return false;
});

$("#sidebar-toggle-btn").click(function () {
  animateSidebar();
  return false;
});

$("#sidebar-hide-btn").click(function () {
  animateSidebar();
  return false;
});

function animateSidebar() {
  $("#sidebar").animate(
    {
      width: "toggle",
    },
    350,
    function () {
      map.invalidateSize();
    }
  );
}

function sizeLayerControl() {
  $(".leaflet-control-layers").css("max-height", $("#map").height() - 50);
}

function clearHighlight() {
  highlight.clearLayers();
}

function sidebarClick(id) {
  var layer = markerClusters.getLayer(id);
  map.setView([layer.getLatLng().lat, layer.getLatLng().lng], 17);
  layer.fire("click");
  /* Hide sidebar and go to the map on small screens */
  if (document.body.clientWidth <= 767) {
    $("#sidebar").hide();
    map.invalidateSize();
  }
}

function syncSidebar() {
  /* Empty sidebar features */
  $("#feature-list tbody").empty();
  /* Loop through bridges layer and add only features which are in the map bounds */
  bridges.eachLayer(function (layer) {
    if (map.hasLayer(bridgeLayer)) {
      if (map.getBounds().contains(layer.getLatLng())) {
        $("#feature-list tbody").append(
          '<tr class="feature-row" id="' +
            L.stamp(layer) +
            '" lat="' +
            layer.getLatLng().lat +
            '" lng="' +
            layer.getLatLng().lng +
            '"><td style="vertical-align: middle;"><img width="16" height="18" src="assets/img/bridges.png"></td><td class="feature-name">' +
            layer.feature.properties.name +
            '</td><td style="vertical-align: middle;"><i class="fa fa-chevron-right pull-right"></i></td></tr>'
        );
      }
    }
  });
  /* Update list.js featureList */
  featureList = new List("features", {
    valueNames: ["feature-name"],
  });
  featureList.sort("feature-name", {
    order: "asc",
  });
}

/* Basemap Layers */
var cartoLight = L.tileLayer(
  "https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png",
  {
    maxZoom: 19,
    attribution:
      '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, &copy; <a href="https://cartodb.com/attributions">CartoDB</a>',
  }
);
var usgsImagery = L.layerGroup([
  L.tileLayer(
    "http://basemap.nationalmap.gov/arcgis/rest/services/USGSImageryOnly/MapServer/tile/{z}/{y}/{x}",
    {
      maxZoom: 15,
    }
  ),
  L.tileLayer.wms(
    "http://raster.nationalmap.gov/arcgis/services/Orthoimagery/USGS_EROS_Ortho_SCALE/ImageServer/WMSServer?",
    {
      minZoom: 16,
      maxZoom: 19,
      layers: "0",
      format: "image/jpeg",
      transparent: true,
      attribution: "Aerial Imagery courtesy USGS",
    }
  ),
]);

/* Overlay Layers */
var highlight = L.geoJson(null);
var highlightStyle = {
  stroke: false,
  fillColor: "#00FFFF",
  fillOpacity: 0.7,
  radius: 10,
};

var municipalities = L.geoJson(null, {
  style: function (feature) {
    return {
      color: "black",
      fill: false,
      opacity: 1,
      weight: 0.3,
      clickable: false,
    };
  },
  onEachFeature: function (feature, layer) {
    municipalitiesSearch.push({
      name: layer.feature.properties.BoroName,
      source: "municipalities",
      id: L.stamp(layer),
      bounds: layer.getBounds(),
    });
  },
});
$.getJSON("data/municipalities.geojson", function (data) {
  municipalities.addData(data);
});

/* Single marker cluster layer to hold all clusters */
var markerClusters = new L.MarkerClusterGroup({
  spiderfyOnMaxZoom: true,
  showCoverageOnHover: false,
  zoomToBoundsOnClick: true,
  disableClusteringAtZoom: 16,
});

/* Empty layer placeholder to add to layer control for listening when to add/remove bridges to markerClusters layer */
var bridgeLayer = L.geoJson(null);
var bridges = L.geoJson(null, {
  pointToLayer: function (feature, latlng) {
    return L.marker(latlng, {
      icon: L.icon({
        iconUrl: "assets/img/bridges.png",
        iconSize: [24, 28],
        iconAnchor: [12, 28],
        popupAnchor: [0, -25],
      }),
      title: feature.properties.name,
      riseOnHover: true,
    });
  },
  onEachFeature: function (feature, layer) {
    if (feature.properties) {
      var content =
        "<table class='table table-striped table-bordered table-condensed'>" +
        "<tr><th>Name</th><td>" +
        feature.properties.name +
        "</td></tr>" +
        "<tr><th>Road code</th><td>" +
        feature.properties.road_code +
        "</td></tr>" +
        "<tr><th>Owner</th><td>" +
        feature.properties.owner +
        "</td></tr>" +
        "<tr><th>Technical report</th><td><button onclick='generatePDF(" +
        JSON.stringify(feature.properties) +
        ")'>Generate PDF</button></td></tr>" +
        "<tr><th>Inspections history</th><td><a id='inspections-history-" +
        feature.properties.id +
        "' class='url-break' href='#' data-id='" +
        feature.properties.id +
        "'>View Inspections</a></td></tr>" +
        "<table>";
      layer.on({
        click: function (e) {
          $("#feature-title").html(feature.properties.name);
          $("#feature-info").html(content);
          $("#featureModal").modal("show");
          highlight
            .clearLayers()
            .addLayer(
              L.circleMarker(
                [
                  feature.geometry.coordinates[1],
                  feature.geometry.coordinates[0],
                ],
                highlightStyle
              )
            );
        },
      });
      $("#feature-list tbody").append(
        '<tr class="feature-row" id="' +
          L.stamp(layer) +
          '" lat="' +
          layer.getLatLng().lat +
          '" lng="' +
          layer.getLatLng().lng +
          '"><td style="vertical-align: middle;"><img width="16" height="18" src="assets/img/bridges.png"></td><td class="feature-name">' +
          layer.feature.properties.name +
          '</td><td style="vertical-align: middle;"><i class="fa fa-chevron-right pull-right"></i></td></tr>'
      );
      bridgeSearch.push({
        name: layer.feature.properties.name,
        owner: layer.feature.properties.owner,
        source: "Bridges",
        id: L.stamp(layer),
        lat: layer.feature.geometry.coordinates[1],
        lng: layer.feature.geometry.coordinates[0],
      });

      // Add event listener for the "Inspections history" link
      document.addEventListener("click", function (e) {
        if (
          e.target &&
          e.target.id === "inspections-history-" + feature.properties.id
        ) {
          var bridgeId = e.target.getAttribute("data-id");
          window.open(
            "assets/php/inspections.php?fkStructure=" + bridgeId,
            "_blank"
          );
          console.log("You clicked the inspection history button!");
        }
      });
    }
  },
});

$.getJSON("assets/php/get_bridges.php", function (data) {
  bridges.addData(data); // Ensure bridgeLayer gets the data
  //bridgeLayer.addData(data);
  markerClusters.addLayer(bridges); // Add the bridges to the markerClusters layer
  //map.addLayer(bridges); // Add the bridges layer to the map
});

map = L.map("map", {
  zoom: 10,
  center: [40.702222, -73.979378],
  layers: [cartoLight, municipalities, markerClusters, highlight],
  zoomControl: false,
  attributionControl: false,
});

/* Layer control listeners that allow for a single markerClusters layer */
map.on("overlayadd", function (e) {
  if (e.layer === bridgeLayer) {
    markerClusters.addLayer(bridges);
    syncSidebar();
  }
});

map.on("overlayremove", function (e) {
  if (e.layer === bridgeLayer) {
    markerClusters.removeLayer(bridges);
    syncSidebar();
  }
});

/* Filter sidebar feature list to only show features in current map bounds */
map.on("moveend", function (e) {
  syncSidebar();
});

/* Clear feature highlight when map is clicked */
map.on("click", function (e) {
  highlight.clearLayers();
});

/* Attribution control */
function updateAttribution(e) {
  $.each(map._layers, function (index, layer) {
    if (layer.getAttribution) {
      $("#attribution").html(layer.getAttribution());
    }
  });
}
map.on("layeradd", updateAttribution);
map.on("layerremove", updateAttribution);

var attributionControl = L.control({
  position: "bottomright",
});
attributionControl.onAdd = function (map) {
  var div = L.DomUtil.create("div", "leaflet-control-attribution");
  div.innerHTML =
    "<span class='hidden-xs'>Developed by <a href='tars4815.github.io'>tars4815</a> | </span><a href='#' onclick='$(\"#attributionModal\").modal(\"show\"); return false;'>Attribution</a>";
  return div;
};
map.addControl(attributionControl);

var zoomControl = L.control
  .zoom({
    position: "bottomright",
  })
  .addTo(map);

/* GPS enabled geolocation control set to follow the user's location */
var locateControl = L.control
  .locate({
    position: "bottomright",
    drawCircle: true,
    follow: true,
    setView: true,
    keepCurrentZoomLevel: true,
    markerStyle: {
      weight: 1,
      opacity: 0.8,
      fillOpacity: 0.8,
    },
    circleStyle: {
      weight: 1,
      clickable: false,
    },
    icon: "fa fa-location-arrow",
    metric: false,
    strings: {
      title: "My location",
      popup: "You are within {distance} {unit} from this point",
      outsideMapBoundsMsg: "You seem located outside the boundaries of the map",
    },
    locateOptions: {
      maxZoom: 18,
      watch: true,
      enableHighAccuracy: true,
      maximumAge: 10000,
      timeout: 10000,
    },
  })
  .addTo(map);

/* Larger screens get expanded layer control and visible sidebar */
if (document.body.clientWidth <= 767) {
  var isCollapsed = true;
} else {
  var isCollapsed = false;
}

var baseLayers = {
  "Street Map": cartoLight,
  "Aerial Imagery": usgsImagery,
};

var groupedOverlays = {
  "Points of Interest": {
    "<img src='assets/img/bridges.png' width='24' height='28'>&nbsp;Bridges":
      bridgeLayer,
  },
  Reference: {
    Municipalities: municipalities,
  },
};

var layerControl = L.control
  .groupedLayers(baseLayers, groupedOverlays, {
    collapsed: isCollapsed,
  })
  .addTo(map);

/* Highlight search box text on click */
$("#searchbox").click(function () {
  $(this).select();
});

/* Prevent hitting enter from refreshing the page */
$("#searchbox").keypress(function (e) {
  if (e.which == 13) {
    e.preventDefault();
  }
});

$("#featureModal").on("hidden.bs.modal", function (e) {
  $(document).on("mouseout", ".feature-row", clearHighlight);
});

/* Typeahead search functionality */
$(document).one("ajaxStop", function () {
  $("#loading").hide();
  sizeLayerControl();
  /* Fit map to municipalities bounds */
  map.fitBounds(municipalities.getBounds());
  featureList = new List("features", { valueNames: ["feature-name"] });
  featureList.sort("feature-name", { order: "asc" });

  var boroughsBH = new Bloodhound({
    name: "municipalities",
    datumTokenizer: function (d) {
      return Bloodhound.tokenizers.whitespace(d.name);
    },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    local: municipalitiesSearch,
    limit: 10,
  });

  var bridgesBH = new Bloodhound({
    name: "Bridges",
    datumTokenizer: function (d) {
      return Bloodhound.tokenizers.whitespace(d.name);
    },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    local: bridgeSearch,
    limit: 10,
  });

  var geonamesBH = new Bloodhound({
    name: "GeoNames",
    datumTokenizer: function (d) {
      return Bloodhound.tokenizers.whitespace(d.name);
    },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: "http://api.geonames.org/searchJSON?username=bootleaf&featureClass=P&maxRows=5&countryCode=US&name_startsWith=%QUERY",
      filter: function (data) {
        return $.map(data.geonames, function (result) {
          return {
            name: result.name + ", " + result.adminCode1,
            lat: result.lat,
            lng: result.lng,
            source: "GeoNames",
          };
        });
      },
      ajax: {
        beforeSend: function (jqXhr, settings) {
          settings.url +=
            "&east=" +
            map.getBounds().getEast() +
            "&west=" +
            map.getBounds().getWest() +
            "&north=" +
            map.getBounds().getNorth() +
            "&south=" +
            map.getBounds().getSouth();
          $("#searchicon")
            .removeClass("fa-search")
            .addClass("fa-refresh fa-spin");
        },
        complete: function (jqXHR, status) {
          $("#searchicon")
            .removeClass("fa-refresh fa-spin")
            .addClass("fa-search");
        },
      },
    },
    limit: 10,
  });
  boroughsBH.initialize();
  bridgesBH.initialize();
  geonamesBH.initialize();

  /* instantiate the typeahead UI */
  $("#searchbox")
    .typeahead(
      {
        minLength: 3,
        highlight: true,
        hint: false,
      },
      {
        name: "Municipalities",
        displayKey: "name",
        source: boroughsBH.ttAdapter(),
        templates: {
          header: "<h4 class='typeahead-header'>Municipalities</h4>",
        },
      },
      {
        name: "Bridges",
        displayKey: "name",
        source: bridgesBH.ttAdapter(),
        templates: {
          header:
            "<h4 class='typeahead-header'><img src='assets/img/bridges.png' width='24' height='28'>&nbsp;Bridges</h4>",
          suggestion: Handlebars.compile(
            ["{{name}}<br>&nbsp;<small>{{address}}</small>"].join("")
          ),
        },
      },
      {
        name: "GeoNames",
        displayKey: "name",
        source: geonamesBH.ttAdapter(),
        templates: {
          header:
            "<h4 class='typeahead-header'><img src='assets/img/globe.png' width='25' height='25'>&nbsp;GeoNames</h4>",
        },
      }
    )
    .on("typeahead:selected", function (obj, datum) {
      if (datum.source === "Municipalities") {
        map.fitBounds(datum.bounds);
      }
      if (datum.source === "Bridges") {
        if (!map.hasLayer(bridgeLayer)) {
          map.addLayer(bridgeLayer);
        }
        map.setView([datum.lat, datum.lng], 17);
        if (map._layers[datum.id]) {
          map._layers[datum.id].fire("click");
        }
      }
      if (datum.source === "GeoNames") {
        map.setView([datum.lat, datum.lng], 14);
      }
      if ($(".navbar-collapse").height() > 50) {
        $(".navbar-collapse").collapse("hide");
      }
    })
    .on("typeahead:opened", function () {
      $(".navbar-collapse.in").css(
        "max-height",
        $(document).height() - $(".navbar-header").height()
      );
      $(".navbar-collapse.in").css(
        "height",
        $(document).height() - $(".navbar-header").height()
      );
    })
    .on("typeahead:closed", function () {
      $(".navbar-collapse.in").css("max-height", "");
      $(".navbar-collapse.in").css("height", "");
    });
  $(".twitter-typeahead").css("position", "static");
  $(".twitter-typeahead").css("display", "block");
});

// Leaflet patch to make layer control scrollable on touch browsers
var container = $(".leaflet-control-layers")[0];
if (!L.Browser.touch) {
  L.DomEvent.disableClickPropagation(container).disableScrollPropagation(
    container
  );
} else {
  L.DomEvent.disableClickPropagation(container);
}

function generatePDF(feature) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  doc.setFontSize(16);
  doc.setFont("helvetica", "bold");
  doc.text(
    "Report for structure with code: " + feature.name + ", " + feature.municipality,
    10,
    10
  );

  doc.setFontSize(11);
  doc.setFont("helvetica", "normal");
  const longText = "Example of an automatically generated PDF report.\nThis document summarise the main information associated to the selected structure.";
  const splitText = doc.splitTextToSize(longText, 180);
  doc.text(splitText, 10, 20);

  const imgX = 10;
  const imgY = 30; // Start below the text
  const imgWidth = 60;
  const imgHeight = 40; // Set height proportionally or fixed as needed

  const spacingAfterImage = 10; // Space between image and table

  const img = new Image();
  img.crossOrigin = "Anonymous";

  img.onload = function () {
    doc.addImage(img, "JPEG", imgX, imgY, imgWidth, imgHeight);

    const tableStartY = imgY + imgHeight + spacingAfterImage;

    doc.autoTable({
      head: [["Attribute", "Value"]],
      body: [
        ["Official name", feature.name || "N/A"],
        ["Unofficial name", feature.name_unoff || "N/A"],
        ["Road code", feature.road_code || "N/A"],
        ["Location", feature.municipality || "N/A"],
        ["Owner", feature.owner || "N/A"],
        ["Length (m)", feature.length || "N/A"],
        ["Spans", feature.spans || "N/A"]
      ],
      startY: tableStartY,
      theme: "grid",
      styles: { fontSize: 11 },
      columnStyles: {
        0: { fontStyle: "bold" },
        1: { fontStyle: "normal" }
      },
      headStyles: { fillColor: [0, 0, 0] }
    });

    doc.save(`report_${feature.name || "structure"}.pdf`);
  };

  img.onerror = function () {
    console.error("Image could not be loaded:", feature.image_preview);
    alert("Image preview could not be loaded. Generating PDF without image.");

    doc.autoTable({
      head: [["Attribute", "Value"]],
      body: [
        ["Official name", feature.name || "N/A"],
        ["Unofficial name", feature.name_unoff || "N/A"],
        ["Road code", feature.road_code || "N/A"],
        ["Location", feature.municipality || "N/A"],
        ["Owner", feature.owner || "N/A"],
        ["Length (m)", feature.length || "N/A"],
        ["Spans", feature.spans || "N/A"]
      ],
      startY: 30, // Or adjust as needed after your initial text
      theme: "grid",
      styles: { fontSize: 11 },
      columnStyles: {
        0: { fontStyle: "bold" },
        1: { fontStyle: "normal" }
      },
      headStyles: { fillColor: [0, 0, 0] }
    });

    doc.save(`report_${feature.name || "structure"}.pdf`);
  };

  img.src = feature.image_preview;
}
