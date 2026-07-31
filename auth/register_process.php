<?php


include "../config/database.php";



if($_SERVER["REQUEST_METHOD"] == "POST"){



    // Receive form data

    $name = trim($_POST['name']);

    $email = trim($_POST['email']);

    $password = $_POST['password'];

    $confirm_password = $_POST['confirm_password'];

    $role = $_POST['role'];

    $department = trim($_POST['department']);





    // 1. Empty field validation

    if(
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password) ||
        empty($department)
    ){

        die("All fields are required");

    }





    // 2. Password match validation


    if($password !== $confirm_password){


        die("Password does not match");


    }





    // 3. Email validation


    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){


        die("Invalid email address");


    }







    // 4. Duplicate email check


    $check_email = 
    "SELECT user_id FROM users WHERE email = ?";



    $stmt = $conn->prepare($check_email);



    $stmt->bind_param("s", $email);



    $stmt->execute();



    $result = $stmt->get_result();




    if($result->num_rows > 0){


        die("Email already registered");


    }







    // 5. Password hashing


    $hashed_password = 
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );








    // 6. Insert user into database


    $insert_query = 
    "INSERT INTO users
    (name,email,password,role,department)

    VALUES
    (?,?,?,?,?)";




    $stmt = $conn->prepare($insert_query);



    $stmt->bind_param(

        "sssss",

        $name,

        $email,

        $hashed_password,

        $role,

        $department

    );







    // 7. Execute and redirect


    if($stmt->execute()){



        header("Location: ../login.php");

        exit();



    }
    else{


        echo "Registration failed";


    }



}



?>