<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera los datos del formulario
    $nombre_completo = htmlspecialchars($_POST['nombre_completo']);
    $nacionalidad = htmlspecialchars($_POST['nacionalidad']);
    $domicilio = htmlspecialchars($_POST['domicilio']);
    $codigo_postal = htmlspecialchars($_POST['codigo_postal']);
    $email = htmlspecialchars($_POST['email']);
    $isbn = htmlspecialchars($_POST['isbn']);
    $dhda = htmlspecialchars($_POST['dhda']);
    $genero = htmlspecialchars($_POST['genero']);
    $sello_editorial = htmlspecialchars($_POST['sello_editorial']);
    $caracteristicas_titulo = htmlspecialchars($_POST['caracteristicas_titulo']);
    $subtitulo = htmlspecialchars($_POST['subtitulo']);
    $formato = htmlspecialchars($_POST['formato']);
    $solapa = htmlspecialchars($_POST['solapa']);
    $tamaño = isset($_POST['tamaño']) ? $_POST['tamaño'] : "No seleccionado";
    $acepto_terminos = $_POST['terminos'] === 'on' ? true : false;

    // Verifica que se acepten los términos
    if (!$acepto_terminos) {
        header("Location: error.html?error=" . urlencode("Debe aceptar los términos y condiciones."));
        exit();
    }

    // Configura PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'equinoxioform@gmail.com'; // Asegúrate de que tu correo esté correcto
        $mail->Password = 'ojumdwxlmtqrgsqs'; // Usa una contraseña de aplicación de Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('equinoxioform@gmail.com', 'Editorial');
        $mail->addAddress('fliulipi234@gmail.com'); // Cambia esto por tu correo
        $mail->addReplyTo($email, $nombre_completo);

        // Contenido del correo
        $mail->isHTML(false);
        $mail->Subject = "Nuevo formulario enviado desde la editorial";
        $mail->Body = "Has recibido un nuevo formulario:\n\n" . 
                      "\n" .
                      "Información del autor:\n" .
                      "Nombre completo: " . $nombre_completo . "\n" . 
                      "Nacionalidad: " . $nacionalidad . "\n" .
                      "Domicilio: " . $domicilio . "\n" . 
                      "Código Postal: " . $codigo_postal . "\n" .
                      "Correo electrónico: " . $email . "\n" . 
                      "\n" .
                      "Información del libro:\n" . 
                      "ISBN: " . $isbn . "\n" . 
                      "DHDA: " . $dhda . "\n" . 
                      "Género Literario: " . $genero . "\n" . 
                      "Sello Editorial: " . $sello_editorial . "\n" . 
                      "Características del título: " . $caracteristicas_titulo . "\n" . 
                      "Subtítulo: " . $subtitulo . "\n" . 
                      "Formato: " . $formato . "\n" . 
                      "Solapa: " . $solapa . "\n" .
                      "Tamaño seleccionado: " . $tamaño . "\n";

        // Adjuntar archivos sin guardarlos en el servidor
        if (!empty($_FILES['fotoautor']['tmp_name'])) {
            $mail->addAttachment($_FILES['fotoautor']['tmp_name'], $_FILES['fotoautor']['name']);
        }
        if (!empty($_FILES['portada']['tmp_name'])) {
            $mail->addAttachment($_FILES['portada']['tmp_name'], $_FILES['portada']['name']);
        }
        if (!empty($_FILES['archivo_libro']['tmp_name'])) {
            $mail->addAttachment($_FILES['archivo_libro']['tmp_name'], $_FILES['archivo_libro']['name']);
        }

        // Enviar el correo
        if ($mail->send()) {
            header("Location: exito.html");
            exit();
        } else {
            header("Location: error.html?error=" . urlencode("Error al enviar el correo."));
            exit();
        }
    } catch (Exception $e) {
        header("Location: error.html?error=" . urlencode("Hubo un error: " . $mail->ErrorInfo));
        exit();
    }
}
?>