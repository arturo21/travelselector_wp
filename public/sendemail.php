<?php
	// ...
	$email=get_option('myemailtravselector');
	if($email!=''){
		$title   = 'Solicitud de Compra de Boletos';
		$content = '<strong>Solicitud de Boletería</strong>';
		$desde=$_POST['desdeinput'];
		$hasta=$_POST['haciainput'];
		$ida=$_POST['salidainput'];
		$vuelta=$_POST['vueltainput'];
		$ninos=$_POST['ninosinput'];
		$adultos=$_POST['adultosinput'];
		$nombre=$_POST['nombreinput'];
		$telefono=$_POST['telefinput'];
		$emaildest=$_POST['emaildest'];
		$content.="Datos de la Solicitud";
		$content.="Desde: ".$desde;
		$content.="Hasta: ".$hasta;
		$content.="Salida: ".$ida;
		$content.="Vuelta: ".$vuelta;
		$content.="Niños: ".$ninos;
		$content.="Adultos: ".$adultos;
		$content.="Nombre: ".$nombre;
		$content.="Teléfono: ".$telefono;
		$content.="Email: ".$emaildest;
		wp_mail( $email, $title, $content );

		echo($content);
	}
?>

