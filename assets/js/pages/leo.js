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

function showVeh(str) {
 if (str == "") {
  document.getElementById("showVehInfo").innerHTML = "";
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
    document.getElementById("showVehInfo").innerHTML = this.responseText;
   }
  };
  xmlhttp.open("GET", "functions/leo/api.php?a=searchVeh&q=" + str, true);
  xmlhttp.send();
 }
}

function showName(str) {
 if (str == "") {
  document.getElementById("showPersonInfo").innerHTML = "";
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
    document.getElementById("showPersonInfo").innerHTML = this.responseText;
   }
  };
  xmlhttp.open("GET", "functions/leo/api.php?a=searchName&q=" + str, true);
  xmlhttp.send();
 }
}

function loadNames() {
  var returnHtml = "";
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  } else {
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  returnHtml += '<option selected="true" disabled="disabled">Search Name, Or DOB</option>';
  xmlhttp.onreadystatechange = function() {
   if (this.readyState == 4 && this.status == 200) {
    returnHtml += this.responseText;
    document.getElementById("nameSearch").innerHTML = returnHtml;
    document.getElementById("suspect").innerHTML = returnHtml;
    document.getElementById("suspect_arr").innerHTML = returnHtml;
   }
  };
  xmlhttp.open("GET", "functions/leo/api.php?a=searchNameAc", true);
  xmlhttp.send();
}

function loadVehs() {
  var returnHtml = "";
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  } else {
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  returnHtml += '<option selected="true" disabled="disabled">Search VIN, Plate, Or Model</option>';
  xmlhttp.onreadystatechange = function() {
   if (this.readyState == 4 && this.status == 200) {
    returnHtml += this.responseText;
    document.getElementById("vehicleSearch").innerHTML = returnHtml;
    document.getElementById("vehicle_plate").innerHTML = returnHtml;
   }
  };
  xmlhttp.open("GET", "functions/leo/api.php?a=searchVehicleAc", true);
  xmlhttp.send();
}

function loadWpns() {
  var returnHtml = "";
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  } else {
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  returnHtml += '<option selected="true" disabled="disabled">Search Serial, or Owner Name</option>';
  xmlhttp.onreadystatechange = function() {
   if (this.readyState == 4 && this.status == 200) {
    returnHtml += this.responseText;
    document.getElementById("weaponSearch").innerHTML = returnHtml;
   }
  };
  xmlhttp.open("GET", "functions/leo/api.php?a=searchWeaponAc", true);
  xmlhttp.send();
}

function showWpn(str) {
 if (str == "") {
  document.getElementById("showWpn").innerHTML = "";
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
    document.getElementById("showWpn").innerHTML = this.responseText;
   }
  };
  xmlhttp.open("GET", "functions/leo/api.php?a=searchWpns&q=" + str, true);
  xmlhttp.send();
 }
}


 getTime();
 getBolos();
 get911calls();
 getActiveUnits();
 getStatus();
 getAOP();
 getPendingIds();
 getPriorityStatus();

function getTime(){
  (function worker() {
  $.ajax({
    url: 'functions/leo/api.php?a=dynamicTime',
    success: function(data) {
      $('#getTime').html(data);
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 1000);
    }
  });
})();
}

function getBolos(){
  (function worker() {
  $.ajax({
    url: 'functions/leo/api.php?a=getBolos',
    success: function(data) {
      $('#getBolos').html(data);
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 1000);
    }
  });
})();
}

function get911calls(){
  (function worker() {
  $.ajax({
    url: 'functions/leo/api.php?a=get911calls',
    success: function(data) {
      $('#get911calls').html(data);
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 1000);
    }
  });
})();
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

function getActiveUnits(){
  (function worker() {
  $.ajax({
    url: 'functions/leo/api.php?a=getActiveUnits',
    success: function(data) {
      $('#getActiveUnits').html(data);
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 1000);
    }
  });
})();
}

function getStatus(){
  (function worker() {
  $.ajax({
    url: 'functions/leo/api.php?a=getStatus',
    success: function(data) {
      $('#checkStatus').html(data);
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 1000);
    }
  });
})();
}

function getAOP(){
  (function worker() {
  $.ajax({
    url: 'functions/leo/api.php?a=getAOP',
    success: function(data) {
      $('#checkAOP').html(data);
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 1000);
    }
  });
})();
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

function changeSubDivision(selected) {
   var i = selected.value;
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
   xmlhttp.open("GET", "functions/leo/api.php?a=setSubDivision&sd=" + i, true);
   xmlhttp.send();
}

$(document).ready(function() {
 $("#dismiss").delay(3000).fadeOut("slow");
});

function aopSet(str) {
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
  xmlhttp.open("GET", "functions/leo/api.php?a=setAOP&q=" + str, true);
  xmlhttp.send();
 }
}

function getPendingIds(){
  (function worker() {
  $.ajax({
    url: 'functions/leo/api.php?a=getPendingIds',
    success: function(data) {
      $('#getPendingIds').html(data);
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 1000);
    }
  });
})();
}
