/**
 * Common used functions
 */

function createPopup(type, message) {
  switch(type) {
  case "warning":
    symbol = "⚠️";
    break;
  case "error":
    symbol = "❌";
    break;
  case "success":
    symbol = "✅";
    break;
  default:
    symbol = "ℹ️";
    type = "info";
  }
  const div = document.createElement("div");
  const span1 = document.createElement("span");
  const span2 = document.createElement("span");
  const content = document.createTextNode(message);
  const span1Content = document.createTextNode("×");
  const span2Content = document.createTextNode(symbol);
  span1.setAttribute("class", "closebtn");
  span2.setAttribute("class", "closebtn");
  span1.addEventListener("click", function(){
    div.style.opacity = "0";
    setTimeout(function(){ div.remove(); }, 600);
  });
  span1.appendChild(span1Content);
  span2.appendChild(span2Content);
  div.setAttribute("class", "alert " + type);
  div.appendChild(span1);
  div.appendChild(span2);
  div.appendChild(content);
  var h2 = document.getElementsByTagName("h2")[0];
  h2.parentNode.insertBefore(div, h2);
  setTimeout(function(){ div.remove(); }, 10000);  // 10 seconds
}


function apiUpdateItem(node, tracker, id) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() { apiRequestResultHandle(this); };
  xhttp.open("GET", "api.php?Action=update&TrackerID=" + tracker
                    + "&ItemID=" + id, true);
  xhttp.send();
  node.parentNode.innerHTML = '❓';
}


function apiRequestResultHandle(request, node) {
  if (request.status == 200) {
    try {
      var answer = JSON.parse(request.responseText);
    } catch (e) {
      // ignore
    }
    if (answer && answer.state) {
      createPopup(answer.state, answer.message);
    } else if (node) {
      node.innerHTML = request.responseText;
    } else {
      createPopup("error", request.responseText);
    }
  } else {
    createPopup("error", "Request failed: " + request.responseText);
  }
}


/**
 * index.php - Release Burn Down Chart
 */

function openAll () {
  var details = document.getElementsByTagName("details");
  for (var i = 0; i < details.length; i++) {
    details[i].setAttribute("open", "true");
  }
}


function closeAll () {
  var details = document.getElementsByTagName("details");
  for (var i = 0; i < details.length; i++) {
    details[i].removeAttribute("open");
  }
}


/**
 * editor.html
 */

function apiRequestFill() {
  // Read GET parameter or insert default value.
  var value = "Action=get&Format=HTMLCSS&OpenClosed=open"
            + "&OrderBy=TrackerID,!ItemID&Limit=25";
  if (document.location.search.substring(1)) {
    value = document.location.search.substring(1);
  }
  request = document.getElementById("request");
  request.value = value;

  // Add event listener.
  request.addEventListener("keyup", function(event) {
      if (event.key === "Enter") { apiRequestSend(); }
    });

  // Perform the request.
  apiRequestSend();
}


function apiRequestSend() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == XMLHttpRequest.DONE) {
      document.getElementById("loading").style.visibility = "hidden";
      document.getElementById("requestButton").disabled = false;
      apiRequestResultHandle(this, document.getElementById("result"));
    }};
  var request = document.getElementById("request").value;
  xhttp.open("GET", "api.php?" + request, true);
  xhttp.send();
  document.getElementById("loading").style.visibility = "visible";
  document.getElementById("requestButton").disabled = true;

}
