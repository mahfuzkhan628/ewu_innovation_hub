<?php

include "../config/database.php";

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Receive Form Data

    $email = trim($_POST['email']);
    $password = $_POST['password'];



    // Empty Field Validation

    if(empty($email) || empty($password)){

        die("Please fill in all fields.");

    }



    // Check User by Email

    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();



    if($result->num_rows == 1){

        $user = $result->fetch_assoc();



        // Verify Password

        if(password_verify($password, $user['password'])){

            // Store Session Data

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['department'] = $user['department'];



            // Redirect According to Role

            if($user['role'] == "student"){

                header("Location: ../student/dashboard.php");
                exit();

            }

            elseif($user['role'] == "faculty"){

                header("Location: ../faculty/dashboard.php");
                exit();

            }

        }

        else{

            die("Incorrect password.");

        }

    }

    else{

        die("No account found with this email.");

    }



    $stmt->close();
    $conn->close();

}

?>