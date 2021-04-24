<?php
    class Service {
        public $name;
        public $type;
        public $hostname;
        public $last_status;

        public function set_name($name) {
            $this->name = $name;
        }

        public function set_hostname($hostname) {
            $this->hostname = $hostname;
        }

        public function set_type($type) {
            $this->type = $type;
        }

        public function get_name() {
            return $this->name;
        }

        public function get_hostname() {
            return $this->hostname;
        }

        public function get_last_status() {
            return $this->last_status;
        }

        //Database coming soon
        private function log_down() {
            
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
            //Check the type
            $this->last_status = $this->ping();
            return $this->last_status;
        }
    }