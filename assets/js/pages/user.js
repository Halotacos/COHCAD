function setUserTheme(str) {
 if (str == "") {
     return;
 } else {
     if (window.XMLHttpRequest) {
         // code for IE7+, Firefox, Chrome, Opera, Safari
         xmlhttp = new XMLHttpRequest();
     } else {
         // code for IE6, IE5
         xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
     }
     xmlhttp.onreadystatechange = function() {
         if (this.readyState == 4 && this.status == 200) {
             //hmmm
         }
     };
     xmlhttp.open("GET", "functions/staff/setTheme.php?a=usertheme&q=" + str, true);
     xmlhttp.send();
     alert("CAD/MDT Theme Updated... Your Page Will Refresh Soon");
     location.reload();
 }
}
