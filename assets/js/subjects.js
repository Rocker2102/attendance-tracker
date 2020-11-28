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
