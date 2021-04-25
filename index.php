<?php
    require_once 'service.php';

    $watch_file = file_get_contents("watch.json");
    $watch_list = json_decode($watch_file, true);

    $services = array();

    //Currently only ping graph is being supported.
    //There might be some sort of status bar for http and
    //https. We also might add better color coding for them

    if ($_GET["api"] === "json" && $_GET["data"] === "ping" && $_GET["service"]) {
        foreach ($watch_list as $service_name => $service) {
            $temp_service = new Service();
            $temp_service->set_name($service['name']);
            $temp_service->set_type($service['type']);
            $temp_service->set_hostname($service['hostname']);

            if ($_GET["service"] === $temp_service->get_name()) {
                $status_data = $temp_service->get_last_status_data();
            }
        }

        if (!$status_data) {
            header("HTTP/1.1 404 Not Found");
            $status_data = new stdClass();
            $status_data->error = "404";
        }

        header('Content-Type: application/json');
        echo json_encode($status_data);
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>SSP - SimpleStatusPage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.26.1/apexcharts.min.css" integrity="sha512-cXlEk9KIclmKZMbBGA+wGShlISDUphT+/wyEIWT8eufvRJNC4xoLjlgbz83H+46jgqM2b3tEYCK/hoV7AkCJxA==" crossorigin="anonymous" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.26.1/apexcharts.min.js" integrity="sha512-F1EofoLU9YY/iBX1R3yED4wbi6rcON36n/qZD+YGMWQklYvcj1pYaxFmaWQCRqgtZBoc6pG4s3GiW6e+Jeqtdg==" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <h1>SSP - SimpleStatusPage</h1>

    <div class="list-group">
    <?php
        foreach ($watch_list as $service_name => $service) {
            $temp_service = new Service();
            $temp_service->set_name($service['name']);
            $temp_service->set_type($service['type']);
            $temp_service->set_hostname($service['hostname']);
            $temp_service->set_graph($service['graph']);

            if ($temp_service->get_type() === "http") {
                $test_method_text = "HTTP code: ";
            } else if ($temp_service->get_type() === "https") {
                $test_method_text = "HTTPS code: ";
            } else {
                $test_method_text = $temp_service->get_type() . ": ";
            }
            
            if ($temp_service->get_last_status() > 0) {
            ?>
                <a href="#" class="list-group-item d-flex justify-content-between align-items-center <?php if (($temp_service->get_last_status() > 100 && $temp_service->get_type() === "ping") || (($temp_service->get_type() === "http" || $temp_service->get_type() === "https") && $temp_service->get_last_status() != 200)) echo "list-group-item-warning"; else echo "list-group-item-success"; ?>">
                    <?php echo $temp_service->get_name(); ?>
                    <span class="badge badge-primary badge-pill"><?php echo $test_method_text . $temp_service->get_last_status(); ?></span>
                </a>
            <?php
            } else {
            ?>
                <a href="#" class="list-group-item d-flex justify-content-between align-items-center list-group-item-danger">
                    <?php echo $temp_service->get_name(); ?>
                    <span class="badge badge-primary badge-pill">Ping: -1<!-- Service down--></span>
                </a>
            <?php
            }

            array_push($services, $temp_service);
        }

        //print_r($services);
    ?>
    </div>
    <hr>

    <?php 
    foreach ($services as $service) {
        if ($service->get_type() === "ping" && $service->get_graph()) {
            ?>
            <h2><?php echo $service->get_name(); ?></h2>
    
            <div id="graph-<?php echo $service->get_name(); //Change to the key in watch.json ?>"></div>
            <?php
        }
    }
    ?>
    
    <script>
    function httpGet(theUrl) {
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.open("GET", theUrl, false); // false for synchronous request
        xmlHttp.send(null);
        return xmlHttp.responseText;
    }

    const graphElements = document.querySelectorAll('*[id^="graph-"]');

    graphElements.forEach(graphElement => {

        const service = graphElement.id.replace("graph-", "");

        const serviceData = JSON.parse(httpGet("/?api=json&data=ping&service=" + service));

        console.log(serviceData);

        const options = {
            series: [
                {
                    name: service + " ping",
                    data: serviceData.map(data => data.status)
                }
            ],
            chart: {
                height: 350,
                type: 'area',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: serviceData.map(data => data.time)
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        const chart = new ApexCharts(graphElement, options);
        chart.render();
    });

    </script>

    </div>
</body>