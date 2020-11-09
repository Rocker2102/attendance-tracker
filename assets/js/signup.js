"use strict";

$("#register-form").on("submit", function(e) {
    e.preventDefault();
    let userdata = new FormData(this);
    let submitBtn = $(this).find("button[type='submit']");
    let defaultText = submitBtn.html();

    function reset() {
        displayInfoMessages("#info-area", "");
        modButton(submitBtn, defaultText, false);
    }

    modButton(submitBtn, "Registering " + getSpinner("sync", "right-align"), true);

    submitUserData(userdata).then(function(request) {
        reset();
        request.json().then((response) => {
            checkResponse(response);
            if (!response.error) {
                showToast(response.message, "green", "person_add");
                setTimeout(() => { location.href = "index.php?modal=login-modal" }, 3000);
            } else {
                displayInfoMessages("#info-area", response.info, "text-warning");
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

async function submitUserData(userdata) {
    return await fetch(getApiUrl("users/add.php"), {
        method: "POST",
        body: userdata
    });
}
