"use strict";

$(document).ready(function() {
    updateEnrolledSubjects(requestEnrolledSubjects(getAccessToken().token));
    updateAllSubjects(requestAllSubjects());
});

function displayEnrolledSubjects(data) {
    let container = $("#enrolled");
    container.html("");
    for (let i = 0; i < data.length; i++) {
        container.append(getRow(data[i], "remove"));
    }
}

function displayAllSubjects(data) {
    let container = $("#all-subjects");
    container.html("");
    for (let i = 0; i < data.length; i++) {
        container.append(getRow(data[i], "add"));
    }
}

function getRow(data, rowIcon) {
    let li = createElement("li", {class: "collection-item",
        "ctm-code": data.code, "ctm-name": data.name});
    $(li).append(createElement("span", {},
        `<strong>[${data.code}]</strong> ${data.name}`));
    $(li).append(createElement("a", {href: "javascript:void(0)", class: "secondary-content"},
        getMaterialIcon(rowIcon)));
    return li;
}

function updateEnrolledSubjects(requestPromise) {
    displayInfoMessages("#info-area", "");
    requestPromise.then((request) => {
        request.json().then((response) => {
            checkResponse(response);
            if (!response.error) {
                displayEnrolledSubjects(response.data);
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

function updateAllSubjects(requestPromise) {
    requestPromise.then((request) => {
        request.json().then((response) => {
            if (!response.error) {
                displayAllSubjects(response.data);
            } else {
                showToast(response.message, "red", "close");
            }
        }).catch((error) => {
            request.status == 404 ? showToast("Request Error!", "red", "cancel")
                : responseParseError(error);
        });
    }).catch(() => { showToast("Server Error!", "red", "wifi_off") });
}

$("#all-subjects").on("click", "a", function() {
    let item = $(this).closest("li.collection-item");
    let enrollModal = $("#enroll-modal");
    enrollModal.find("input[name='subject_id']").val(item.attr("ctm-code"));
    enrollModal.find("input[id='subject_name']").val(item.attr("ctm-name"));
    M.updateTextFields();
    enrollModal.modal("open");
});

$("#enrolled").on("click", "a", function() {
    let item = $(this).closest("li.collection-item");
    let subjectData = new FormData;
    subjectData.append("subject_id", item.attr("ctm-code"));

    if (item.attr("disabled") == "disabled") {
        return;
    }

    item.attr("disabled", "disabled");

    fetch(getApiUrl("enrolled-subjects/remove.php"), {
        method: "POST",
        headers: {
            "X-Access-Token": getAccessToken().token
        },
        body: subjectData
    }).then((request) => {
        item.attr("disabled", "");
        request.json().then((response) => {
            checkResponse(response);
            if (!response.error) {
                showToast(response.message, "blue", "info");
                item.remove();
            } else {
                displayInfoMessages("#info-area", response.info, "text-warning");
                showToast(response.message, "red", "close")
            }
        }).catch((error) => {
            request.status == 404 ? showToast("Request Error!", "red", "cancel")
                : responseParseError(error);
        });
    });
});

$("#enroll-form").on("submit", function(e) {
    e.preventDefault();
    let form = $(this);
    let subjectData = new FormData(this);
    let submitBtn = form.find("button[type='submit']");
    let defaultText = submitBtn.html();
    let rowData = {
        code: form.find("input[name='subject_id']").val(),
        name: form.find("input#subject_name").val()
    }

    function reset() {
        modButton(submitBtn, defaultText, false);
    }

    modButton(submitBtn, "Enrolling " + getSpinner("sync", "right-align"), true);
    displayInfoMessages("#info-area", "");

    fetch(getApiUrl("enrolled-subjects/add.php"), {
        method: "POST",
        headers: {
            "X-Access-Token": getAccessToken().token
        },
        body: subjectData
    }).then((request) => {
        reset();
        request.json().then((response) => {
            checkResponse(response);
            if (!response.error) {
                $("#enroll-modal").modal("close");
                $("#enrolled").append(getRow(rowData, "remove"));
                showToast(response.message, "green", "done");
            } else {
                showToast(response.message, "red", "close");
                typeof response.info != "undefined" ? displayInfoMessages("#info-area", response.info, "text-danger") : false;
                console.log(typeof response.info);
            }
        }).catch((error) => {
            request.status == 404 ? showToast("Request Error!", "red", "cancel")
                : responseParseError(error);
        });
    });
});
