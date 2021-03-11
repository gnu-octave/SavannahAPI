/**
 * Web application to display and search Savannah bugs and patches.
 */

var queryList;  /// Unique instance of QueryWidgetList (Singleton Pattern).

/**
 * Things to do, once the page is loaded at the client.
 */
$(document).ready(function(){

  queryList = new QueryWidgetList($("#queries")[0], $("#appImportExport")[0]);

  new QuickSearchWidget(document.location.search.substring(1));

  // Initialize "Settings"
  makeTextareaAdjustable($("#appImportExport")[0]);
  $("#settingsCopy").click(function(event) {
      copyToClipboard($("#appImportExport")[0].value);
    });
  $("#settingsImport").click(function(event) {
      queryList.reset($("#appImportExport")[0].value);
      showPopup("warning", "JSON import successful.");
    });
  $("#settingsReset").click(function(event) {
      queryList.reset();
      showPopup("warning", "Reset successful.");
    });

});


/**
 * Representation of a query.  It consists of:
 *
 * @param label string to identify the query
 *
 * @param url some url assiciated to the query
 *
 * @param api parameters to query SavannahAPI.  For example:
 *              `Action=get&TrackerID=bugs` or very sloppy from user input
 *              `  Action=get    TrackerID=bugs  `.
 *              All input will be normalized to the first example form.
 */
class Query {
  constructor(label, url, api) {
    this.label  = label;
    this.api    = api.trim().replaceAll(/\s+/g, "&");
    this.url    = url;
  }
  static getDefault() {
    return new Query("New query", "", localStorage.getItem("defaultQuery"));
  }
  getLabel()       { return this.label; }
  getQueryString() { return this.api; }
  getURL()         { return this.url; }
  getPermaLink() {
    return localStorage.getItem("apiURL") + '?' + this.getQueryString();
  }
}


/**
 * Representation of a `Query` result.
 */
class QueryResult {
  constructor(str = "") {
    this.result = str;
    this.count  = 0;
    this.getCount();
  }
  getHTML() { return this.result }
  getCount() {
    if (this.result && (this.count <= 0)) {
      if (this.result.substr(0, 6) === "<table") {
        // ignore head line
        this.count = (this.result.match(/<tr/g) || []).length - 1;
      } else {
        try {
          this.count = JSON.parse(this.result).length;
        } catch (e) {
          // Must be CSV and count rows.  Ignore head line and last newline.
          this.count = this.result.split("\n").length - 2;
        }
      }
    }
    return this.count;
  }
}


/**
 * Class to control a list of `QueryWidget`s.
 */
class QueryWidgetList {
  constructor(rootNode, importExportNode) {
    this.items = [];
    this.rootNode         = rootNode;
    this.importExportNode = importExportNode;

    var queries = JSON.parse(localStorage.getItem("customQueries"));
    // If no custom queries are saved, load the default ones.
    if (!queries || (queries.length == 0)) {
      queries = JSON.parse(localStorage.getItem("defaultQueries"));
    }
    // Create all saved queries in "readonly" mode without popup messages.
    queries.forEach(q => this.add(new Query(q.label, q.url, q.api),
                                  {silent: true, readonly: true, save: false}));
    this.save();

    $("#newQueryButton").click(function(event) {
        queryList.add(Query.getDefault());
      });
  }

  /**
   * Remove all custom queries from `localStorage` and restarts the web app.
   *
   * @param jsonString new custom queries to be saved in `localStorage`.
   *                   If not given, the app will be reset to the default
   *                   queries during the reconstruction.
   */
  reset(jsonString = "") {
    var self = this;
    localStorage.removeItem("customQueries");
    this.items.forEach(i => self.remove(i));
    if (jsonString) {
      localStorage.setItem("customQueries", jsonString);
    }
    queryList = new QueryWidgetList(this.rootNode, this.importExportNode);
  }

  /**
   * Save current state of the web app persistently to the `localStorage`.
   */
  save() {
    var jsonString = this.getCustomQueriesJSON();
    localStorage.setItem("customQueries", jsonString);
    this.importExportNode.value = jsonString;
  }

