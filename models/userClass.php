<?php
class User {
    //atributos

    //getters
    public function getUserByUname($username) {
        $mysqli = openConex();
        $stmt = $mysqli->prepare("SELECT id_user
                                    FROM users
                                    WHERE username = ?
                                    LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function getUserByID($id_user) {
        $mysqli = openConex();
        $stmt = $mysqli->prepare("SELECT username
                                    FROM users
                                    WHERE id_user = ?
                                    LIMIT 1");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    //setters

}