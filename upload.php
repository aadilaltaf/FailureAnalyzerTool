<!DOCTYPE html>
<html>
   <head>
      <title>SonicWALL</title>
      <meta charset ="utf-8">
      <meta name = "viewport" content = "width = device-width, initial-scale = 1.0">
      <!-- Bootstrap -->
      <link rel="icon" href="images/favicon.ico">
      <link href = "css/bootstrap.min.css" rel = "stylesheet">
      <link href = "css/sonicwall.css" rel = "stylesheet">
      <script src = "js/bootstrap.min.js"></script>
   </head>
   <body>
    <div class = "container">
        <div class="row"> <h1 class="col-md-12" id="toolname">Failure Analyzer Tool <a href='index.html' id="copy">&copy</a></h1></div>
            <div class="row" align="center">
                <?php
                error_reporting(E_ALL);
                ini_set("display_errors",true);
                require_once 'functions.php';
                class Analyzer
                {
                    function __construct()
                    {
                        $log_file=sanitizeString($_FILES['log_file']['name']);
                        $log_link=sanitizeString($_POST['log_link']);
                        $test_type=sanitizeString($_POST['test_type']);
                        $product=sanitizeString($_POST['product']);
                        if(isset($log_file) && !empty($log_file) && isset($log_link) && !empty($log_link)){
                            die('<h2 id="error">Error: Please Provide Either Log File Only Or Just The Log Link.</h2>');
                        }
                        if(empty($log_file) && empty($log_link)){
                            die('<h2 id="error">Error: Please Provide Either Log File Or Log Link At Least</h2>');
                        }
                        else{
                            if(!empty($log_file)){
                                echo "<h5>File Name: <i><a href='logs/" . $log_file . "' target=_blank>" . $log_file ."</a></i></h5>";
                                $log_link="";
                            }
                            if(!empty($log_link)){
                                echo "<h5>Log Link: <i><a href='" . $log_link . "' target=_blank>" . $log_link ."</a></i></h5>";
                                $file_name = basename($log_link);
                                echo "<h5>File Name: <i>" . $file_name ."</i></h5>";
                            }
                        }  
                        if(empty($test_type)){
                            die('<h2 id="error">Error: Please Provide Test Type</h2>');
                        }
                        else{
                            echo "<h5>Test Type: <i>" . $test_type ."</i></h5>";
                            if($test_type=='API'){
                                die('<h2 id="error">API LOG PARSER WORK STILL IN PROGRESS</h2>');
                            }
                        }  
                        if(isset($log_file) && !empty($log_file)){
                            $errors= array();
                            $file_name = $_FILES['log_file']['name'];
                            $file_size = $_FILES['log_file']['size'];
                            $file_tmp = $_FILES['log_file']['tmp_name'];
                            $file_type = $_FILES['log_file']['type'];
                            @$file_ext=strtolower(end(explode('.',$file_name)));
                            
                            $extensions= array("log","txt");
                            
                            if(in_array($file_ext,$extensions)=== false){
                                die("<h3 id='error'>This is not a log file, please select a log file with '.log' extension.</h3>");
                            }
                            if($file_size > 5242880) {
                                die('<h3 id="error">File size must not be greater than 5 MB</h3>');
                            }
                            move_uploaded_file($file_tmp,"logs/".$file_name);
                            echo "<h6>File Upload Successful</h6>";
                            echo "</div>";
                            echo "</div>";
                            if($test_type=='API'){
                                require_once 'log_parser_api.php';
                            }
                            else{
                                require_once 'log_parser_ui.php';
                            }
                            
                        }
                        else if (isset($log_link)){
                            $file_extension = substr($file_name, -4);
                            if ($file_extension != '.log')
                            {
                                die('<h3 id="error">Not a log file, please specify a log file link with file extension as ".log"</h3>');
                            }
                            else
                            {
                                if (file_put_contents("logs/".$file_name, file_get_contents($log_link)))
                                {
                                    echo "<h6>File Accessed successfully</h6>";
                                }
                                else
                                {
                                    die('<h3 id="error">File Access failed.</h3>');
                                }
                            }
                            echo "</div>";
                            echo "</div>";
                            if($test_type=='API'){
                                require_once 'log_parser_api.php';
                            }
                            else{
                                require_once 'log_parser_ui.php';
                            }
                        }
                    }
                }
                $analyzer = new Analyzer;
                ?>
        </div>
   </body>
</html>
