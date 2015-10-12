<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 */
class DbHandler {

    private $conn;

    function __construct() {
        //require_once dirname(__FILE__) . './DbConnect.php';
        // opening db connection
        //$db = new DbConnect();
        $this->conn = mysqli_connect("localhost", "root", "", "Snappvote_DB"); //$db->connect();
    }

    public function getLastId() {
        return mysqli_insert_id($this->conn);
    }

    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM `User`");
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM `User`"
                . " WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getGroupsByUserId($id) {
        $stmt = $this->conn->prepare("SELECT `id`, `user_id`, `name` FROM `Group` WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getGroupById($id) {
        $stmt = $this->conn->prepare("SELECT `id`, `user_id`, `name` FROM `Group` WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getUsersByGroupId($id) {
        $stmt = $this->conn->prepare("SELECT User.id FROM `User`"
                . " INNER JOIN `Group_Contact` ON Group_Contact.contact_id = User.id"
                . " AND Group_Contact.group_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getSnappvoteById($id) {
        $stmt = $this->conn->prepare("SELECT `id`, `author_id`, `title`, "
                . "`img_1`, `img_2`, `answer_1`, `answer_2`, `expire_date`"
                . " FROM `Snappvote` WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getVotersBySnappvoteId($id) {
        $stmt = $this->conn->prepare("SELECT User.username, Snappvote_Answer.answer_id  FROM `User`"
                . " INNER JOIN `Snappvote_Answer` ON Snappvote_Answer.voter_id = User.id"
                . " AND Snappvote_Answer.snappvote_id = ?");

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getSnappvotesByAuthorId($id) {
        $stmt = $this->conn->prepare("SELECT `id`, `author_id`, `title`, "
                . "`img_1`, `img_2`, `answer_1`, `answer_2`, `expire_date`"
                . " FROM `Snappvote` WHERE author_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getAnswersBySnappvoteId($id) {
        $stmt = $this->conn->prepare("SELECT `id`, `snappvote_id`, `voter_id`, `answer_id`"
                . " FROM `Snappvote_Answer` WHERE snappvote_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function getSnappvoteByVoterId($id) {
        $stmt = $this->conn->prepare("SELECT Snappvote.id, Snappvote.author_id, Snappvote.title, "
                . "Snappvote.img_1, Snappvote.img_2, Snappvote.answer_1, Snappvote.answer_2, Snappvote.expire_date FROM `Snappvote`"
                . " INNER JOIN `Snappvote_Answer` ON Snappvote_Answer.snappvote_id = Snappvote.id AND Snappvote_Answer.voter_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function createGroup($user_id, $name) {
        $stmt = $this->conn->prepare("INSERT INTO `Group`(`user_id`, `name`) VALUES (?,?)");
        $stmt->bind_param("is", $user_id, $name);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function createUser($username, $avatarBase64, $email, $phone, $country) {
        $stmt = $this->conn->prepare("INSERT INTO `User`(`email`, `phone`, `country`, `username`)"
                . " VALUES (?,?,?,?,?)");
        $filename = $this->saveImage($avatarBase64);
        //saveImage($avatarBase64);

        $stmt->bind_param("sssss", $email, $filename, $phone, $country, $username);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function saveImage($base64img) {
        define('UPLOAD_DIR', '../uploads/');
        $base64img = str_replace('data:image/jpeg;base64,', '', $base64img);
        $data = base64_decode($base64img);
        $filename = UPLOAD_DIR . uniqid() . ".jpg";
        file_put_contents($filename, $data);
        return $filename;
    }

    public function createSnappvote($author_id, $title, $img_1, $img_2, $answer_1, $answer_2, $expire_date) {
        $stmt = $this->conn->prepare("INSERT INTO `Snappvote`"
                . "(`author_id`, `title`, `img_1`, `img_2`, `answer_1`, `answer_2`, `expire_date`)"
                . " VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("issssss", $author_id, $title, $img_1, $img_2, $answer_1, $answer_2, $expire_date);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function createAnswer($snappvote_id, $voter_id, $answer_id) {
        $stmt = $this->conn->prepare("INSERT INTO `Snappvote_Answer`(`snappvote_id`, `voter_id`, `answer_id`)"
                . " VALUES (?,?,?)");
        $stmt->bind_param("iii", $snappvote_id, $voter_id, $answer_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function createAnswersBulk($snappvote_id, $contacts_ids, $answer_id) {
        $stmt = $this->conn->prepare("INSERT INTO `Snappvote_Answer`(`snappvote_id`, `voter_id`, `answer_id`) VALUES (?,?,?)");
        for ($x = 0; $x <= 10; $x++) {
            $stmt->bind_param("iii", $snappvote_id, $contacts_ids[$x], $answer_id);
            $result = $stmt->execute();
            if ($result) {
        } else {
            return FALSE;
        }
        }

        $stmt->close();
        return TRUE;

    }

    public function addUserToGroup($user_id, $group_id) {
        $stmt = $this->conn->prepare("INSERT INTO `Group_Contact`(`group_id`, `contact_id`) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $group_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function createContact($user_id, $name) {
        $stmt = $this->conn->prepare("INSERT INTO `Group`(`user_id`, `name`) VALUES (?,?)");
        $stmt->bind_param("is", $user_id, $name);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateAnswer($snappvote_id, $voter_id, $answer_id) {
        $stmt = $this->conn->prepare("UPDATE `Snappvote_Answer` SET `answer_id`=? WHERE snappvote_id = ? AND voter_id = ?");
        $stmt->bind_param("iii", $answer_id, $snappvote_id, $voter_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>
