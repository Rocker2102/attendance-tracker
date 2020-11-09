"use strict";

$("#login-form").on("submit", function(e) {
    e.preventDefault();
    let credentials = new FormData(this);
    let submitBtn = $(this).find("button[type='submit']");
    let defaultText = submitBtn.html();

    function reset() {
        modButton(submitBtn, defaultText, false);
    }

    modButton(submitBtn, "Authenticating " + getSpinner("sync", "right-align"), true);

    requestAccessToken(credentials).then(function(request) {
        reset();
        request.json().then((response) => {
            checkResponse(response);
            if (!response.error) {
                setAccessToken(response.data) ? showToast(response.message, "green", "https")
                    : showToast("LocalStorage/Cookie error!", "red", "error_outline");
                setTokenStatus("valid");
                updateAccountData(requestAccountData(response.data.token));
                initTokenTimer(new Date(response.data.valid_till));
                $("#logout-btn").removeClass("d-none");
                $("#login-modal").modal("close");
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

$("#logout-btn").click(function() {
    $(this).html(getSpinner("motion_photos_on", "left") + "Logging out...");
    removeAccessToken(getAccessToken().token).then(() => {
        setAccessToken("");
        location.reload();
    });
});

async function removeAccessToken(token) {
    return await fetch(getApiUrl("tokens/remove.php"), { headers: { "X-Access-Token": token } });
}