  /**
   * Update graphical representation of @p item with @p newNode.
   *
   * @param item `QueryWidget` to be updated
   *
   * @param newNode new graphical representation (DOM `Node`) of @p item
   *
   * It is not necessary, that @p item had a graphical representation before.
   */
  update(item, newNode) {
    var old = item.getNode();
    if (old) {
      this.rootNode.replaceChild(newNode, old);
    } else {
      this.rootNode.appendChild(newNode);
    }
  }

  /**
   * Remove persistently @p item with graphical representation from the app.
   *
   * @param item `QueryWidget` to be removed from:
   *               1. graphical representation
   *               2. this list
   *               3. `localStorage`
   */
  remove(item) {
    this.rootNode.removeChild(item.getNode());
    this.items = this.items.filter(function(e){ return e!= item; });
    this.save();
  }

  /**
   * Returns the current state of the web app as JSON string.
   *
   * @return JSON string
   */
  getCustomQueriesJSON() {
    var queries = [];
    this.items.forEach(i => queries.push(i.getQuery()));
    return JSON.stringify(queries);
  }

  /**
   * Add a new query to the web app.
   *
   * @param query `Query` to be evaluated and added to the web app
   *
   * @param options object array with boolean fields
   *                  `silent`  : if true, do not show any popups on success
   *                  `readonly`: if true, the `QueryWidget` is started in
   *                              readonly mode
   *                  `save`: if true, save changes persistently to the
   *                          `localStorage`
   */
  add(query, options={silent: true, readonly: true, save: true}) {
    var widget = new QueryWidget(this, query, options);
        widget.send(options);
    this.items.push(widget);
    if (options.save) {
      this.save();
    }
  }
}


/**
 * Widget to display and modify `Queries`.
 */
class QuickSearchWidget {
  constructor(url_get_params) {
    this.result = new QueryResult();
    this.repaint();

    $("#quickSearchInput")[0].value = url_get_params;

    var self = this;
    $("#quickSearchClearButton").click(function(event) {
        self.result = new QueryResult();
        self.repaint();
        $("#quickSearchInput")[0].value = "";
      });
    $("#quickSearchSubmitButton").click(function(event) {
        self.send();
      });
    $("#quickSearchInput").keyup(function(event) {
        if (event.keyCode === 13) {
          event.preventDefault();
          $("#quickSearchSubmitButton").click();
        }
      });
  }

  setResultHTML(result) {
    this.result = new QueryResult(result);
    this.repaint();
  }

  repaint() {
    $("#quickSearchResult")[0].innerHTML = this.result.getHTML();
    $("#quickSearchCount")[0].innerHTML = "(" + this.result.getCount() + ")";
  }

  markBusy() {
    $("#quickSearchSubmitButton").disabled = true;
    $("#quickSearchCount")[0].innerHTML = `<i class="fas fa-sync fa-spin"></i>`;
  }

  markFree() {
    $("#quickSearchSubmitButton").disabled = false;
    this.repaint();
  }

  prepareQuery() {
    var input = $("#quickSearchInput")[0].value.trim();
    if (input.length < 3) {
      return false;
    }
    // Check if input contains API parameter no extra processing.
    if (input.includes("Action=get") || input.includes("Action=update")) {
      var qstring = input;
    } else {
      var qstring
        = 'Action=get&OrderBy=TrackerID,!ItemID&Format=HTMLCSS&Keywords=';
      qstring += input.replaceAll(' ', '%20');
    }
    return new Query('', '', qstring, '');
  }

  send(params) {
    params = (params ? params : {});
    const self = this;
    var query = this.prepareQuery();
    if (query === false) {
      showPopup("warning", "Search string must have at least 3 characters.");
      return;
    }
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == XMLHttpRequest.DONE) {
        self.markFree();
        params.widget = self;
        apiRequestHandleResult(this, params);
      }};
    xhttp.open("GET", "api.php?" + query.getQueryString(), true);
    xhttp.send();
    self.markBusy();
  }
}


/**
 * Widget to display and modify `Queries`.
 */
class QueryWidget {
  constructor(list, query, options) {
    this.list     = list;
    this.readonly = options.readonly;
    this.query    = query;
    this.result   = new QueryResult();

    // Sub-widgets that need access.
    this.url = null;
    this.label = null;
    this.parameter = null;
    this.refreshButton = null;

    this.node = null;
    this.repaint();
  }

