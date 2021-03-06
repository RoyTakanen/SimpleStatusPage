<?php
    $config = json_decode(file_get_contents(dirname(__FILE__) . "/config.json"));
    require_once 'vendor/autoload.php';

    use Medoo\Medoo;

    $database = new Medoo((array) $config->database);

    class Service {
        private $name;
        private $type;
        private $hostname;
        private $graph;

        public function set_name($name) {
            $this->name = $name;
        }

        public function set_hostname($hostname) {
            $this->hostname = $hostname;
        }

        public function set_type($type) {
            $this->type = $type;
        }

        public function set_graph($graph) {
            $this->graph = $graph;
        }

        public function get_graph() {
            return $this->graph;
        }

        public function get_type() {
            return $this->type;
        }

        public function get_name() {
            return $this->name;
        }

        public function get_hostname() {
            return $this->hostname;
        }

        public function get_last_status_data($minutes_before_now=20) {
            global $database;

            $last_statuses = $database->select("status", [
                "time",
                "status"
            ], [
                "name[=]" => $this->name,
                "LIMIT" => $minutes_before_now,
                "ORDER" => ["time" => "DESC"]
            ]);
                
            if (count($last_statuses) > 0) {
                return $last_statuses;
            } else {
                return FALSE;
            }
        }

        public function get_last_status() {
            global $database;
            $last_status = $database->select("status", [
                "type",
                "time",
                "status"
            ], [
                "name[=]" => $this->name,
                "LIMIT" => 1,
                "ORDER" => ["time" => "DESC"]
            ]);
                
            if (count($last_status) > 0) {
                return $last_status[0]["status"];
            } else {
                return FALSE;
            }
        }

        private function http() {
            $url = "http://" . $this->hostname;
            $headers = get_headers($url);
            $status_code = substr($headers[0], 9, 3);

            return $status_code;
        }

        private function https() {
            $url = "https://" . $this->hostname;
            $headers = get_headers($url);
            $status_code = substr($headers[0], 9, 3);

            return $status_code;
        }

        /*
        If returns false something went wrong
        */
        private function ping() {
            $host = $this->hostname;
            $port = 80;

            $starttime = microtime(true);
            $file      = fsockopen ($host, $port, $errno, $errstr, 10);
            $stoptime  = microtime(true);
            $status    = 0;
        
            if (!$file) $status = -1;  // Site is down
            else {
                fclose($file);
                $status = ($stoptime - $starttime) * 1000;
                $status = floor($status);
            }
            return $status;
        }

        /*
        If returns false you have to check that the service has been configured
        */
        public function get_status() {
            if (!($this->name || $this->type || $this->hostname)) return false;
            if ($this->get_type() === "ping") {
                $this->last_status = $this->ping();
            } else if ($this->get_type() === "http") {
                $this->last_status = $this->http();
            } else if ($this->get_type() === "https") {
                $this->last_status = $this->https();
            }

            return $this->last_status;
        }
    }