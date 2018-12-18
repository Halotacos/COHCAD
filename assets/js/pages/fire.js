$(document).ready(function() {
 $('.js-example-basic-single').select2({
  theme: "bootstrap4",
  minimumInputLength: 3,
 });
});
$(document).ready(function() {
 $('.js-example-basic-multiple').select2({
  theme: "bootstrap4"
 });
});


$(document).ready(function() {
 getTime();
 get911callsFire();
 getStatus();
 getAOP();
 getPendingIds();
});

function getTime() {
  $(document).ready(function() {
   $.ajaxSetup({
    cache: false
   });
   setInterval(function() {
    $('#getTime').load('functions/leo/api.php?a=dynamicTime');
  }, 1000);
  });
}

function get911callsFire() {
  $(document).ready(function() {
   $.ajaxSetup({
    cache: false
   });
   setInterval(function() {
    $('#get911callsFire').load('functions/leo/api.php?a=get911callsFire');
   }, 3000);
  });
}

function updateNotepad(str) {
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
     xmlhttp.open("GET", "functions/leo/api.php?a=updateNotepad&txt=" + str, true);
     xmlhttp.send();
 }
}

function getStatus() {
  $(document).ready(function() {
   $.ajaxSetup({
    cache: false
   });
   setInterval(function() {
    $('#checkStatus').load('functions/leo/api.php?a=getStatusFire');
   }, 3000);
  });
}

function getAOP() {
  $(document).ready(function() {
   $.ajaxSetup({
    cache: false
   });
   setInterval(function() {
    $('#checkAOP').load('functions/leo/api.php?a=getAOP');
   }, 3000);
  });
}

function setStatus(unit) {
   var i = unit.id;
   if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
   } else {
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
   }
   xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     //hmmmzz
    }
   };
   xmlhttp.open("GET", "functions/leo/api.php?a=setStatus&q=" + i, true);
   xmlhttp.send();
}

function setFireStation(unit) {
   var i = unit.value;
   if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
   } else {
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
   }
   xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     //hmmmzz
    }
   };
   xmlhttp.open("GET", "functions/leo/api.php?a=setFireStation&q=" + i, true);
   xmlhttp.send();
   location.reload();
}

function updateFireStation(unit) {
   var i = unit.value;
   if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
   } else {
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
   }
   xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     //hmmmzz
    }
   };
   xmlhttp.open("GET", "functions/leo/api.php?a=updateFireStation&q=" + i, true);
   xmlhttp.send();
}

$(document).ready(function() {
 $("#dismiss").delay(3000).fadeOut("slow");
});
