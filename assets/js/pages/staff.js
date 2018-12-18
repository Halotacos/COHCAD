function setIdentityVerification(str) {
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
     xmlhttp.open("GET", "functions/staff/setIdentityVerification.php?q=" + str, true);
     xmlhttp.send();
     alert("Identity Verification Settings Updated");
 }
}

function setSignUpVerification(str) {
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
     xmlhttp.open("GET", "functions/staff/setSignUpVerification.php?q=" + str, true);
     xmlhttp.send();
     alert("Sign Up Verification Settings Updated");
 }
}

function setTheme(str) {
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
     xmlhttp.open("GET", "functions/staff/setTheme.php?a=theme&q=" + str, true);
     xmlhttp.send();
     alert("CAD/MDT Theme Updated... Your Page Will Refresh Soon");
     location.reload();
 }
}

function setTimezone(str) {
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
     xmlhttp.open("GET", "functions/staff/setTheme.php?a=timezone&q=" + str, true);
     xmlhttp.send();
     alert("CAD/MDT TimeZone Updated... Your Page Will Refresh Soon");
     location.reload();
 }
}

function setBackground(str) {
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
     xmlhttp.open("GET", "functions/staff/setBackgroundColor.php?q=" + str, true);
     xmlhttp.send();
     alert("Background Color Updated... Your Page Will Refresh Soon");
     location.reload();
 }
}

function searchUsers() {
 // Declare variables
 var input, filter, table, tr, td, i;
 input = document.getElementById("userSearch");
 filter = input.value.toUpperCase();
 table = document.getElementById("users");
 tr = table.getElementsByTagName("tr");

 // Loop through all table rows, and hide those who don't match the search query
 for (i = 0; i < tr.length; i++) {
     td = tr[i].getElementsByTagName("td")[1];
     tdt = tr[i].getElementsByTagName("td")[2];
     if (td) {
         if (td.innerHTML.toUpperCase().indexOf(filter) > -1 || tdt.innerHTML.toUpperCase().indexOf(filter) > -1) {
             tr[i].style.display = "";
         } else {
             tr[i].style.display = "none";
         }
     }
 }
}

function searchIdentities() {
 // Declare variables
 var input, filter, table, tr, td, i;
 input = document.getElementById("identitySearch");
 filter = input.value.toUpperCase();
 table = document.getElementById("identities");
 tr = table.getElementsByTagName("tr");

 // Loop through all table rows, and hide those who don't match the search query
 for (i = 0; i < tr.length; i++) {
     td = tr[i].getElementsByTagName("td")[0];
     if (td) {
         if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
             tr[i].style.display = "";
         } else {
             tr[i].style.display = "none";
         }
     }
 }
}

function searchCharacters() {
 // Declare variables
 var input, filter, table, tr, td, i;
 input = document.getElementById("characterSearch");
 filter = input.value.toUpperCase();
 table = document.getElementById("characters");
 tr = table.getElementsByTagName("tr");

 // Loop through all table rows, and hide those who don't match the search query
 for (i = 0; i < tr.length; i++) {
     td = tr[i].getElementsByTagName("td")[1];
     if (td) {
         if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
             tr[i].style.display = "";
         } else {
             tr[i].style.display = "none";
         }
     }
 }
}

$(document).ready(function() {
 $("#dismiss").delay(3000).fadeOut("slow");
});
