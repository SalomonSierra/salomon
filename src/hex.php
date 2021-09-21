<?php
//funcion que retorna el pequeño sha1
function myshorthash($k){
    return hash('sha1',$k);
}
//esta funcion retorna un hash de sha256 normal
function myhash($k){
    return hash('sha256',$k);
}
//CONTENIDO , FfHf7nMddw33E9fEzcWw, 2
//encripta el dato con key
function encryptData($text,$key,$compress=true) {

	if(!function_exists('openssl_cipher_iv_length')) {
		//Error de cifrado: php openssl no instalado: comuníquese con un administrador
		MSGError("Encryption error -- php openssl not installed -- contact an admin (" . getFunctionName() .")");
		LogError("Encryption error -- php openssl not installed -- contact an admin (" . getFunctionName() .")");
		return "";
	}

	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	$key = myhash($key . "123456789012345678901234567890"); // . myhash($key);
	$grade='##';
	if($compress) {
		$text = zipstr($text);//devulve la cadena comprimida o false
		$grade = '@#';
	}
	$crypttext = openssl_encrypt($text . myshorthash($text) . $grade, 'aes-256-cbc', substr(pack("H*", $key),0,32), OPENSSL_RAW_DATA, $iv);
	return base64_encode($crypttext . $iv);
}
//funcion para descriptar datos
function decryptData($crypttext,$key,$txt='') {

	if(!function_exists('openssl_cipher_iv_length')) {
        //Error de cifrado: php openssl no instalado: comuníquese con un administrador
		MSGError("Encryption error -- php openssl not installed -- contact an admin (" . getFunctionName() .")");
		LogError("Encryption error -- php openssl not installed -- contact an admin (" . getFunctionName() .")");
		return "";
	}
    //base64_decode Decodifica los datos en data codificados en base64.
	$crypttext = base64_decode($crypttext);
    //Devuelve la longitud de cipher en caso de éxito, o false en caso de error.
	$iv_size = openssl_cipher_iv_length('aes-256-cbc');
	$iv = openssl_random_pseudo_bytes($iv_size);
	$test1='';
	$test2='x';
	$clen = strlen($crypttext);
	if($clen > $iv_size) {
		$iv = substr($crypttext, $clen-$iv_size, $iv_size);
		$crypttext = substr($crypttext, 0, $clen-$iv_size);
		$key = myhash($key . "123456789012345678901234567890"); // . myhash($key);

		$decrypttext = openssl_decrypt($crypttext, 'aes-256-cbc', substr(pack("H*", $key),0,32), OPENSSL_RAW_DATA, $iv);
		$pos = strrpos($decrypttext,"#");
		$iscompressed=false;
		if(substr($decrypttext,$pos-1,1)=='@') $iscompressed=true;
		$ll=strlen(myshorthash("x"));
		$test2 = substr($decrypttext,$pos-1-$ll, $ll);
		$decrypttext = substr($decrypttext,0,$pos-1-$ll);
		$test1 = myshorthash($decrypttext);
	}
	if($test1 != $test2) {
		if($txt=='')
			MSGError("Decryption error -- contact an admin now (" . getFunctionName() .")");
		else
		    LogError("Decryption error (" . getFunctionName() .",$txt)");
		return "";
	}
    ////Está función unzipstr descomprime una cadena comprimida
	if($iscompressed) return unzipstr($decrypttext);
	return $decrypttext;
}


//si son iguales retorna 0 si no retorna sub en resto de dos str.
//pasa asdf salomon
function bighexsub($hex1, $hex2){
    //asdf : fdsa
    $h1= strlen($hex1);
    $h2= strlen($hex2);
    while($h1 < $h2){
        $hex1 ='0'.$hex1;
        $h1++;
    }
    while($h2<$h1){
        $hex2='0'.$hex2;
        $h2++;
    }
    $i=0;
    while($hex1[$i] == $hex2[$i] && $i<$h1) $i++;
    if($i>=$h1) return '0';//si son iguales

    if($hex1[$i]>$hex2[$i]){//si hex1 char pos i es mayor afabeticamente no hacer intercambio
        $sinal='';
    }else{
        $sinal='-';
        $a=$hex2;
        $hex2=$hex1;
        $hex1=$a;//intercambio
    }
    $sobra=0;
    $resultado='';
    for($x=$h1-1;$x>=0;$x--){
        $op1=(int)hexdec(substr($hex1,$x,1));//convierte un valor hexadecimal a decimal, cuaquier valor que no sea hexadecimal lo ignora
        $op2=(int)hexdec(substr($hex2,$x,1));//convierte un valor hexadecimal a decimal, cuaquier valor que no sea hexadecimal lo ignora

        $r=$op1-$op2-$sobra;
        if($r<0){
            $r+=16;
            $sobra=1;
        } else $sobra=0;//dechex decimal a hexadecimal
        if($x>0 || dechex($r) != '0')
            $resultado = dechex($r).$resultado;//se forma normal
    }
    return $sinal.$resultado;
}
//fin
?>
