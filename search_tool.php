<?php
$login_url = 'http://10.5.64.23/api/login';
$username = 'blruser';
$password = 'userblr';

$data = [
    'username' => $username,
    'password' => $password
  ];
$curl = curl_init($login_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
  ]);
$response = json_decode(curl_exec($curl), true);
// print_r($response);
$token = $response['message'];
// echo $token;
echo "<br>";
if(str_contains($token, 'Invalid credentials')){
    die('<br><h2 id="error">Login Failed Due To Invalid credentials</h2>');
}
curl_close($curl);

if($product=='NSM'){
    // $get_testsummary_url = "https://blrauto.eng.sonicwall.com/api/get_test_summary?product=NSM&platform=All&scmlabel=".strtolower($scm_label);
    $get_testsummary_url = "http://10.5.64.23/api/get_test_summary?product=NSM&platform=All&scmlabel=".strtolower($scm_label);
    // echo $nsm_get_testsummary_url;
    // echo "<br>";    
}
else if($product=='SONICOSUI7'){
    // $get_testsummary_url = "https://blrauto.eng.sonicwall.com/api/get_test_summary?product=CloudWAF&platform=All&scmlabel=".strtolower($scm_label);
    $get_testsummary_url = "http://10.5.64.23/api/get_test_summary?product=CloudWAF&platform=All&scmlabel=".strtolower($scm_label);
    // echo $sonicosui7_get_testsummary_url;
    // echo "<br>";    
}
$curl = curl_init($get_testsummary_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Authorization:' . 'TOKEN ' .$token,
    'Content-Type: application/json'
]);
$response = json_decode(curl_exec($curl), true);
// print_r($response);
curl_close($curl);
$data = array();
$suite_names = array();
$log_links = array();

$suites_containing_text  = array();
$log_link_containing_text  = array();
$final_data = array();

if(empty($response['message'])){
    die('<br><h2 id="error">No Data Found (Please check if the <i>SCM Label</i> is correct or the appropriate <i>Product</i> has been selected)</h2>');
}
foreach($response['message'] as $key => $value)
{
    array_push($suite_names, $value['testsuite']);
    array_push($log_links, $value['log_location']);
    $data = array_combine($suite_names, $log_links);
}
foreach($data as $suite_name => $log_link)
{
    $file_name = basename($log_link);
    if (file_put_contents("quick_search_logs/".$file_name, file_get_contents($log_link)))
    {
        $file = fopen("quick_search_logs/".$file_name, 'r' );
        $filesize = filesize("quick_search_logs/".$file_name);
        $filetext = fread( $file, $filesize);
        if (str_contains($filetext, $search_query))
        { 
            array_push($suites_containing_text, $suite_name);
            array_push($log_link_containing_text, $log_link);
            $final_data = array_combine($suites_containing_text, $log_link_containing_text);
        }
        fclose( $file );
    }
    else
    {
        echo('<h6 id="error">File Access failed: </h6>'.$file_name);
    }
}
if(empty($final_data)){
    die('<br><h2 id="error">No test suite log contains the search query text that was proviced.</h2>');
}
else{
    echo '<h3>Total No.of Suites Having Query String: <i>' . count($final_data). '</i></h6>';
    echo "<div id='table'>";
    echo "<table>";
    echo "<tr><th>Test Suite Name</th><th>Log Link</th></tr>";
    foreach($final_data as $suite_name => $log_link)
    {
        $file_name = basename($log_link);
        echo "<tr><td>$suite_name</td><td><a href='quick_search_logs/" . $file_name . "' target=_blank>$log_link</a></td>";
    }
    echo "</table>";
    echo "</div>";
    
}
?>
