<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST);
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
        echo "Debe aceptar los términos y condiciones.";
        exit;
    }

    // Configura PHPMailer
    $mail = new PHPMailer(true);

    function subirArchivo($archivo, $carpeta, $nombreArchivo) {
        if (!empty($archivo['name'])) {
            $tmpPath = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nuevoNombre = $nombreArchivo . "_" . uniqid() . "." . $extension; // Nombre único
            $destino = $carpeta . $nuevoNombre;
    
            if (move_uploaded_file($tmpPath, $destino)) {
                return $destino; // Retorna la ruta del archivo guardado
            } else {
                return false;
            }
        }
        return false;
    }

    // Crear carpeta 'uploads/' si no existe
    if (!is_dir("uploads/")) {
        mkdir("uploads/", 0777, true);
    }

    // Subir archivos
    $fotoAutorPath = subirArchivo($_FILES['fotoautor'], "uploads/", "foto_autor");
    $portadaPath = subirArchivo($_FILES['portada'], "uploads/", "portada_libro");
    $archivoLibroPath = subirArchivo($_FILES['archivo_libro'], "uploads/", "libro");

    // Adjuntar archivos si existen
    if ($fotoAutorPath) {
        $mail->addAttachment($fotoAutorPath, "Foto_Autor.jpg");
    }
    if ($portadaPath) {
        $mail->addAttachment($portadaPath, "Portada_Libro.jpg");
    } else {
        echo "Error al subir la portada";
    }
    if ($archivoLibroPath) {
        $mail->addAttachment($archivoLibroPath, "Archivo_Libro.pdf");
    } else {
        echo "Error al subir el archivo del libro";
    }

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'equinoxioform@gmail.com'; // Asegúrate de que tu correo y contraseña estén correctos
        $mail->Password = 'ojumdwxlmtqrgsqs'; // Asegúrate de usar una contraseña de aplicación de Google
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
                      "Informacion del autor:" . "\n" .
                      "Nombre completo del autor: " . $nombre_completo . "\n" . 
                      "Nacionalidad: " . $nacionalidad . "\n" .
                      "Domicilio: " . $domicilio . "\n" . 
                      "Código Postal: " . $codigo_postal . "\n" .
                      "Correo electrónico: " . $email . "\n" . 
                      "\n" .
                      "Informacion del libro:". "\n" . 
                      "\n" .
                      "ISBN: " . $isbn . "\n" . 
                      "DHDA: " . $dhda . "\n" . 
                      "Género Literario: " . $genero . "\n" . 
                      "Sello Editorial: " . $sello_editorial . "\n" . 
                      "Características del título: " . $caracteristicas_titulo . "\n" . 
                      "Subtítulo: " . $subtitulo . "\n" . 
                      "Formato: " . $formato . "\n" . 
                      "Solapa: " . $solapa . "\n".
                      "Tamaño seleccionado: " . $tamaño . "\n";

        // Enviar el correo
        $mail->send();
        echo "¡El formulario se ha enviado correctamente!";
    } catch (Exception $e) {
        echo "Hubo un error al enviar el formulario: {$mail->ErrorInfo}";
    }
} else {
    echo "Acceso no autorizado.";
}
?>