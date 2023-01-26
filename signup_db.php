<?php

    session_start();
    require_once 'config/db.php';

    if (isset($_POST['signup'])) //ถ้าเจอ NAME ให้ทำต่อ
    {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $c_password = $_POST['c_password'];
        $urole = 'user';

        if(empty($firstname))
        {
            $_SESSION['error'] = 'กรุณากรอกชื่อ';
            header("location: index.php"); //re-direction ย้อนกลับไปที่หน้าต้องการ ต้องกำหนด location 
        }
        else if (empty($lastname))
        {
            $_SESSION['error'] = 'กรุณากรอกนามสกุล';
            header("location: index.php");
        }
        else if (empty($email))
        {
            $_SESSION['error'] = 'กรุณากรอกอีเมลล์';
            header("location: index.php");
        }
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $_SESSION['error'] = 'รูปแบบอีเมลล์ไม่ถูกต้อง';
            header("location: index.php");
            
        }
        else if (empty($password))
        {
            $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
            header("location: index.php");
        }
        else if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) // กรอกพาสเวิร์ดไม่เกิน 20 แต่ไม่น้อย กว่า 5
        {
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาสระหว่าง 5 ถึง 20 ตัวอักษร';
            header("location: index.php");
        }
        else if (empty($c_password))
        {
            $_SESSION['error'] = 'กรุณายืนยันรหัสผ่าน';
            header("location: index.php");
        }
        else if ($password != $c_password)
        {
            $_SESSION['error'] = 'รหัสผ่านไม่ตรงกัน';
            header("location: index.php");
        }
        else 
        {
            try {
                $check_email = $conn->prepare("SELECT email FROM users WHERE email = :email"); // $conn = php_data_opject | เช็คอีเมลล์ซ้ำกันในระบบ
                $check_email->bindParam(":email", $email); // เทียบค่าตัวแปรที่ฝากไว้ กับ ค่าตัวแปรใน DB 
                $check_email->execute();
                $row = $check_email->fetch(PDO::FETCH_ASSOC); // กำหนดให้ FETCH ข้อมูล แบบ PDO::FETCH_ASSOC

                if ($row['email'] == $email)
                {
                    $_SESSION['warning'] = "มีอีเมลล์นี้อยู่แล้ว <a href='signin.php'>คลิกที่นี้</a> เพื่อเข้าสู่ระบบ";
                    header("location: index.php");

                }
                else if (!isset($_SESSION['error']))
                {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT); //เข้ารหัส Hash
                    $stmt = $conn->prepare("INSERT INTO users(firstname, lastname, email, password, urole)
                                            VALUES(:firstname, :lastname, :email, :password, :urole)");
                    $stmt->bindParam(':firstname', $firstname);
                    $stmt->bindParam(':lastname', $lastname);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $passwordHash);
                    $stmt->bindParam(':urole', $urole);
                    $stmt->execute(); // execute ให้ประมวลผลคำสั่ง และ เพิ่มข้อมูลลงฐานข้อมูล
                    $_SESSION['success'] = 'สมัครสมาชิกเรียบร้อยแล้ว! <a href="signin.php" class="alert-link">คลิกที่นี้</a> เพื่อเข้าสู่ระบบ';
                    header("location: index.php");
                }
                else
                {
                    $_SESSION['error'] = "มีบางอย่างผิดพลาด";
                    header("location: index.php");
                }

            } catch(PDOException $e) {
                echo $e->getMessage();

            }
        }
    }

?>