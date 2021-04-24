<!DOCTYPE html>
<html lang="en">
<head>
  <title>Setup - SimpleStatusPage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php
    require_once 'config.php';
    require_once 'vendor/autoload.php';

    use Medoo\Medoo;

    //$database = new Medoo($database_config);
    //CONFIG.php has to contain:
    /*
    <?php
        $installed = FALSE;
    */
    if (!$installed) {
        if ($_POST["database_type"]) {
            $config_file_content = "<?php\n";
            $config_file_content .= '   $installed=TRUE;' . "\n";

            if ($_POST["email"]) {
                $config_file_content .= '   $email="'. $_POST["email"] . '";' . "\n";
            } else {
                $config_file_content .= '   $email=FALSE;' . "\n";
            }

            $database_name = $_POST["database_name"];
            $database_host = $_POST["database_host"];
            $database_username = $_POST["database_username"];
            $database_password = $_POST["database_password"];
            
            if ($_POST["database_type"] === "mysql") {
                $config_file_content .= '   $database_config = array(' . "\n";
                $config_file_content .= "       'database_type' => 'mysql'," . "\n";
                $config_file_content .= "       'database_name' => '$database_name'," . "\n";
                $config_file_content .= "       'server' => '$database_host'," . "\n";
                $config_file_content .= "       'username' => '$database_username'," . "\n";
                $config_file_content .= "       'password' => '$database_password'" . "\n";
                $config_file_content .= "   );" . "\n";

                $database = new Medoo([
                    'database_type' => 'mysql',
                    'database_name' => $database_name,
                    'server' => $database_host,
                    'username' => $database_username,
                    'password' => $database_password
                ]);

                $database->query("
                CREATE TABLE IF NOT EXISTS status(
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    type TEXT NOT NULL,
                    name TEXT NOT NULL,
                    hostname TEXT NOT NULL,
                    status BIGINT NOT NULL,
                    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                );
                ");
            } else if ($_POST["database_type"] === "sqlite") {
                $config_file_content .= '   $database_config = array(' . "\n";
                $config_file_content .= "       'database_type' => 'sqlite'," . "\n";
                $config_file_content .= "       'database_file' => './database.db'" . "\n";
                $config_file_content .= "   );" . "\n"; 

                $database = new Medoo([
                    'database_type' => 'sqlite',
                    'database_file' => './database.db'
                ]);

                $database->query("
                CREATE TABLE IF NOT EXISTS status(
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    type TEXT NOT NULL,
                    name TEXT NOT NULL,
                    hostname TEXT NOT NULL,
                    status BIGINT NOT NULL,
                    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                );
                ");
            }

            file_put_contents("config.php", $config_file_content);
            ?>
            <div class="container">
                <h1>Installed - SimpleStatusPage</h1>

                Now you only have to add this cron job:

                <pre>IS_CRON=1 php <?php echo getcwd(); ?>/cron.php</pre>
            </div>
            <?php
        } else {
            ?>
                <div class="container">
                    <h1>Installation - SimpleStatusPage</h1>
                    <form method="POST">
                        <br>
                        <h2>Database</h2>
                        <div class="form-group">

                            <label for="database_type">Database type:</label>
                            <select name="database_type" id="database_type">
                                <option value="sqlite">SQlite</option>
                                <option value="mysql">MySQL</option>
                            </select>
                            <br><br>
                            <label for="database_name">Database name (if database type is SQLite do not enter):</label>
                            <input type="text" class="form-control" name="database_name" id="database_name" placeholder="Enter database name">
                            <br>
                            <label for="database_host">Database host (if database type is SQLite do not enter):</label>
                            <input type="text" class="form-control" name="database_host" id="database_host" placeholder="Enter database host">
                            <br>
                            <label for="database_username">Database username (if database type is SQLite do not enter):</label>
                            <input type="text" class="form-control" name="database_username" id="database_username" placeholder="Enter database name">
                            <br>
                            <label for="database_password">Database password (if database type is SQLite do not enter):</label>
                            <input type="password" class="form-control" name="database_password" id="database_password" placeholder="Enter database name">

                        </div>

                        <hr>
                        <h2>Alerts</h2>
                        <div class="form-group">
                            <label for="email">Email address for downtime alerts:</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter email">
                        </div>

                        <button type="submit" class="btn btn-primary">Install</button>
                    </form>
                </div>
            <?php
        }
    } else {
        ?>
        Simple Status Page (SSP) has already been succesfully installed. Please refer to our documentation for more info.
        <?php
    }
?>
</body>