<?php

require_once '../include/DbHandler.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

header('Access-Control-Allow-Origin: *');

//Get all users
$app->get('/users', function() {
    header("Access-Control-Allow-Origin: *");
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    $response = array();
    $db = new DbHandler();
    $result = $db->getAllUsers();

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        $tmp["email"] = $row["email"];
        $tmp["phone"] = $row["phone"];
        $tmp["country"] = $row["country"];

        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});

//Get user with :id
$app->get('/users/:id', function($id) {
    $response = array();
    $db = new DbHandler();
    $result = $db->getUserById($id);

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        $tmp["email"] = $row["email"];
        $tmp["phone"] = $row["phone"];
        $tmp["country"] = $row["country"];

        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});

//Get groups for user with :id
$app->get('/users/:id/groups', function($id) {
    $response = array();
    $db = new DbHandler();
    $result = $db->getGroupsByUserId($id);

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        $tmp["user_id"] = $row["user_id"];
        $tmp["name"] = $row["name"];

        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});

//Get users for group with id
$app->get('/groups/:id', function($id) {
    $response = array();
    $db = new DbHandler();
    $result = $db->getGroupById($id);

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        $tmp["email"] = $row["user_id"];
        $tmp["phone"] = $row["name"];

        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});

$app->get('/groups/:id/users', function($id) {
    $response = array();
    $db = new DbHandler();
    $result = $db->getUsersByGroupId($id);

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});

//Get snappvote with :id
$app->get('/snappvotes/:id', function($id) {
    $response = array();
    $db = new DbHandler();
    $result = $db->getSnappvoteById($id);

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        $tmp["author_id"] = $row["author_id"];
        $tmp["title"] = $row["title"];
        $tmp["img_1"] = $row["img_1"];
        $tmp["img_2"] = $row["img_2"];
        $tmp["answer_1"] = $row["answer_1"];
        $tmp["answer_2"] = $row["answer_2"];
        $tmp["expire_date"] = $row["expire_date"];

        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});

//Gets outgoing snappvotes for user with :id
$app->get('/snappvotes/out/:id', function($id) {
    $response = array();
    $db = new DbHandler();
    $result = $db->getSnappvotesByAuthorId($id);

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        $tmp["author_id"] = $row["author_id"];
        $tmp["title"] = $row["title"];
        $tmp["img_1"] = $row["img_1"];
        $tmp["img_2"] = $row["img_2"];
        $tmp["answer_1"] = $row["answer_1"];
        $tmp["answer_2"] = $row["answer_2"];
        $tmp["expire_date"] = $row["expire_date"];
        $tmp["usernames"] = [];
        $tmp["answers_ids"] = [];
        $result2 = $db->getVotersBySnappvoteId($row["id"]);
        while ($row2 = $result2->fetch_assoc()) {
            array_push($tmp["usernames"], $row2["username"]);
            array_push($tmp["answers_ids"], $row2["answer_id"]);
        }

        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});

$app->get('/snappvotes/out/answers/:id', function($id) {
    $response = array();
    $db = new DbHandler();
    $result = $db->getVotersBySnappvoteId($id);

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["username"] = $row["username"];
        $tmp["answer_id"] = $row["answer_id"];

        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});


//Get incoming snappvotes for user with :id
$app->get('/snappvotes/in/:id', function($id) {
    $response = array();
    $db = new DbHandler();
    $result = $db->getSnappvoteByVoterId($id);

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        $tmp["author_id"] = $row["author_id"];
        $tmp["title"] = $row["title"];
        $tmp["img_1"] = $row["img_1"];
        $tmp["img_2"] = $row["img_2"];
        $tmp["answer_1"] = $row["answer_1"];
        $tmp["answer_2"] = $row["answer_2"];
        $tmp["expire_date"] = $row["expire_date"];
        $user_result = $db->getUserById($tmp["author_id"]);

        //$tmp["user"] = array();
        while ($user_row = $user_result->fetch_assoc()) {
            $tmp["author_username"] = $user_row["username"];
        }
        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});