  toggleReadOnly() {
    this.readonly = !this.readonly;
    this.repaint();
  }

  setResultHTML(result) {
    this.result = new QueryResult(result);
    this.repaint();
  }

  getNode() { return this.node };

  getQuery() {
    return (this.readonly ? this.query : new Query(this.label.value,
                                                   this.url.value,
                                                   this.parameter.value));
  }

  repaint() {
    const self = this;
    var query  = this.query;
    var params = this.parameter
               ? this.parameter.value
               : this.query.getQueryString().replaceAll("&", "\n");
    var element = document.createElement(null);
    if (this.readonly) {
      var labelHTML = `
        <div class="font-weight-bold mt-2">
          ${query.getLabel()}
          ${(query.getURL()
            ? `&nbsp;&nbsp;<a href="${query.getURL()}">[link]</a>`
            : "")}
        </div>`;
      var buttonsEditClass = "secondary";
      var buttonsEditIcon  = "far fa-edit";
      var buttonsHTML = "";
      var formHTML = "";
    } else {
      var labelHTML = `<input type="text" class="form-control"
            value="${this.label ? this.label.value : query.getLabel()}">`;
      var buttonsEditClass = "warning";
      var buttonsEditIcon  = "fas fa-ban";
      var buttonsHTML = `
        <div class="btn-group w-100 mt-1" role="group">
          <button type="button" class="btn btn-success form-control">
            <i class="far fa-save"></i>
          </button>
          <button type="button" class="btn btn-danger form-control">
            <i class="far fa-trash-alt"></i>
          </button>
        </div>`;
      var formHTML = `
      <div class="form-group input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">url</div>
        </div>
        <input type="url" class="form-control"
               value="${this.url ? this.url.value : query.getURL()}">
      </div>
      <div class="form-group input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">API parameter</div>
        </div>
        <textarea class="form-control">${params}</textarea>
      </div>`;
    }
    element.innerHTML = `
    <div class="accordion m-1">
      <div class="card">
        <div class="card-header card-header-mod">
          <div class="row">
            <div class="col-3 col-md-2 col-lg-2">
              <button type="button"
                      class="btn btn-info button-width-mod text-left"
                      data-toggle="collapse"
                      data-target=""
                      aria-expanded="true">
                &nbsp;<i class="fas fa-plus"></i>&nbsp;
                <span class="badge badge-pill badge-light badge-light-mod">
                  ${this.result.getCount()}
                </span>
              </button>
            </div>
            <div class="col">
              ${labelHTML}
            </div>
            <div class="col col-md-3">
              <div class="btn-group w-100" role="group">
                <button type="button"
                        class="btn btn-info form-control">
                  <i class="fas fa-sync"></i>
                </button>
                <button type="button"
                        class="btn btn-${buttonsEditClass} form-control">
                  <i class="${buttonsEditIcon}"></i>
                </button>
                <button type="button"
                        class="btn btn-info form-control">
                  <i class="far fa-clipboard"></i>
                </button>
              </div>
              ${buttonsHTML}
            </div>
          </div>
        </div>
        <div class="card-body collapse">
          ${formHTML}
          <div class="overflow-auto">
            ${this.result.getHTML()}
          </div>
        </div>
      </div>
    </div>
    `;
    element = element.firstElementChild;
    var buttons = $(element).find("button");  // order given above
    // toggle button
    buttons[0].addEventListener("click", function(event) {
        $(this).closest("div.card").children("div.card-body").collapse("toggle");
      });
    this.refreshButton = buttons[1];
    this.refreshButton.addEventListener("click", function(event) {
        self.send();
      });
    // edit cancel button
    buttons[2].addEventListener("click", function(event) {
        self.toggleReadOnly();
      });
    // copy button
    buttons[3].addEventListener("click",function(event) {
        copyToClipboard(self.getQuery().getPermaLink())
      });

    if (this.readonly) {
      this.label     = null;
      this.url       = null;
      this.parameter = null;
    } else {
      // save button
      buttons[4].addEventListener("click", function(event) {
          self.query = self.getQuery();
          self.list.save();
          self.toggleReadOnly();
        });
      // delete button
      buttons[5].addEventListener("click", function(event) {
          self.list.remove(self);
        });

      this.label     = $(element).find("input")[0];
      this.url       = $(element).find("input")[1];
      this.parameter = $(element).find("textarea")[0];
      makeTextareaAdjustable(this.parameter);
    }

    $(element).find("table").addClass(
      "table table-borderless table-hover table-responsive");

    // Copy state from previous node.
    if (this.node) {
      if ($(this.node).find('div.card-body')[0].classList.contains("show")) {
            $(element).find('div.card-body')[0].classList.add("show");
      }
    }

    this.list.update(this, element);
    this.node = element;
  }

