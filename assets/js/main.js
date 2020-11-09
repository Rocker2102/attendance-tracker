"use strict";

class localStorage {
    constructor() {
        if (typeof(Storage) === "undefined") {
            this.state = false;
        } else {
            this.state = true;
        }
    }

    getState() {
        return this.state;
    }

    setKey(key, data) {
        if (this.getState()) {
            window.localStorage.setItem(key, data);
        }
    }

    getKey(key) {
        if (this.getState()) {
            return window.localStorage.getItem(key);
        }
    }

    removeKey(key) {
        if (this.getState()) {
            window.localStorage.removeItem(key);
        }
    }

    clearAll() {
        window.localStorage.clear();
    }
}

$(document).ready(function() {
    $(".sidenav").sidenav();
    $(".modal").modal();
    M.updateTextFields();

    uiInit();
});

$(".modal-close").click(function() {
    $(this).closest(".modal").modal("close");
});

function parseUrlQuery(name = null, url = location.href) {
    let parsedUrl = {};
    let queryPart = url.split("?");

    if (queryPart.length <= 1) {
        return null;
    }
    queryPart = queryPart[1];
    let params = queryPart.split("&");

    for (let i = 0; i < params.length; i++) {
        try {
            params[i] = params[i].split("=");
            let paramName = decodeURIComponent(params[i][0]);
            let paramVal = decodeURIComponent(params[i][1]);
            if (name == paramName) {
                return paramVal;
            }
            name == null ? parsedUrl[paramName] = paramVal : false;
        } catch (e) {
            console.log("ParseError: " + params[i]);
            continue;
        }
    }

    return name == null ? parsedUrl : null;
}

function setCookie(name, value, expiry = 0, path = "/") {
    let date = new Date();
    if (expiry < 0) {
        expiry = -365 * 50;
    }
    date.setTime(date.getTime() + expiry * 24 * 60 * 60 * 1000);
    expiry = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expiry + ";path=" + path;
}

function getCookie(name) {
    let cookieName = name + "=";
    let cookieArray = document.cookie.split(";");
    for (let i = 0; i < cookieArray.length; i++) {
        let tmp = cookieArray[i];
        while (tmp.charAt(0) == " ") {
            tmp = tmp.substring(1);
        }
        if (tmp.indexOf(cookieName) == 0) {
            return tmp.substring(cookieName.length, tmp.length);
        }
    }
    return false;
}

function getAccessToken() {
    let key = "accessToken";
    let ls = new localStorage();
    try {
        if (ls.getState()) {
            return JSON.parse(ls.getKey(key));
        } else if (navigator.cookieEnabled) {
            return JSON.parse(getCookie(key));
        } else {
            return false;
        }
    } catch (error) {
        console.error(`Failed to obtain access token. ${error}`);
        return false;
    }
}

function setAccessToken(token) {
    let key = "accessToken";
    let ls = new localStorage();
    try {
        if (ls.getState()) {
            ls.setKey(key, JSON.stringify(token));
            return true;
        } else if (navigator.cookieEnabled) {
            setCookie(key, JSON.stringify(token), 2);
            return true;
        } else {
            return false;
        }
    } catch (error) {
        console.error(`Failed to store access token. ${error}`);
        return false;
    }
}

function checkResponse(response) {
    if (typeof response.reauth != "undefined") {
        setAccessToken("");
        $("#login-modal").modal("open");
    }
    if (typeof response.redirect != "undefined") {
        setTimeout(() => { location.href = response.redirect.url }, response.redirect.after);
    }
}

function getApiUrl(part) {
    return "http://localhost/development/attendance-tracker/api/" + part;
}

function modButton(button, html, disabledState = true, removeClasses = "", addClasses = "") {
    button.attr("disabled", disabledState).removeClass(removeClasses)
        .addClass(addClasses).html(html);
}

function getSpinner(icon = "sync", addClasses = "") {
    let iconsArr = ["refresh", "rotate_right", "motion_photos_on"];
    return getMaterialIcon(icon, "rotate " + addClasses);
}

function getMaterialIcon(icon, addClasses = "", DOMElement = false) {
    return DOMElement ? createElement("i", {class: "material-icons " + addClasses}, icon)
        : "<i class='material-icons " + addClasses + "'>" + icon + "</i>";
}

