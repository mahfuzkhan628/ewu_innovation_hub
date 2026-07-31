<?php


include "../config/database.php";

session_start();



if($_SERVER["REQUEST_METHOD"] == "POST"){



    $email = $_POST['email'];

    $password = $_POST['password'];



    // Check user by email

    $sql = "SELECT * FROM users WHERE email=?";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param("s",$email);


    $stmt->execute();


    $result = $stmt->get_result();




    if($result->num_rows == 1){



        $user = $result->fetch_assoc();




        // Verify password


        if(password_verify($password,$user['password'])){


            $_SESSION['user_id'] = $user['user_id'];

            $_SESSION['name'] = $user['name'];

            $_SESSION['role'] = $user['role'];




            // Role based redirect


            if($user['role']=="student"){


                header("Location: ../student/dashboard.php");

                exit();


            }



            elseif($user['role']=="faculty"){


                header("Location: ../faculty/dashboard.php");

                exit();


            }



        }


        else{


            echo "Wrong Password";


        }



    }



    else{


        echo "User not found";


    }



}

?>