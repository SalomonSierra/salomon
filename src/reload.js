
var msg = 0;
var rld  = 0;
var selecionando = 0;
var seg = 120;
//para recargarse la pagina 2min
function recarregar() {
	//esto hace para que no entre en profundidad
	if (rld) {
		clearTimeout(rld);
        rld  = 0;
    }
    if ((document.form1 && document.form1.message && document.form1.message.value) ||
        (document.form1 && document.form1.sourcefile && document.form1.sourcefile.value) ||
        (document.form1 && document.form1.answer && document.form1.answer.value) ||
        (document.form2 && document.form2.answer && document.form2.answer.value) ||
        (document.form2 && document.form2.message && document.form2.message.value) ||
        (document.form2 && document.form2.sourcefile && document.form2.sourcefile.value) ||
        selecionando == 1) {

        if (msg<5) {
			msg++;//es mejor a 5 no hace damas que incrementar
		}
		else {
			// Esta página intentó recargarse, pero parece que está llenando el formulario. Para actualizar,
			// haga clic en el botón Recargar en su \ navegador. Este mensaje no se volverá a mostrar.
			alert("Esta página intentó recargarse, pero parece que está completando el formulario.\n Para actualizar, haga clic en el botón Recargar en su navegador. Este mensaje no se volverá a mostrar.");
        }
    } else
        document.location.reload();
	//120 segundos o 2 minutos
    rld = setTimeout("recarregar()", seg * 1000);
}
//para comenzar
function Comecar() {
	//120 segundos o 2 minutos
	rld = setTimeout("recarregar()", seg * 1000);
}
//para parar
function Parar() {
	if (rld) {
		clearTimeout(rld);//para la repiticion de setTimeout
		rld  = 0;//var a cero
	}
}
//cuando llama selecionando =1
function Arquivo() {
	selecionando = 1;
}
