<?
    $response =  array("doesn't look like anything to me");
    
    $method = $_SERVER['REQUEST_METHOD'];

    $response[] = $method;
    $data = json_encode($response, JSON_FORCE_OBJECT);

    if ($method === 'POST') {
        echo "{'nice': 'post'}";
    }
    
    else if ($method === 'GET') {
        echo $data;
    }
?>