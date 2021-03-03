/**
 * Global variables.
 */
var queryList;
var queryEditWidget;


$(document).ready(function(){
  var queries = document.getElementById("queries");
  queryList = new QueryWidgetList();
  var defaultQueries = JSON.parse(localStorage.getItem("defaultQueries"));
  defaultQueries.forEach(e =>
    new QueryWidget(queries, new Query(e.label, e.url, e.api, ''))
    );
  new QueryWidget(queries, Query.getDefault());
  });


/**
 * Load locally stored items.
 */
class Query {
  constructor(label, url, apiParams, result) {
    this.label  = label;
    this.url    = url;
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
  getURL() {
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


class QueryWidgetList {
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


class QueryWidget {
  constructor(parentNode, query) {
    this.parentNode = parentNode;
    this.query = query;

    // Widgets that need access.
    this.updateButton = null;
    this.saveButton = null;
    this.copyButton = null;
    this.parameter = null;
    this.loading = null;
    this.label = null;
    this.url = null;

    this.node = this.getNode();
    this.parentNode.appendChild(this.node);
    this.send({silent: true});
  }

  getNode() {
    const self = this;
    var query = this.query;
    var params = query.getPermaLinkParams().replaceAll("&", "\n");
    var element = document.createElement(null);
    element.innerHTML = `
    <details class="m-2 p-1">
      <summary class="container-fluid">
        <span class="badge badge-secondary badge-pill">
          ${query.getResultCount()}
        </span>
        <input type="text" value="${query.getLabel()}">
        <button type="button">
          <span class="spinner-border spinner-border-sm d-none"
                role="status" aria-hidden="true"></span>
          Update
        </button>
        <button type="button">Save changes</button>
        <button type="button">Copy permalink</button>
      </summary>
      <div class="my-3"></div>
      <div class="form-group input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">url</div>
        </div>
        <input type="url" value=${query.getURL()} class="form-control">
      </div>
      <div class="form-group input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">API parameter</div>
        </div>
        <textarea class="form-control form-control-lg">${params}</textarea>
      </div>

      <div class="overflow-auto">
        ${query.getResultHTML()}
      </div>
    </details>
    `;
    element = element.firstElementChild;
    var buttons = element.getElementsByTagName("button");  // order given above
    this.updateButton = buttons[0];
    this.saveButton   = buttons[1];
    this.copyButton   = buttons[2];
    var inputs = element.getElementsByTagName("input");
    this.label = inputs[0];
    this.url   = inputs[1];
    this.parameter = element.getElementsByTagName("textarea")[0];
    this.loading = element.getElementsByTagName("img")[0];

    this.updateButton.addEventListener("click", function(event) {
        self.send();
      });
    this.copyButton.addEventListener("click", function(event) {
        var temp = $("<input>");
        $("body").append(temp);
        temp.val(self.getChangesAsQuery().getPermaLink()).select();
        document.execCommand("copy");
        temp.remove();
      });
    var adjustHeight = function(event) {
      this.style.height = "1px";
      this.style.height = this.scrollHeight + "px";
      };

    this.parameter.addEventListener("keyup",  adjustHeight);
    this.parameter.addEventListener("change", adjustHeight);

    return element;
  }

  repaint() {
    var old = this.node;
    this.node = this.getNode();
    this.node.open = old.open;
    this.parentNode.replaceChild(this.node, old);
  }

  setResultHTML(result) {
    this.query.setResultHTML(result);
    this.repaint();
  }

  getChangesAsQuery() {
    return new Query(this.label.value, this.url.value,
                     this.parameter.value, '');
  }

  markBusy() {
    this.updateButton.disabled = true;
    var span = this.updateButton.getElementsByTagName("span")[0];
    span.classList.toggle("d-none");
    span.classList.toggle("d-inline-block");
  }

  markFree() {
    this.updateButton.disabled = false;
    var span = this.updateButton.getElementsByTagName("span")[0];
    span.classList.toggle("d-none");
    span.classList.toggle("d-inline-block");
  }

  send(options) {
    const self = this;
    var query = this.getChangesAsQuery();
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == XMLHttpRequest.DONE) {
        self.markFree();
        apiRequestResultHandle(this, self, options);
      }};
    xhttp.open("GET", "api.php?" + query.getPermaLinkParams(), true);
    xhttp.send();
    self.markBusy();
  }
}


function showPopup(type, message) {
  var delay = 10000;  // milliseconds
  var headText = "Info";
  switch(type) {
    case "warning":
      headText = "⚠️ Warning";
      break;
    case "error":
      headText = "❌ Error";
      delay *= 10;
      break;
    case "success":
      headText = "✅ Success";
      break;
    default:
      type = "info";
  }
  var element = document.createElement(null);
  element.innerHTML = `
  <div role="alert" aria-life="assertive" aria-atomic="true"
       class="toast md-toast-${type}">
    <div class="toast-header md-toast-${type}">
      <strong class="mr-auto">${headText}</strong>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast"
              aria-label="Close">
        <span aria-hidden="true">×</span>
      </button>
    </div>
    <div class="toast-body md-toast-${type}">
      ${message}
    </div>
  </div>
  `;
  element = element.firstElementChild;
  document.getElementById("toasts").appendChild(element);
  $(element).toast({delay: delay});
  $(element).toast('show');
  setTimeout(function(){ element.remove(); }, delay);
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


function apiRequestResultHandle(request, queryForm, options) {
  if (request.status == 200) {
    try {
      var answer = JSON.parse(request.responseText);
    } catch (e) {
      // ignore
    }
    if (answer && answer.state) {
      showPopup(answer.state, answer.message);
    } else if (queryForm) {
      queryForm.setResultHTML(request.responseText);
      if (options && !options.silent) {
        showPopup("success", "");
      }
    } else {
      showPopup("error", request.responseText);
    }
  } else {
    showPopup("error", "Request failed: " + request.responseText);
  }
}