  markBusy() {
    $(this.refreshButton).disabled = true;
    $(this.refreshButton).children('i')[0].classList.toggle("fa-spin");
  }

  markFree() {
    $(this.refreshButton).disabled = false;
    $(this.refreshButton).children('i')[0].classList.toggle("fa-spin");
  }

  send(params) {
    params = (params ? params : {});
    const self = this;
    var query = this.getQuery();
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == XMLHttpRequest.DONE) {
        self.markFree();
        params.widget = self;
        apiRequestHandleResult(this, params);
      }};
    xhttp.open("GET", "api.php?" + query.getQueryString(), true);
    xhttp.send();
    self.markBusy();
  }
}


/**
 * API callback function to update a single item.
 *
 * @param node (optional) DOM `Node` whos link will be replace with a question
 *                        mark.  Application specific parameter.
 *
 * @param tracker TrackerID verified by the server
 *
 * @param id ItemID verified by the server
 *
 */
function updateCallback(node=null, tracker, id) {
  var url   = "api.php?Action=update&TrackerID=" + tracker + "&ItemID=" + id;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == XMLHttpRequest.DONE) {
      apiRequestHandleResult(this,
                             {message: "<b>" + tracker + " #" + id + "</b>"});
    }};
  xhttp.open("GET", url, true);
  xhttp.send();
  if (node) {
    node.parentNode.innerHTML = '❓';
  }
}


/**
 * Handle the result of an API request.
 *
 * @param request result of an `XMLHttpRequest`
 *
 * @param params several application specific parameters
 */
function apiRequestHandleResult(request, params) {
  if (request.status == 200) {
    var answer = request.responseText;
    params = (params ? params : {});
    /*
     * The API server will answer with either a JSON string of the form:
     *
     *   {"state": ["success", "error", "warning", "info"], "message": string}
     *
     * or with a plain string containing the queried result, for example.
     */
    try {
      var answer = JSON.parse(answer);
    } catch (e) {
      // ignore
    }
    if (answer.state) {
      showPopup(answer.state, (params.message ? params.message + ' ' : '')
                              + answer.message);
    } else if (params.widget) {
      params.widget.setResultHTML(request.responseText);
      if (!params.silent) {
        showPopup("success", "");
      }
    } else {
      showPopup("warning", "Unknown server response: " + request.responseText);
    }
  } else {
    showPopup("error", "Request failed: " + request.responseText);
  }
}


/**
 * Shows a popup.
 *
 * @param type one of {"info", "warning", "error", "success"}
 *
 * @param message string to be displayed
 *
 * @param delay in milliseconds
 */
function showPopup(type, message, delay=10000) {
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
  $("#toasts")[0].appendChild(element);
  $(element).toast({delay: delay});
  $(element).toast('show');
  setTimeout(function(){ element.remove(); }, delay);
}

/**
 * Make a `textarea` adjustable to the given content.
 *
 * @param node DOM `Node` of a `textarea`
 */
function makeTextareaAdjustable(node) {
  var adjustHeight = function () {
      this.style.height = "1px";
      this.style.height = this.scrollHeight + "px";
    };
  node.addEventListener("change", adjustHeight);
  node.addEventListener("focus",  adjustHeight);
  node.addEventListener("keyup",  adjustHeight);
}

/**
 * Copy a string to the clipboard and show popup when done.
 *
 * @param str string to be copied to the clipboard.
 */
function copyToClipboard(str) {
  var temp = $("<input>");
  $("body").append(temp);
  temp.val(str).select();
  document.execCommand("copy");
  temp.remove();
  showPopup("info", "Copied to clipboard.");
}
