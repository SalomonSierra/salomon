var hexchr="0123456789abcdef";
//hex1 y hex2 son cadenas hexadecimales devuelve la suma de los dos
function bighexsoma(hex1, hex2){
    if(hex1.length>hex2.length){
        a=hex2;
        hex2=hex1;
        hex1=a;
    }
    while(hex1.length<hex2.length)
        hex1='0'+hex1;
    sobra=0;
    resultado="";
    for(x=hex1.length-1;x>=0;x--){
        if(hex1.charAt(x)>'9') op1=hex1.charCodeAt(x)-hexchr.charCodeAt(10)+10;
        else op1=hex1.charCodeAt(x)-hexchr.charCodeAt(0);
        if(hex2.charAt(x)>'9') op2=hex2.charCodeAt(x)-hexchr.charCodeAt(10)+10;
        else op2=hex2.charCodeAt(x)-hexchr.charCodeAt(0);
        r=op1+op2+sobra;
        if(r>15){
            r-=16;
            sobra=1;
        }else{
            sobra=0;
        }
        resultado=hexchr.charAt(r)+resultado;
    }
    if(sobra==1)
        resultado="1"+resultado;
    return resultado;
}
