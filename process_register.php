<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = htmlspecialchars($_POST['nama']);
    $email    = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Email sudah terdaftar!";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $email, $password);

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dhyanad1315@gmail.com';
            $mail->Password   = 'password___';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('emailkamu@gmail.com', 'Sistem Registrasi');
            $mail->addAddress($email, $nama);

            $mail->isHTML(true);
            $mail->Subject = 'Konfirmasi Pendaftaran';
            $mail->Body    = "
                <h3>Halo, $nama!</h3>
                <p>Terima kasih telah melakukan registrasi.</p>
                <p>Akun kamu berhasil didaftarkan menggunakan email: <b>$email</b></p>
            ";

            $mail->send();

            header("Location: success.php");
            exit;
        } catch (Exception $e) {
            echo "Data tersimpan, tetapi email gagal dikirim. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Registrasi gagal disimpan ke database.";
    }
}
?>
