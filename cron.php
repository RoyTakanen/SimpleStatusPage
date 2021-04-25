<?php
    require_once 'config.php';
    require_once 'service.php';

    require_once 'vendor/autoload.php';

    use Medoo\Medoo;

    if (getenv("IS_CRON") == 1) {
    
        $database = new Medoo($database_config);
        
        $watch_file = file_get_contents(dirname(__FILE__) . "/watch.json");
        $watch_list = json_decode($watch_file, true);
    
        foreach ($watch_list as $service_name => $service) {
            $temp_service = new Service();
            $temp_service->set_name($service['name']);
            $temp_service->set_type($service['type']);
            $temp_service->set_hostname($service['hostname']);

            if ($email && $service['email']) {
                $last_status = $temp_service->get_last_status()[0]["status"];
                $current_status = $temp_service->get_status();
                if ($temp_service->get_type() === "http" || $temp_service->get_type() === "https") {
                    if ($last_status != $current_status) {
                        $msg = "Hello,\n\nThe service " . $temp_service->get_name() . " " . $temp_service->get_type() . " status code has been changed from " . $last_status . " to " . $current_status . ".\n\nSSP - SimpleStatusPage";

                        mail($email,"Service " . $temp_service->get_name() . " status has changed", $msg);
                    }
                } else if ($temp_service->get_type() === "ping") {
                    if ($last_status != $current_status && $current_status == -1) {
                        $msg = "Hello,\n\nThe service " . $temp_service->get_name() . " has stopped responding to a ping.\n\nSSP - SimpleStatusPage";

                        mail($email,"Service " . $temp_service->get_name() . " status has changed", $msg);
                    }
                }
            }

            $database->insert("status", [
                "name" => $temp_service->get_name(),
                "hostname" => $temp_service->get_hostname(),
                "type" => $temp_service->get_type(),
                "status" => $temp_service->get_status()
            ]);
        }
    }
