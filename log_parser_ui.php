<?php
function log_parser_without_subtest($file_name, $log_link)
{
    // echo $log_link;
    $path = rtrim($log_link, $file_name);
    // echo $path;
    $file = fopen("logs/".$file_name, 'r' );
    $errors = array();
    $other_messages = array();
    $testcase_start_line_no = 0;
    $testcase_end_line_no = 0;
    echo "<div id='table'>";
    echo "<table>";
    echo "<tr><th>S.No</th><th>Test Case Name</th><th>Pass/Fail</th><th>Error Line - Error/Exception occured if any</th><th>Other Messages</th></tr>";
    $testcase_end = FALSE;
    $i=1;
    $line_no = 0;
    while(!feof($file)) {                                                                       // read till end of file
        $line = fgets($file);                                                                   // pick line one at a time
        $line_no = $line_no +1;
        if (str_contains($line, '########## STARTING CASE:')) { 
            $line_array_1 = explode("#",$line);
            $testcase_name = $line_array_1[10];
            $testcase_name = ltrim($testcase_name,"STARTING CASE:");   
            $testcase_start_line_no = $line_no; 
        }
        if (str_contains($line, 'Exception 	:') && str_contains($line, 'ERROR')) { 
            $line_array_2 = explode("] -",$line);
            $error = $line_no . ' - ' . $line_array_2[1];
            array_push($errors, $error);
        }
        if (str_contains($line, 'ERROR') && !str_contains($line, 'Exception 	:')) { 
            $line_array_2 = explode("] -",$line);
            $error = $line_no . ' - ' . $line_array_2[1];
            array_push($errors, $error);
        }
        if (str_contains($line, '"success": false')|| str_contains($line, '"code": "E_ERR"') || str_contains($line, '"level": "error"') || str_contains($line, "'success': False") || str_contains($line, "'code': 'E_ERR'") || str_contains($line, "'level': 'error'") ) { 
            $line_array_2 = explode("] -",$line);
            $error = $line_no . ' - ' . $line_array_2[1];
            array_push($errors, $error);
        }
        if (str_contains($line, 'Operation Result error message') || str_contains($line, 'Commit Result error message')) { 
            $line_array_3 = explode("] -",$line);
            array_push($other_messages, $line_array_3[1]);
        }
        if (str_contains($line, '########## Testcase:')) {
            $testcase_end=TRUE;
            $line_array_4 = explode("#",$line);
            $testcase_result = $line_array_4[10];
            $testcase_result = ltrim($testcase_result, "Testcase: " . $testcase_name);
            $testcase_end_line_no = $line_no; 
        }
        if ($testcase_end==TRUE) 
        {
            $testcase_end = False;

            echo "<tr><td>$i</td><td  id='testcase_name'>$testcase_name</td><td>$testcase_result</td>";
            echo "<td  id='exceptions'>";
            if(empty($errors)) {
                echo "No Errors";
            }
            else {
                // echo "Test Case Start Line No: " .$testcase_start_line_no;
                echo "<br>";
                foreach ($errors as $error) {
                    echo($error);
                    echo "<br>";
                }
                echo "Test Case End Line No: " .$testcase_end_line_no;
            }
            echo "</td>";
            echo "<td>";
            if(empty($other_messages)) {
                echo "No Other Message";
            }
            else {
                foreach ($other_messages as $message) {
                    echo($message);
                    echo "<br>";
                }
            }
            echo "</td>";
            echo "</tr>";
            $errors = array();
            $other_messages = array();
            $i=$i+1;
            $line_no = 0;
        }
        
    }
    fclose($file);
    echo "</table>";
    echo "</div>";
}
function log_parser_with_subtest($file_name)
{
    // echo "Subtests found in logs";
    $file = fopen("logs/".$file_name, 'r' );
    $errors = array();
    $other_messages = array();
    $subtest_testcase_names  = array();
    $subtest_final_names = array();
    $subtest_errors  = array();
    $subtest_other_messages  = array();
    $subtest_results = array();
    $final_results = array();
    $subtest = FALSE;
    $main_testcase_end_line_no = 0;
    $subtest_name = "";
    echo "<div id='table'>";
    echo "<table>";
    echo "<tr><th>S.No</th><th>Test Case Name</th><th>Pass/Fail</th><th>Error Line - Error/Exception occured if any</th><th>Other Messages</th></tr>";
    $main_testcase_end = FALSE;
    $i=1;
    $j=0;
    $line_no = 0;
    $no_of_errors = 0;
    while(!feof($file)) {                                                                       // read till end of file
        $line = fgets($file);                                                                   // pick line one at a time
        $line_no = $line_no +1;
        if (str_contains($line, '########## STARTING CASE:')) { 
            $line_array_1 = explode("#",$line);
            $testcase_name = $line_array_1[10];
            $testcase_name = ltrim($testcase_name,"STARTING CASE:");
        }
        if (str_contains($line, '########## STARTING SUBTEST CASE:')) {
            $subtest = TRUE;
            $line_array_1 = explode("#",$line);
            $subtest_name= $line_array_1[10];
            $subtest_name = ltrim($subtest_name,"########## STARTING SUBTEST CASE:");
            $subtest_name = trim($subtest_name);
            $subtest_testcase_names[] = $subtest_name;
        }
        if (str_contains($line, 'Exception') && str_contains($line, 'ERROR')) { 
            if ($subtest==TRUE){
                $no_of_errors = $no_of_errors+1;
                $line_array_2 = explode("] -",$line);
                $error = $line_no . ' - ' . $line_array_2[1];
                $subtest_errors[$subtest_name][$no_of_errors] = $error;
            }
            else{
                $line_array_2 = explode("] -",$line);
                $error = $line_no . ' - ' . $line_array_2[1];
                array_push($errors, $error);
            }
        }
        if (str_contains($line, 'ERROR') && !str_contains($line, 'Exception') && !str_contains($line, 'SUB TEST :')) { 
            if ($subtest==TRUE){
                $no_of_errors = $no_of_errors+1;
                $line_array_2 = explode("] -",$line);
                $error = $line_no . ' - ' . $line_array_2[1];
                $subtest_errors[$subtest_name][$no_of_errors] = $error;
            }
            else{
                $line_array_2 = explode("] -",$line);
                $error = $line_no . ' - ' . $line_array_2[1];
                array_push($errors, $error);
            }
        }
        if (str_contains($line, '"success": false')|| str_contains($line, '"code": "E_ERR"') || str_contains($line, '"level": "error"') || str_contains($line, "'success': False") || str_contains($line, "'code': 'E_ERR'") || str_contains($line, "'level': 'error'") || str_contains($line, '"level":"error"')) { 
            if ($subtest==TRUE){
                $no_of_errors = $no_of_errors+1;
                $line_array_2 = explode("] -",$line);
                $error = $line_no . ' - ' . $line_array_2[1];
                $subtest_errors[$subtest_name][$no_of_errors] = $error;
            }
            else{
                $line_array_2 = explode("] -",$line);
                $error = $line_no . ' - ' . $line_array_2[1];
                array_push($errors, $error);
            }
        }
        if (str_contains($line, 'Operation Result error message') || str_contains($line, 'Commit Result error message')) { 
            if ($subtest==TRUE){
                $line_array_3 = explode("] -",$line);
                // array_push($subtest_other_messages[$subtest_name], $line_array_3);
            }
            else{
                $line_array_3 = explode("] -",$line);
                array_push($other_messages, $line_array_3[1]);
            }     
        }
        if (str_contains($line, 'SUB TEST :')) {
            $line_array_4 = explode("SUB TEST :",$line);
            $subtest_data = $line_array_4[1];
            $subtest_data = explode(": ",$subtest_data);
            $subtest_final_name = $subtest_data[0];
            $subtest_result = $subtest_data[1];
            $subtest_final_names[] = trim($subtest_final_name);
            $subtest_results[] = trim($subtest_result);
        }
        if (str_contains($line, '########## Testcase:')) {
            $main_testcase_end=TRUE;
            $line_array_5 = explode("#",$line);
            $testcase_result = $line_array_5[10];
            $testcase_result = ltrim($testcase_result, "Testcase: " . $testcase_name);
            $main_testcase_end_line_no = $line_no;
        } 
        if ($main_testcase_end==TRUE) 
        {
            $main_testcase_end = False;
            $final_results = array_combine($subtest_final_names, $subtest_results);
            foreach ($final_results as $subtest_case_name => $result) {
                if (!array_key_exists(trim($subtest_case_name), $subtest_errors))
                {
                    $subtest_errors[$subtest_case_name][0] = 'No Errors';
                }
            }
            foreach ($subtest_errors as $subtest_case_name => $subtest_errors) {
                echo "<tr><td>SubTest</td>";
                echo "<td  id='testcase_name'>$subtest_case_name</td>";
                $subtest_case_name = trim($subtest_case_name);
                echo "<td>$final_results[$subtest_case_name]</td>";
                echo "<td  id='exceptions'>";
                foreach ($subtest_errors as $error_no => $error) {
                    echo $error;
                    echo "<br>";
                }
                echo "</td>";
                echo "<td>No Other Message</td>";
                echo "</tr>";
            }
            echo "<tr><td>$i</td><td id='testcase_name'>$testcase_name</td><td>$testcase_result</td>";
            echo "<td  id='exceptions'>";
            if(empty($errors)) {
                echo "No Errors";
            }
            else {
                echo "<br>";
                foreach ($errors as $error) {
                    echo($error);
                    echo "<br>";
                }
            }
            echo "</td>";
            echo "<td>";
            if(empty($other_messages)) {
                echo "No Other Message";
            }
            else {
                foreach ($other_messages as $message) {
                    echo($message);
                    echo "<br>";
                }
            }
            echo "</td>";
            echo "</tr>";
            $final_results = array();
            $subtest_testcase_names  = array();
            $subtest_final_names = array();
            $subtest_errors  = array();
            $subtest_other_messages  = array();
            $subtest_results = array();
            $final_results = array();
            $errors = array();
            $other_messages = array();
            $i=$i+1;
            $subtest = FALSE;
        }    
        
    }
    fclose($file);
    echo "</table>";
    echo "</div>";
}

$file = fopen("logs/".$file_name, 'r' );
$filesize = filesize("logs/".$file_name);
$filetext = fread( $file, $filesize);
fclose( $file );
if (str_contains($filetext, '########## STARTING SUBTEST CASE: '))
 { 
    log_parser_with_subtest($file_name);
}
else
{
    log_parser_without_subtest($file_name, $log_link);
}
?>
