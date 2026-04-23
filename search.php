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
        <div class="row"> <h1 class="col-md-12" id="toolname"><u>Quick Log Search</u> <a href='quick_log_search.html' id="copy">&copy</a></h1></div>
            <div class="row" align="center">
                <?php
                error_reporting(E_ALL);
                ini_set("display_errors",true);
                require_once 'functions.php';
                class SearchTool
                {
                    function __construct()
                    {
                        $scm_label=sanitizeString($_POST['scm_label']);
                        $search_query=sanitizeString($_POST['search_query']);
                        $product=sanitizeString($_POST['product']);
                        if(empty($scm_label)){
                            die('<h2 id="error">Error: Please Provide the SCM Label</h2>');
                        }
                        if(empty($search_query)){
                            die('<h2 id="error">Error: Please Provide Search Query (Its Empty)</h2>');
                        }
                        else{
                            if(!empty($scm_label)){
                                echo "<h5>SCM Label: <i>$scm_label</i></h5>";
                            }
                            if(!empty($search_query)){
                                echo "<h5>Search Query: <i>$search_query</i></h5>";;
                            }
                        }  
                        if(empty($product)){
                            die('<h2 id="error">Error: Please Provide Product</h2>');
                        }
                        else{
                            echo "<h5>Product: <i>" . $product ."</i></h5>";
                        }  
                        if(isset($scm_label) && !empty($search_query)){
                            require_once 'search_tool.php';
                        }
                    }
                }
                $search_tool = new SearchTool;
                ?>
        </div>
   </body>
</html>
