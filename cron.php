<?php
    require_once 'config.php';
    require_once 'service.php';

    require_once 'vendor/autoload.php';

    use Medoo\Medoo;

    $database = new Medoo($database_config);

    //TODO: setup.php for database table creation and so on...

    $watch_file = file_get_contents("watch.json");
    $watch_list = json_decode($watch_file, true);

    foreach ($watch_list as $service_name => $service) {
        $temp_service = new Service();
        $temp_service->set_name($service['name']);
        $temp_service->set_type($service['type']);
        $temp_service->set_hostname($service['hostname']);

        $database->insert("status", [
            "name" => $temp_service->get_name(),
            "hostname" => $temp_service->get_hostname(),
            "type" => $temp_service->get_type(),
            "status" => $temp_service->get_status()
        ]);
    }