<?php
    require_once 'service.php';

    $watch_file = file_get_contents("watch.json");
    $watch_list = json_decode($watch_file, true);

    $services = array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>SSP - simple status page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1>SSP - simple status page</h1>

    <div class="list-group">
    <?php
        foreach ($watch_list as $service_name => $service) {
            $temp_service = new Service();
            $temp_service->set_name($service['name']);
            $temp_service->set_type($service['type']);
            $temp_service->set_hostname($service['hostname']);

            //Change ping text to the correct one (like http)
            if ($temp_service->get_type() === "http") {
                $test_method_text = "HTTP code: ";
            } else if ($temp_service->get_type() === "https") {
                $test_method_text = "HTTPS code: ";
            } else {
                $test_method_text = $temp_service->get_type() . ": ";
            }

            if ($temp_service->get_status() > 0) {
            ?>
                <a href="#" class="list-group-item d-flex justify-content-between align-items-center <?php if (($temp_service->get_status() > 100 && $temp_service->get_type() === "ping") || (($temp_service->get_type() === "http" || $temp_service->get_type() === "https") && $temp_service->get_status() != 200)) echo "list-group-item-warning"; else echo "list-group-item-success"; ?>">
                    <?php echo $temp_service->get_name(); ?>
                    <span class="badge badge-primary badge-pill"><?php echo $test_method_text . $temp_service->get_status() ?></span>
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
</div>
