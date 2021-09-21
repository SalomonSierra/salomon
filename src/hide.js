var modifyClassName = function (elem, add, string) {
var s = (elem.className) ? elem.className : "";
var a = s.split(" ");//El método split() divide un objeto de tipo String en un array
// (vector) de cadenas mediante la separación de la cadena en subcadenas.
if (add) {
  for (var i=0; i<a.length; i++) {
      if (a[i] == string) {
          return;
      }
  }
  s += " " + string;
}
else {
    s = "";
    for (var i=0; i<a.length; i++) {
        if (a[i] != string)
            s += a[i] + " ";
        }
    }
elem.className = s;
}
function toggleGroup(n) {
    //alert(n);
    var currentClass = document.getElementById("myscoretable");
    for (var i=1; i<999; i++) {
      modifyClassName(currentClass,true,"sitehide"+i);
    }
    modifyClassName(currentClass,false,"sitehide"+n);
}
