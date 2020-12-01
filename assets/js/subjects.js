"use strict";

async function requestEnrolledSubjects(token) {
    return await fetch(getApiUrl("enrolled-subjects/get.php"), { headers: { "X-Access-Token": token } });
}

async function requestAllSubjects() {
    return await fetch(getApiUrl("subjects/get.php"));
}

async function requestAttendance(code, token) {
    return await fetch(getApiUrl("attendance/get.php?subject_id=" + code),
        { headers: { "X-Access-Token": token }
    });
}

function displayEnrolledSubjects(container, requestPromise) {
    displayInfoMessages("#info-area", "");
    requestPromise.then((request) => {
        request.json().then((response) => {
            checkResponse(response);
            if (!response.error) {
                let html = "<option disabled selected>Choose a subject</option>";
                response.data.forEach(element => {
                    html += `<option value=${element.code}>[${element.code}] ${element.name}</option>`;
                });
                container.html(html);
                $("select").formSelect({classes: "text-light"});
            } else {
                showToast(response.message, "red", "close");
                displayInfoMessages("#info-area", response.message, "text-danger");
            }
        }).catch((error) => {
            request.status == 404 ? showToast("Request Error!", "red", "cancel")
                : responseParseError(error);
        });
    }).catch(() => { showToast("Server Error!", "red", "wifi_off") });
}
