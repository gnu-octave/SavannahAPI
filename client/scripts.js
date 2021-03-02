/**
 * Global variables.
 */
var queryList;
var queryForm;

/**
 * Load locally stored items.
 */
class Query {
  constructor(label, savannahURL, apiParams, result) {
    this.label  = label;
    this.url    = savannahURL;
    this.api    = apiParams.trim().replaceAll(/\s+/g, "&");
    this.setResultHTML(result);
  }
  static getDefault() {
    var value = document.location.search.substring(1);
    value = (value ? value : localStorage.getItem("defaultQuery"));
    return new Query('', '', value, '');
  }
  static getFromGETParams() {
    return new Query('', '', localStorage.getItem("defaultQuery"), '');;
  }
  getLabel() {
    return this.label;
  }
  getSavannahURL() {
    return this.url;
  }
  getPermaLink() {
    return localStorage.getItem("apiURL") + '?' + this.api;
  }
  getPermaLinkParams() {
    return this.api;
  }
  setResultHTML(str) {
    this.result = str;
    this.resultCount = this.__getResultCount();
  }
  getResultHTML() {
    return this.result;
  }
  getResultCount() {
    return this.resultCount;
  }
  __getResultCount() {
    if (!this.result) {
      return 0;
    }
    if (this.result.substr(0, 6) === "<table") {
      // ignore head line
      return (this.result.match(/<tr>/g) || []).length - 1;
    } else {
      try {
        return JSON.parse(this.result).length;
      } catch (e) {
        // Must be CSV and count rows.  Ignore head line and last newline.
        return this.result.split("\n").length - 2;
      }
    }
  }
}


class QueryList {
  constructor() {
    this.savedQueries = JSON.parse(localStorage.getItem("savedQueries"));
  }
  getQueries () {
    return this.savedQueries;
  }
  save () {
    localStorage.setItem("savedQueries", JSON.stringify(savedQueries));
  }
}


class QueryForm {
  constructor() {
    this.label        = document.getElementById("queryLabel");
    this.savannah     = document.getElementById("querySavannah");
    this.parameter    = document.getElementById("queryParameter");
    this.result       = document.getElementById("queryResult");
    this.resultCount  = document.getElementById("queryResultCount");
    this.updateButton = document.getElementById("queryUpdateButton");
    this.loading      = document.getElementById("loading");

    this.parameter.addEventListener("keyup", function(event) {
        this.style.height = "1px";
        this.style.height = this.scrollHeight + "px";
      });
    this.updateButton.addEventListener("click", function(event) {
        queryForm.send();
      });

    this.setQuery(Query.getDefault());
    this.send();
  }

  setQuery(query) {
    this.setValues(query.getLabel(),
                   query.getSavannahURL(),
                   query.getPermaLinkParams().replaceAll("&", "    "),
                   query.getResultHTML(),
                   query.getResultCount());
  }


  getQuery() {
    return new Query(this.label.innerHTML,
                     this.savannah.innerHTML,
                     this.parameter.innerHTML,
                     this.result.innerHTML);
  }

  setValues(label, url, apiParams, result, count) {
    this.label.innerHTML       = label;
    this.savannah.innerHTML    = url;
    this.parameter.innerHTML   = apiParams;
    this.result.innerHTML      = result;
    this.resultCount.innerHTML = count;
  }

  markBusy() {
    this.loading.style.visibility = "visible";
    this.updateButton.disabled = true;
  }

  markFree() {
    this.loading.style.visibility = "hidden";
    this.updateButton.disabled = false;
  }

  send() {
    var query = this.getQuery();
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == XMLHttpRequest.DONE) {
        queryForm.markFree();
        apiRequestResultHandle(this, query);
      }};
    xhttp.open("GET", "api.php?" + query.getPermaLinkParams(), true);
    xhttp.send();
    this.markBusy();
  }
}


function init() {
  queryList = new QueryList();
  queryForm = new QueryForm();
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
  queryResult.parentNode.insertBefore(div, queryResult);
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


function apiRequestResultHandle(request, query) {
  if (request.status == 200) {
    try {
      var answer = JSON.parse(request.responseText);
    } catch (e) {
      // ignore
    }
    if (answer && answer.state) {
      createPopup(answer.state, answer.message);
    } else if (query) {
      query.setResultHTML(request.responseText);
      queryForm.setQuery(query);
    } else {
      createPopup("error", request.responseText);
    }
  } else {
    createPopup("error", "Request failed: " + request.responseText);
  }
}
