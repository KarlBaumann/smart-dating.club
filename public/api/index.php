<?php
require_once '../vendor/autoload.php'; // change path as needed

//$inputJSON = file_get_contents('php://input');
//$input = json_decode($inputJSON, TRUE); //convert JSON into array

//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Credentials: true');
//header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

//function getPostObject()
//{
//    $str = file_get_contents('php://input');
//    $std = json_decode($str);
//    if ($std === null) {
//        $std = new stdClass();
//        $array = explode('&', $str);
//        foreach ($array as $parm) {
//            $parts = explode('=', $parm);
//            if (sizeof($parts) != 2) {
//                continue;
//            }
//            $key = $parts[0];
//            $value = $parts[1];
//            if ($key === NULL) {
//                continue;
//            }
//            if (is_string($key)) {
//                $key = urldecode($key);
//            } else {
//                continue;
//            }
//            if (is_bool($value)) {
//                $value = boolval($value);
//            } else if (is_numeric($value)) {
//                $value += 0;
//            } else if (is_string($value)) {
//                if (empty($value)) {
//                    $value = null;
//                } else {
//                    $lower = strtolower($value);
//                    if ($lower === 'true') {
//                        $value = true;
//                    } else if ($lower === 'false') {
//                        $value = false;
//                    } else if ($lower === 'null') {
//                        $value = null;
//                    } else {
//                        $value = urldecode($value);
//                    }
//                }
//            } else if (is_array($value)) {
//                // value is an array
//            } else if (is_object($value)) {
//                // value is an object
//            }
//            $std->$key = $value;
//        }
//        // length of post array
//        //$std->length = sizeof($array);
//    }
//    return $std;
//}
//
//$json = getPostObject();


$fb = new Facebook\Facebook([
    'app_id' => 1697318077067239,
    'app_secret' => get_env(APP_SECRET),
    'default_graph_version' => 'v4.0',
]);

$helper = $fb->getJavaScriptHelper();

try {
    $accessToken = $helper->getAccessToken();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    $return = ['error' => 'Graph returned an error: ' . $e->getMessage()];
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    $return = ['Facebook SDK returned an error: ' . $e->getMessage()];
    exit;
}

if (!isset($accessToken)) {
    $return = ['No cookie set or no OAuth data could be obtained from cookie.'];
    exit;
}


$fb->setDefaultAccessToken((string)$accessToken);


$batch = [
    'user-profile' => $fb->request('GET', '/me?fields=id,name,birthday,email,hometown,address,first_name,gender,languages,last_name,link,location,middle_name,short_name,significant_other,friends,family,groups,picture.width(3024).height(3024)'),
    //'user-photos' => $fb->request('GET', '/me/photos?fields=id,source,name'),
    //'user-groups' => $fb->request('GET', '/me/groups'),
];

try {
    $responses = $fb->sendBatchRequest($batch);
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

foreach ($responses as $key => $response) {

    $user = $response->getDecodedBody();

}




$person = [
    'id' => $user['id'],
    'email' => $user['email'] ?? null,
    'birthday' => $user['birthday'] ?? null,
    'gender' => $user['gender'] ?? null,
    'languages' => json_encode($user['languages']) ?? null,
    'link' => $user['link'] ?? null,
    'short_name' => $user['short_name'] ?? null,
    'location' => json_encode($user['location']) ?? null,
    'friends' => json_encode($user['friends']) ?? null,
    'groups' => json_encode($user['groups']) ?? null,
    'picture' => json_encode($user['picture']) ?? null,
    'last_name' => $user['last_name'] ?? null,
    'first_name' => $user['first_name'] ?? null
];

//echo json_encode($user->all());


$pdo = new PDO('mysql:host=localhost;dbname=smart-dating', 'smart-dating', 'gnitadßtrams');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

function getPerson($id, $pdo)
{
    $statement = $pdo->prepare("SELECT * FROM persons WHERE id = :id");
    $statement->execute(array(':id' => $id));
    return $statement->fetch(PDO::FETCH_ASSOC);
}

function addPerson($person, $pdo)
{
    $person = [
        ':id' => (int)$person['id'],
        ':first_name' => $person['first_name'] ?? null,
        ':last_name' => $person['last_name'] ?? null,
        ':email' => $person['email'] ?? null,
        ':soc_type' => $person['soc_type'] ?? null,
        ':dating_status' => $person['dating_status'] ?? null,
        ':birthday' => $person['birthday'] ?? null,
        ':gender' => $person['gender'] ?? null,
        ':languages' => $person['languages'] ?? null,
        ':link' => $person['link'] ?? null,
        ':short_name' => $person['short_name'] ?? null,
        ':location' => $person['location'] ?? null,
        ':friends' => $person['friends'] ?? null,
        ':groups' => $person['groups'] ?? null,
        ':picture' => $person['picture'] ?? null
    ];


    try {
        $sql = "INSERT INTO `persons` 
                       (id,first_name,last_name,email,birthday,gender,languages,link,short_name,location,friends,groups,picture,soc_type,dating_status) 
                VALUES (:id,:first_name,:last_name,:email,:birthday,:gender,:languages,:link,:short_name,:location,:friends,:groups,:picture,:soc_type,:dating_status)";
        $statement = $pdo->prepare($sql);
        $statement->execute($person);
    } catch (PDOException $e) {
        throw $e;
    } catch (Exception $e) {
        throw $e;
    }
    return $person;
}

$row = getPerson($person['id'], $pdo);

if ($row) {

    $return = $row;

} else {

    $return = addPerson($person, $pdo);

}

header('Content-Type: application/json');
exit(json_encode($return));