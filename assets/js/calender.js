"use strict";

$(document).ready(function() {
    displayEnrolledSubjects($("#subject-id"), requestEnrolledSubjects(getAccessToken().token));
    $("select").formSelect();
});

$("#subject-id").change(function() {
    fetchAttendanceAtDate();
});

$("#start-date").on("blur", function() {
    fetchAttendanceAtDate();
});

function fetchAttendanceAtDate() {
    let subject_id = $("#subject-id").val();
    let start_date = $("#start-date").val(), end_date = start_date;

    if (subject_id == "" || subject_id == null) {
        return;
    }
    if (start_date == "" || start_date == null) {
        return;
    }

    let formdata = new FormData();
    formdata.append("subject_id", subject_id);
    formdata.append("start_date", start_date);
    formdata.append("end_date", end_date);

    displayInfoMessages("#info-area", "Loading..." + getSpinner("sync", "right-align"), "text-warning");
    fetch(getApiUrl("attendance/get.php"), {
            method: "POST",
            body: formdata,
            headers: { "X-Access-Token": getAccessToken().token
        }
    }).then((request) => {
        displayInfoMessages("#info-area", "");
        request.json().then((response) => {
            checkResponse(response);
            if (!response.error) {
                updateCurrentView(response.data);
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

function updateCurrentView(data) {
    let actualType = $("#actual-type");
    actualType.removeClass("bg-success bg-warning bg-danger bg-info text-light text-dark");

    $("#subject-details").html(`${data.subject_name}, ${data.subject_id}`);
    $("#mark-date").html(data.start_date);
    if (data.attendance.present == 1) {
        actualType.attr("data-state", "present").addClass("bg-success").html("Present");
    } else if (data.attendance.absent == 1) {
        actualType.attr("data-state", "absent").addClass("bg-danger").html("Absent");
    } else if (data.offdates.length == 1) {
        actualType.attr("data-state", "weeklyoff").addClass("bg-info").html("Weekly Off");
    } else if (data.holidays.length == 1) {
        actualType.attr("data-state", "leave").addClass("bg-warning text-dark").html("Holiday");
    } else {
        actualType.attr("data-state", "").html("-");
    }
}
