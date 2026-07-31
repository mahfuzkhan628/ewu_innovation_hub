<?php


include "../config/database.php";



if($_SERVER["REQUEST_METHOD"]=="POST"){



    $name = $_POST['name'];

    $email = $_POST['email'];

    $password = $_POST['password'];

    $role = $_POST['role'];

    $department = $_POST['department'];




    // Check existing email


    $checkSql = 
    "SELECT * FROM users WHERE email=?";


    $stmt = $conn->prepare($checkSql);


    $stmt->bind_param("s",$email);


    $stmt->execute();


    $result = $stmt->get_result();




    if($result->num_rows > 0){


        echo "Email already exists";

        exit();


    }





    // Hash Password


    $hashedPassword =
    password_hash($password,PASSWORD_DEFAULT);





    // Insert User


    $sql = 
    "INSERT INTO users
    (name,email,password,role,department)
    VALUES(?,?,?,?,?)";



    $stmt = $conn->prepare($sql);



    $stmt->bind_param(
        "sssss",
        $name,
        $email,
        $hashedPassword,
        $role,
        $department
    );




    if($stmt->execute()){


        header("Location: ../login.php");

        exit();


    }



    else{


        echo "Registration Failed";


    }




}


?>