//Get answers for snappvote wiht :id
$app->get('/snappvotes/answers/:id', function($id) {
    $response = array();
    $db = new DbHandler();
    $result = $db->getAnswersBySnappvoteId($id);

    while ($row = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["snappvote_id"] = $row["snappvote_id"];
        $tmp["voter_id"] = $row["voter_id"];
        $tmp["answer_id"] = $row["answer_id"];

        array_push($response, $tmp);
    }

    echoResponse(200, $response);
});

//Create new user
$app->post('/users', function() use ($app) {
    $response = array();
    $username = $app->request->post('username');
    $email = $app->request->post('email');
    $phone = $app->request->post('phone');
    $country = $app->request->post('country');

    $db = new DbHandler();
    $success = $db->createUser($username, $email, $phone, $country);

    if ($success) {
        $response["error"] = false;
        $response["id"] = $db->getLastId();
        $response["message"] = "User created successfully";
    } else {
        $response["error"] = true;
        $response["message"] = "Error. Please try again";
    }
    echoResponse(201, $response);
});

//Create new group for user with :id
$app->post('/groups/:id', function($id) use ($app) {
    $response = array();
    $name = $app->request->post('name');

    $db = new DbHandler();

    $success = $db->createGroup($id, $name);

    if ($success) {
        $response["error"] = false;
        $response["message"] = "Group created successfully";
    } else {
        $response["error"] = true;
        $response["message"] = "Error. Please try again";
    }
    echoResponse(201, $response);
});

//Add user with :id to group with :gid
$app->post('/groups/:id/:gid', function($user_id, $group_id) use ($app) {
    $response = array();

    $db = new DbHandler();

    $success = $db->addUserToGroup($user_id, $group_id);

    if ($success) {
        $response["error"] = false;
        $response["message"] = "User added successfully";
    } else {
        $response["error"] = true;
        $response["message"] = "Error. Please try again";
    }
    echoResponse(201, $response);
});

//Create new outgoing snappvote for user with :id
$app->post('/snappvotes/out/:id', function($id) use ($app) {
    $response = array();
    $author_id = $id;
    $title = $app->request->post('title');
    $img_1 = $app->request->post('img_1');
    $img_2 = $app->request->post('img_2');
    $answer_1 = $app->request->post('answer_1');
    $answer_2 = $app->request->post('answer_2');
    $expire_date = $app->request->post('expire_date');
    $contacts_ids = $app->request->post('contacts_ids');
    $db = new DbHandler();

    $success = $db->createSnappvote($author_id, $title, $img_1, $img_2, $answer_1, $answer_2, $expire_date);
    $contactsSuccess = $db->createAnswersBulk($db->getLastId(), $contacts_ids, -1);

    if ($success) {
        $response["error"] = false;
        $response["id"] = $db->getLastId();
        $response["message"] = "Snappvote created successfully.";
    } else {
        $response["error"] = true;
        $response["message"] = "Error. Please try again";
    }
    echoResponse(201, $response);
});

//Create new answer for snappvote with :id
$app->post('/snappvotes/answers/:id', function($id) use ($app) {
    $response = array();
    $voter_id = $app->request->post('voter_id');
    $answer_id = $app->request->post('answer_id');

    $db = new DbHandler();

    $success = $db->createAnswer($id, $voter_id, $answer_id);

    if ($success) {
        $response["error"] = false;
        $response["message"] = "Answer created successfully";
    } else {
        $response["error"] = true;
        $response["message"] = "Error. Please try again";
    }
    echoResponse(201, $response);
});

$app->put('/snappvotes/answers/:id', function($id) use ($app) {
    $response = array();

    $db = new DbHandler();
    $voter_id = $app->request->post('voter_id');
    $answer_id = $app->request->post('answer_id');
    $success = $db->updateAnswer($id, $voter_id, $answer_id);

    if ($success) {
        $response["error"] = false;
        $response["message"] = "Answer updated";
    } else {
        $response["error"] = true;
        $response["message"] = "Error. Please try again";
    }
    echoResponse(201, $response);
});

$app->post('/test', function() use ($app) {

});

function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}

function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
?>
