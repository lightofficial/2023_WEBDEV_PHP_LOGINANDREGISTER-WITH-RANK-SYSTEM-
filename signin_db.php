<?php
    session_start();
    require_once 'config/db.php';

    if (isset($_POST['signin'])) //ถ้าเจอ NAME ให้ทำต่อ
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email))
        {
            $_SESSION['error'] = 'กรุณากรอกอีเมลล์';
            header("location: signin.php");
        }
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $_SESSION['error'] = 'รูปแบบอีเมลล์ไม่ถูกต้อง';
            header("location: signin.php");
            
        }
        else if (empty($password))
        {
            $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
            header("location: signin.php");
        }
        else if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) // กรอกพาสเวิร์ดไม่เกิน 20 แต่ไม่น้อย กว่า 5
        {
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาสระหว่าง 5 ถึง 20 ตัวอักษร';
            header("location: signin.php");
        }

        else 
        {
            try {
                $check_data = $conn->prepare("SELECT * FROM users WHERE email = :email"); // $conn = php_data_opject | เช็คอีเมลล์ซ้ำกันในระบบ
                $check_data->bindParam(":email", $email); // เทียบค่าตัวแปรที่ฝากไว้ กับ ค่าตัวแปรใน DB 
                $check_data->execute(); // เริ่มกระบวนการทำงาน execute
                $row = $check_data->fetch(PDO::FETCH_ASSOC); // กำหนดให้ FETCH ข้อมูล แบบ PDO::FETCH_ASSOC ดึงข้อมูลจากตารางมาทั้งหมด

                if ($check_data->rowCount() > 0)
                {

                    if($email == $row['email'])
                    {
                        if(password_verify($password, $row['password'])) //เทียบพาสเวิร์ดกับที่กรอกเข้ามา
                        {
                            if($row['urole'] == 'admin')
                            {
                                    $_SESSION['admin_login'] = $row['id'];
                                    header("location: admin.php");
                            }
                            else
                            {
                                $_SESSION['user_login'] = $row['id'];
                                header("location: user.php");
                            }
                        }
                        else
                        {
                            $_SESSION['error'] = 'รหัสผ่านไม่ถูกต้อง';
                            header("location: signin.php");
                        }
                    }
                    else
                    {
                        $_SESSION['error'] = 'ไม่มีข้อมูลในระบบ';
                        header("location: signin.php");
                    }

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