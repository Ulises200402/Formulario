<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function validarISBN($isbn) {
    return preg_match('/^\d{3}-\d-\d{2,5}-\d{2,7}-\d$/', $isbn) || 
           preg_match('/^\d-\d{2,5}-\d{2,7}-\d$/', $isbn);
}

function validarDNDA($dnda) {
    return preg_match('/^\d{6,7}\/\d{4}$/', $dnda);
}

function validarTelefono($telefono) {
    return preg_match('/^\d+$/', $telefono);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario y sanitizarlos
    $nombre_completo = htmlspecialchars($_POST['nombre_completo']);
    $documento_identidad = htmlspecialchars($_POST['documento_identidad']);
    $sexo = htmlspecialchars($_POST['sexo']);
    $nacionalidad = htmlspecialchars($_POST['nacionalidad']);
    $domicilio = htmlspecialchars($_POST['domicilio']);
    $otros_datos = htmlspecialchars($_POST['otros_datos']);
    $email = htmlspecialchars($_POST['email']);
    $telefono = htmlspecialchars($_POST['telefono']);
    $menciones = htmlspecialchars($_POST['menciones']);
  
    // Información del libro
    $titulo = htmlspecialchars($_POST['titulo']);
    $razon_social = htmlspecialchars($_POST['razon_social']);
    $sello_editorial = htmlspecialchars($_POST['sello_editorial']);
    $cuit = htmlspecialchars($_POST['cuit']);
    $isbn = htmlspecialchars($_POST['isbn']);
    $dnda = htmlspecialchars($_POST['dnda']);
    $genero = htmlspecialchars($_POST['genero']);
    $subgenero = htmlspecialchars($_POST['subgenero']);
    $tipo_obra = htmlspecialchars($_POST['tipo_obra']);
    $edicion = htmlspecialchars($_POST['edicion']);
    $temas = htmlspecialchars($_POST['temas']);
    $idioma = htmlspecialchars($_POST['idioma']);
    $laminado = isset($_POST['laminado']) ? htmlspecialchars($_POST['laminado']) : "No seleccionado";
    $impresion = isset($_POST['impresion']) ? htmlspecialchars($_POST['impresion']) : "No seleccionado";
    $tamaño = isset($_POST['tamaño']) ? htmlspecialchars($_POST['tamaño']) : "No seleccionado";
    $tapas = isset($_POST['tapas']) ? htmlspecialchars($_POST['tapas']) : "No seleccionado";
    $material = isset($_POST['material']) ? htmlspecialchars($_POST['material']) : 'No proporcionado';
    $sintesis = isset($_POST['sintesis']) ? htmlspecialchars($_POST['sintesis']) : 'No proporcionado';
    $paginas = isset($_POST['paginas']) ? htmlspecialchars($_POST['paginas']) : "No proporcionado";
    $archivos_adicionales = isset($_FILES['archivos_adicionales']) ? $_FILES['archivos_adicionales'] : null;
    $portada = isset($_FILES['portada']) ? $_FILES['portada'] : null;
    $archivo_libro = isset($_FILES['archivo_libro']) ? $_FILES['archivo_libro'] : null;
    $acepto_terminos = isset($_POST['terminos']) && $_POST['terminos'] === 'on' ? true : false;

    // Verifica que se acepten los términos
    if (!$acepto_terminos) {
        header("Location: error.html?error=" . urlencode("Debe aceptar los términos y condiciones."));
        exit();
    }

    if (!validarISBN($isbn)) {
        header("Location: error.html?error=" . urlencode("Formato de ISBN inválido."));
        exit();
    }

    if (!validarDNDA($dnda)) {
        header("Location: error.html?error=" . urlencode("Formato de DNDA inválido."));
        exit();
    }

    if (!validarTelefono($telefono)) {
        header("Location: error.html?error=" . urlencode("El teléfono solo debe contener números."));
        exit();
    }

    // Configura PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bohemiaeditorial2025@gmail.com'; // Asegúrate de que tu correo esté correcto
        $mail->Password = 'vhtxavcfkbgzxvgi'; // Usa una contraseña de aplicación de Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('bohemiaeditorial2025@gmail.com', 'Editorial');
        $mail->addAddress('fliulipi234@gmail.com'); // Cambia esto por tu correo
        $mail->addReplyTo($email, $nombre_completo);

        // Contenido del correo
        $mail->isHTML(false);
        $mail->Subject = "Nuevo formulario enviado desde la editorial";
        $mail->Body = "Has recibido un nuevo formulario:\n\n" . 
                      "Información del autor:\n" . 
                      "Nombre completo: " . $nombre_completo . "\n" . 
                      "Documento de identidad: " . $documento_identidad . "\n" .
                      "Sexo: " . $sexo . "\n" . 
                      "Nacionalidad: " . $nacionalidad . "\n" . 
                      "Domicilio: " . $domicilio . "\n" . 
                      "Otros datos: " . $otros_datos . "\n" .
                      "Correo electrónico: " . $email . "\n" . 
                      "Número de Teléfono: " . $telefono . "\n" . 
                      "Menciones: " . $menciones . "\n\n" .
                      "Información del libro:\n" . 
                      "Título: " . $titulo . "\n" .
                      "Razón Social: " . $razon_social . "\n" . 
                      "Sello Editorial: " . $sello_editorial . "\n" . 
                      "CUIT: " . $cuit . "\n" . 
                      "ISBN: " . $isbn . "\n" . 
                      "DNDA: " . $dnda . "\n" . 
                      "Género: " . $genero . "\n" . 
                      "Subgénero: " . $subgenero . "\n" . 
                      "Tipo de Obra: " . $tipo_obra . "\n" . 
                      "Edición: " . $edicion . "\n" . 
                      "Temas: " . $temas . "\n" . 
                      "Idioma: " . $idioma . "\n" . 
                      "Laminado: " . $laminado . "\n" . 
                      "Impresión: " . $impresion . "\n" . 
                      "Tamaño: " . $tamaño . "\n" . 
                      "Tapas: " . $tapas . "\n" . 
                      "Material: " . $material . "\n" . 
                      "Síntesis: " . $sintesis . "\n" . 
                      "Páginas: " . $paginas . "\n" . 
                      "Acepto términos y condiciones: " . ($acepto_terminos ? "Sí" : "No") . "\n";

        // Adjuntar archivos
        foreach (['archivos_adicionales', 'portada', 'archivo_libro'] as $fileKey) {
            if (!empty($_FILES[$fileKey]['tmp_name'])) {
                if (is_array($_FILES[$fileKey]['tmp_name'])) {
                    foreach ($_FILES[$fileKey]['tmp_name'] as $index => $tmp_name) {
                        if (!empty($tmp_name)) {
                            $mail->addAttachment($tmp_name, $_FILES[$fileKey]['name'][$index]);
                        }
                    }
                } else {
                    $mail->addAttachment($_FILES[$fileKey]['tmp_name'], $_FILES[$fileKey]['name']);
                }
            }
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