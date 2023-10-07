<?php
require_once "db/dbconn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_email = $_POST["email"];
    $input_password = $_POST["password"];

    $stmt = $conn->prepare("SELECT commuterid, password FROM commuter WHERE email = ?");
    $stmt->bind_param("s", $input_email);
    $stmt->execute();
    $stmt->store_result();

    $stmt->bind_result($commuterid, $password);
    $stmt->fetch();

    if ($stmt->num_rows == 1 && $input_password == $password) {
        session_id($commuterid);
        session_start();

        $_SESSION["commuterid"] = $commuterid;

        header("Location: commuter/commuter.php");
        exit();
    } else {
        $stmt->close();

        // If not found in commuter table, check dispatcher table
        $stmt = $conn->prepare("SELECT dispatcherid, password FROM dispatcher WHERE email = ?");
        $stmt->bind_param("s", $input_email);
        $stmt->execute();
        $stmt->store_result();

        $stmt->bind_result($dispatcherid, $password);
        $stmt->fetch();

        if ($stmt->num_rows == 1 && $input_password == $password) {
            session_id($dispatcherid);
            session_start();

            $_SESSION["dispatcherid"] = $dispatcherid;

            header("Location: dispatcher/dispatcher.php");
            exit();
        } else {
            $stmt->close();

            header("Location: index.php?error=true");
            exit();
        }
    }
}

$conn->close();
?>