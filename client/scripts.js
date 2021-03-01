/**
 * Load locally stored items.
 */
class Query {
  constructor(label,savannahURL,apiURL) {
    // If called Query() read values from GUI.
    if ((typeof label === 'undefined')
        && (typeof savannahURL === 'undefined')
        && (typeof apiURL === 'undefined')) {
      this.label = queryLabel.value;
      this.url   = querySavannah.value;
      this.api   = queryInput.value.trim().replaceAll(/\s+/g, "&");
    } else {
      this.label = label;
      this.url   = savannahURL;
      this.api   = apiURL.trim().replaceAll(/\s+/g, "&");
    }
  }
  getPermaLink() {
    return storage.getAPIURL() + '?' + this.api;
  }
  getPermaLinkParams() {
    return this.api;
  }
  getDisplayParams() {
    return this.api.replaceAll("&", "    ");
  }
}


class Storage {
  constructor() {
    this.apiURL       = localStorage.getItem("apiURL");
    this.defaultQuery = new Query('', '', localStorage.getItem("defaultQuery"));
    this.savedQueries = JSON.parse(localStorage.getItem("savedQueries"));
  }
  getAPIURL () {
    return this.apiURL;
  }
  getDefaultQuery () {
    return this.defaultQuery;
  }
  getQueries () {
    return this.savedQueries;
  }
  save () {
    localStorage.setItem("savedQueries", JSON.stringify(savedQueries));
  }
}
var storage = new Storage();


/**
 * Elements always available.
 */
var queryInput;
var queryLabel;
var querySavannah;
var queryUpdateButton;
var loadingIndicator;
var result;
var resultCount;


function init() {
  queryInput        = document.getElementById("queryInput");
  queryLabel        = document.getElementById("queryLabel");
  querySavannah     = document.getElementById("querySavannah");
  queryUpdateButton = document.getElementById("queryUpdateButton");
  loadingIndicator  = document.getElementById("loading");
  result            = document.getElementById("result");
  resultCount       = document.getElementById("resultCount");
  queryInputInitialize();
}


function countResults(nodeToCount, nodeResult) {
  if (nodeToCount.firstChild.tagName == "TABLE") {
    // ignore head line
    nodeResult.innerHTML = nodeToCount.getElementsByTagName('tr').length - 1;
  } else {
    try {
      nodeResult.innerHTML = JSON.parse(nodeToCount.innerHTML).length;
    } catch (e) {
      // Must be CSV and count rows.  Ignore head line and last newline.
      nodeResult.innerHTML = nodeToCount.innerHTML.split("\n").length - 2;
    }
  }
}


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
  const div   = document.createElement("div");
  const span1 = document.createElement("span");
        span1.appendChild(document.createTextNode("×"));
        span1.setAttribute("class", "closebtn");
        span1.addEventListener("click", function(){
          div.style.opacity = "0";
          setTimeout(function(){ div.remove(); }, 600);
        });
  const span2 = document.createElement("span");
        span2.appendChild(document.createTextNode(symbol));
        span2.setAttribute("class", "closebtn");
  div.setAttribute("class", "alert " + type);
  div.appendChild(span1);
  div.appendChild(span2);
  div.appendChild(document.createTextNode(message));
  result.parentNode.insertBefore(div, result);
  setTimeout(function(){ div.remove(); }, 10000);  // 10 seconds
}


function apiUpdateItem(node, tracker, id) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == XMLHttpRequest.DONE) {
      apiRequestResultHandle(this);
    }};
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


function queryInputSend() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == XMLHttpRequest.DONE) {
      loadingIndicator.style.visibility = "hidden";
      queryUpdateButton.disabled = false;
      apiRequestResultHandle(this, result);
      countResults(result, resultCount);
    }};
  xhttp.open("GET", "api.php?" + new Query().getPermaLinkParams(), true);
  xhttp.send();
  loadingIndicator.style.visibility = "visible";
  queryUpdateButton.disabled = true;
}


function queryInputInitialize(value) {
  if (!value) {
    var getParams = document.location.search.substring(1);
    value = (getParams ? new Query('', '', getParams)
                       : storage.getDefaultQuery());
  } else {
    value = new Query('', '', value);
  }
  queryInput.value = value.getDisplayParams();
  queryInput.addEventListener("keyup", function(event) {
      queryInputAdjust(this);
    });
  queryInputAdjust(queryInput);
  queryInputSend();
}


function queryInputAdjust(element) {
  element.style.height = "1px";
  element.style.height = element.scrollHeight + "px";
}