function showToast(htmlData, classData = "blue white-text", icon = "info") {
    let toastIcon = getMaterialIcon(icon, "left");
    return M.toast({html: toastIcon + htmlData, classes: classData});
}

function responseParseError(error = null) {
    showToast("Parse error", "red", "rule");
    error != null ? console.warn("Parse error: " + error) : false;
}

function isUndefined(data) {
    return typeof data == "undefined";
}

function uiInit() {
    let ls = new localStorage();
    let accessToken = getAccessToken();
    let queryModal = parseUrlQuery("modal");

    try {
        $("#" + queryModal).modal("open");
    } catch (error) {}

    if (!accessToken || accessToken == "" || accessToken == null) {
        setTokenStatus("unavailable");
    } else {
        $("#logout-btn").removeClass("d-none");
        let validTill = new Date(accessToken.valid_till);
        let current = new Date();
        if (current < validTill) {
            setTokenStatus("valid");
            initTokenTimer(new Date(accessToken.valid_till));
            displayAccountData(JSON.parse(ls.getKey("userdata")));
        } else {
            updateAccountData(requestAccountData(accessToken.token));
        }
    }
}

function initTokenTimer(validTill) {
    function formatNumber(num) {
        return num < 10 ? "0" + num : num;
    }

    let timer = setInterval(function() {
        try {
            let element = $("#token-timer");
            const str = "<i class='material-icons left-align'>av_timer</i>";
            let current = new Date();
            let diff = Math.floor((validTill - current) / 1000);
            if (isNaN(diff) || diff <= 0) {
                element.html(str + "00:00:00");
                clearInterval(timer);
                return;
            }
            let hours = Math.floor((diff) / 3600);
            let minutes = Math.floor((diff - hours * 3600) / 60);
            let seconds = Math.floor(diff - hours * 3600 - minutes * 60);
            let newStr = str + formatNumber(hours) + ":" + formatNumber(minutes) + ":"
                + formatNumber(seconds);
            element.html(newStr);
        } catch (error) {
            console.warn("Failed to update timer!", error);
            clearInterval(timer);
            return;
        }
    }, 1000);
}

function displayAccountData(data = {}) {
    try {
        $("[account-name]").html(data["name"]);
        $("[account-username]").html(data["username"]);
    } catch (error) {
        let tmp = "unavailable";
        displayAccountData({"name": tmp, "username": tmp});
    }
}

function updateAccountData(requestPromise) {
    let ls = new localStorage();
    requestPromise.then((request) => {
        setTokenStatus("invalid");
        request.json().then((response) => {
            checkResponse(response);
            if (!response.error) {
                displayAccountData(response.data);
                ls.setKey("userdata", JSON.stringify(response.data));
                setTokenStatus("valid");
                showToast(response.message, "green", "done");
            } else {
                showToast(response.message, "red", "close")
            }
        }).catch((error) => {
            request.status == 404 ? showToast("Request Error!", "red", "cancel")
                : responseParseError(error);
        });
    }).catch(() => { showToast("Server Error!", "red", "wifi_off") });
}

async function requestAccountData(token) {
    setTokenStatus("verifying");
    return await fetch(getApiUrl("users/get.php"), { headers: { "X-Access-Token": token } });
}

function setTokenStatus(newStatus) {
    newStatus = newStatus.toLowerCase();
    let element = $("#token-status");
    let status = {
        "unavailable": {
            "icon": "info",
            "iconClass": "right-align",
            "class": "white"
        },
        "verifying": {
            "icon": "rotate_right",
            "iconClass": "right-align rotate",
            "class": "yellow"
        },
        "valid": {
            "icon": "done",
            "iconClass": "right-align",
            "class": "green"
        },
        "invalid": {
            "icon": "close",
            "iconClass": "right-align",
            "class": "red"
        }
    }

    function applyStatus(obj) {
        let remove = "white yellow green red";
        element.removeClass(remove).addClass(obj.class)
            .html(newStatus + getMaterialIcon(obj.icon, obj.iconClass));
    }

    typeof status[newStatus] != "undefined" ? applyStatus(status[newStatus]) : console.log("Invalid parameters!");
}

function displayInfoMessages(element, messages, classes = "") {
    let remove = "text-danger text-dark text-light text-success text-warning"
    $(element).removeClass(remove).addClass(classes).html(Array.isArray(messages) ? messages.join("<br>") : messages);
}
