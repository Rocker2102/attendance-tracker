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
    return;
    if (typeof response.reauth != "undefined") {
        /* TODO pop reauth modal */
    }
    if (typeof response.redirect != "undefined") {
        location.href = response.redirect;
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

function uiInit() {
    /* TODO: launch init function to detect stored token & fetch user info (show status in login modal) */
}
