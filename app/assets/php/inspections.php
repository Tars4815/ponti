<?php
header('Content-Type: text/html');

// inspection_history.php
// This script retrieves inspection records for a specific bridge and displays them in a table format.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Get the fkStructure from the query parameter
$fkStructure = isset($_GET['fkStructure']) ? intval($_GET['fkStructure']) : 0;

// SQL query to get the inspection records for the specific bridge
$sql = "
SELECT *
FROM inspections
WHERE fkstructure = :fkStructure";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':fkStructure', $fkStructure, PDO::PARAM_INT);
$stmt->execute();

$inspections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#000000">
    <meta name="description" content="PONTI platform">
    <meta name="author" content="Federica Gaspari">
    <title>PONTI | Inspection History</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/app.css">

    <link rel="apple-touch-icon" sizes="76x76" href="../img/favicon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../img/favicon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../img/favicon-152.png">
    <link rel="icon" sizes="196x196" href="../img/favicon-196.png">
    <link rel="icon" type="image/x-icon" href="../img/favicon.ico">
</head>

<body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <div class="navbar-icon-container">
                    <a href="#" class="navbar-icon pull-right visible-xs" id="nav-btn"><i
                            class="fa fa-bars fa-lg white"></i></a>
                    <a href="#" class="navbar-icon pull-right visible-xs" id="sidebar-toggle-btn"><i
                            class="fa fa-search fa-lg white"></i></a>
                </div>
                <a class="navbar-brand" href="#">P.O.N.T.I - Inspection History for Bridge ID:
                    <?php echo htmlspecialchars($fkStructure); ?></a>
            </div>
            <div class="navbar-collapse collapse">
                <form class="navbar-form navbar-right" role="search">
                    <div class="form-group has-feedback">
                        <input id="searchbox" type="text" placeholder="Search" class="form-control">
                        <span id="searchicon" class="fa fa-search form-control-feedback"></span>
                    </div>
                </form>
            </div><!--/.navbar-collapse -->
        </div>
    </div>
    <div id="container">
        <?php if ($inspections): ?>
            <table class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Inspector(s)</th>
                        <th>Comments</th>
                        <th>3D Viewer</th>
                        <th>Report</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inspections as $inspection): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inspection['date']); ?></td>
                            <td><?php echo htmlspecialchars($inspection['technician']); ?></td>
                            <td><?php echo htmlspecialchars($inspection['note']); ?></td>
                            <td><button class="viewer-button" data-id="<?php echo $inspection['id']; ?>">3D Data viewer</button>
                            <td><button class="report-inspection">Info</button></td>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div id="info-banner" class="alert alert-info"
                style="display:none; position:fixed; bottom:0; left:0; right:0; margin:0; border-radius:0; z-index:9999;">
                <strong>Inspection report:</strong>
                <ul id="product-list" style="margin:0;"></ul>
            </div>
        <?php else: ?>
            <p>No inspection records found for this bridge.</p>
        <?php endif; ?>
        <script>
            // Add event listeners to the buttons for 3D viewer and report inspection
            document.querySelectorAll('.viewer-button').forEach(button => {
                button.addEventListener('click', function () {
                    var inspectionId = this.getAttribute('data-id');
                    window.open('../../viewer/index.php?inspectionId=' + inspectionId, '_blank');
                    console.log("Opening the 3D viewer for virtual inspection...");
                });
            });
            document.querySelectorAll('.report-inspection').forEach((button, index) => {
                button.addEventListener('click', function () {
                    const inspectionId = <?php echo json_encode(array_column($inspections, 'id')); ?>[index];

                    fetch('get_inspection_info.php?inspectionId=' + inspectionId)
                        .then(response => response.json())
                        .then(data => {
                            const banner = document.getElementById('info-banner');
                            const list = document.getElementById('product-list');
                            list.innerHTML = '';

                            const summary = document.createElement('p');
                            //<li>${data.counts.oriented_images} <i>oriented images</i>,</li>
                            //Total: ${data.counts.defects} defects
                            summary.innerHTML = `
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
    
    <!-- Left Column: Products + Defects -->
    <div style="flex: 1 1 300px; min-width: 250px;">
      <strong>Products summary</strong>:<br> 
      <li>213 <a href=""><i><u>oriented image(s)</u></i></a></li>
      <li>${data.counts.pointclouds} <i>pointcloud(s)</i></li>
      <br><strong>Defects summary</strong>:<br>
      Total annotated defects: 25<br><br>
      <li><i>Spalling</i>: 3</li>
      <li><i>Cracking</i>: 5</li>
      <li><i>Corrosion</i>: 2</li>
      <li><i>Other types</i>: 15</li>
    </div>
    
    <!-- Right Column: Rating + Preview -->
    <div style="flex: 1 1 300px; min-width: 250px;">
      <strong>Overall bridge rating</strong>:<br>
      3 out of 5<br><br>
      <strong>3D viewer preview</strong>:<br>
      <img src="../img/viewer-preview.png" alt="3D Viewer Preview" style="width:100%; max-width:400px; height:auto;">
    </div>

  </div>
    `;
                            list.appendChild(summary);

                            banner.style.display = 'block';
                        })


                        .catch(error => {
                            console.error('Error fetching product info:', error);
                        });
                });
            });

        </script>
    </div>

</body>

</html>