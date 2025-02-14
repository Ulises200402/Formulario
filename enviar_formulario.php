<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera los datos del formulario
    $titulo = htmlspecialchars($_POST['titulo']);
    $autor = htmlspecialchars($_POST['autor']);
    $fecha_nacimiento = htmlspecialchars($_POST['fecha_nacimiento']);
    $email = htmlspecialchars($_POST['email']);
    $biografia = htmlspecialchars($_POST['biografia']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $genero = htmlspecialchars($_POST['genero']);

    // Configura PHPMailer
    $mail = new PHPMailer(true);

    // Función para subir archivos
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
    $archivoLibroPath = subirArchivo($_FILES['archivo_libro'], "uploads/", "archivo_libro"); // Subir el archivo PDF del libro

    // Adjuntar archivos si existen
    if ($fotoAutorPath) {
        $mail->addAttachment($fotoAutorPath, "Foto_Autor.jpg");
    }   
    if ($portadaPath) {
        $mail->addAttachment($portadaPath, "Portada_Libro.jpg");
    }
    if ($archivoLibroPath) { // Adjuntar el archivo PDF si existe
        $mail->addAttachment($archivoLibroPath, "Libro.pdf"); // Ajusta el nombre del archivo según lo que necesites
    }
    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'equinoxioform@gmail.com';
        $mail->Password = 'ojumdwxlmtqrgsqs'; // Asegúrate de colocar tu contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('equinoxioform@gmail.com', 'Editorial');
        $mail->addAddress('fliulipi234@gmail.com');
        $mail->addReplyTo($email, $autor);

        // Contenido del correo
        $mail->isHTML(false);
        $mail->Subject = "Nuevo formulario enviado desde la editorial";
        $mail->Body = "Has recibido un nuevo formulario:\n\n" . 
                      "Título del libro: " . $titulo . "\n" .
                      "Autor(es): " . $autor . "\n" .
                      "Fecha de nacimiento: " . $fecha_nacimiento . "\n" .
                      "Correo electrónico: " . $email . "\n" .
                      "Género Literario: " . $gener . "\n" .
                      "Biografía del autor: " . $biografia . "\n" .
                      "Descripción del libro: " . $descripcion . "\n";

        // Enviar el correo
        $mail->send();
        echo "¡El formulario se ha enviado correctamente!";
        // Redirigir a la página de éxito
        header("Location: exito.html");
        exit();
        } catch (Exception $e) {
            $error_message = urlencode($mail->ErrorInfo); // Codifica el mensaje para la URL
            header("Location: error.html?error=$error_message");
        exit();
    }
        } else {
        echo "Acceso no autorizado.";
    }
?>