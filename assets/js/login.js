"use strict";

$("#login-form").on("submit", function(e) {
    e.preventDefault();
    let credentials = new FormData(this);
    let submitBtn = $(this).find("button[type='submit']");
    let defaultText = submitBtn.html();

    function reset() {
        modButton(submitBtn, defaultText, false, "btn-warning", "btn-success browser-default");
    }

    modButton(submitBtn, "Authenticating " + getSpinner("sync", "right-align"), true, "btn-success", "btn-warning");

    requestAccessToken(credentials).then(function(request) {
        reset();
        request.json().then((response) => {
            checkResponse(response);
            if (!response.error) {
                setTokenStatus("valid");
                setAccessToken(response.data) ? showToast(response.message, "green", "https")
                    : showToast("LocalStorage/Cookie error!", "red", "error_outline");
            } else {
                showToast(response.message, "red", "close")
            }
        }).catch((error) => {
            request.status == 404 ? showToast("Request Error!", "red", "cancel")
                : responseParseError(error);
        });
    }).catch(() => {
        reset();
        showToast("Server Error!", "red", "wifi_off");
    });
});

async function requestAccessToken(credentials) {
    return await fetch(getApiUrl("tokens/get.php"), {
        method: "POST",
        body: credentials
    });
}
