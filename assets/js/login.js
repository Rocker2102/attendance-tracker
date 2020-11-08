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
        if (request.ok) {
            request.json().then((response) => {
                setAccessToken(response.data) ? showToast(response.message, "green", "https")
                    : showToast("LocalStorage/Cookie error!", "red", "error_outline");
            }).catch(responseParseError);
        } else {
            request.json().then((response) => {
                showToast(response.message, "red", "close")
            }).catch(() => {
                showToast("An error occurred!", "red", "error_outline");
            });
        }
    }).catch(() => {
        reset();
        showToast("Server Error!", "red", "wifi_off");
    });
});

async function requestAccessToken(credentials) {
    return await fetch(getApiUrl("users/get_token.php"), {
        method: "POST",
        body: credentials
    });
}
