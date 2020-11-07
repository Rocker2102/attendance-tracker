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
});

$.ajaxSetup({
    headers: {
        "X-Access-Token": getAccessToken()
    }
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
    let ls = new localStorage();
    if (ls.getState()) {
        return ls.getKey("accessToken");
    } else if (navigator.cookieEnabled) {
        return getCookie("accessToken");
    } else {
        return false;
    }
}

function setAccessToken(token) {
    let ls = new localStorage();
    if (ls.getState()) {
        ls.setKey("accessToken", token);
        return true;
    } else if (navigator.cookieEnabled) {
        setCookie("accessToken", token, 1);
        return true;
    } else {
        return false;
    }
}

function parseJsonResponse(response) {
    try {
        response = JSON.parse(response);
        return response;
    } catch (e) {
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

function getSpinner(icon = "sync") {
    let iconsArr = ["refresh", "rotate_right", "motion_photos_on"];
    return getMaterialIcon(icon, "rotate");
}

function getMaterialIcon(icon, addClasses = "", DOMElement = false) {
    return DOMElement ? createElement("i", {class: "material-icons " + addClasses}, icon)
        : "<i class='material-icons " + addClasses + "'>" + icon + "</i>";
}

function showToast(htmlData, classData = "blue white-text", icon = "info") {
    let toastIcon = getMaterialIcon(icon, "left");
    return M.toast({html: toastIcon + htmlData, classes: classData});
}
