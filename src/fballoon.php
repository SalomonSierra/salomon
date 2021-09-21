<?php
//me  envio la ruta,false, color rgb, ruta./balloos/.md5.pnp
//guarda el imagen con color dado en png
function balloonpng($dir,$get_s,$get_color=null,$get_file=null) {
    //imagecreatefrompng — Crea una nueva imagen a partir de un fichero o de una URL
	if($get_s)
		$smile=imagecreatefrompng($dir . "/images/smallballoontransp.png");
	else
		$smile=imagecreatefrompng($dir . "/images/bigballoontransp.png");
	//imagesavealpha — Establecer la bandera para guardar la información completa
    // del canal alfa (como oposición a la transparencia de un simple color) cuando se guardan imágenes PNG
	imageSaveAlpha($smile, true);
	if($get_color != null) {
        //convierte de hex a des
		$r = hexdec( substr($get_color, 0, 2) );
		$g = hexdec( substr($get_color, 2, 2) );
		$b = hexdec( substr($get_color, 4, 2) );
		//imagecolorallocate — Asigna un color para una imagen fondo
		$kek=imagecolorallocate($smile,$r,$g,$b);
        //imagefill Lleva a cabo un relleno comenzando en la coordenada dada (superior izquierda
        // es 0, 0), con el color dado, en image.
        if($get_s)
			imagefill($smile,5,5,$kek);
		else
			imagefill($smile,12,25,$kek);
	}
    //imagepng — Imprimir una imagen PNG al navegador o a un archivo
	if($get_file != null)
		imagepng($smile,$get_file);//
	else
		imagepng($smile);
}

//me envia un color en RGB
//retorna un globo creado con el color dado.
function balloonurl($color) {

	$locr=$_SESSION['locr'];
	$loc=$_SESSION['loc'];
	$ds = DIRECTORY_SEPARATOR;
	if($ds=="") $ds = "/";
    //md5 Devuelve el hash como un número hexadecimal de 32 caracteres.
	if(!is_readable($locr . $ds . 'balloons' . $ds . md5($color) . '.png')) {
		if($color<0 || $color=='')
			@copy($locr. $ds . 'images' . $ds . 'bigballoonboca1.png', $locr . $ds . 'balloons' . $ds . md5($color) . '.png');
		else//envio la ruta,false, color rgb, ruta./balloos/.md5.pnp//guarda el imagen con color dado en png
			balloonpng($locr,false,$color,$locr . $ds . 'balloons' . $ds . md5($color) . '.png');
		if(!is_readable($locr . $ds . 'balloons' . $ds . md5($color) . '.png')) {
			return $loc . "/images/bigballoontransp.png";
//			return $loc . "/balloon.php?color=" . $color;
		}
	}
	return $loc . "/balloons/" . md5($color) . '.png';
}
?>
