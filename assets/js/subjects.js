"use strict";

async function requestEnrolledSubjects(token) {
    return await fetch(getApiUrl("enrolled-subjects/get.php"), { headers: { "X-Access-Token": token } });
}

async function requestAllSubjects() {
    return await fetch(getApiUrl("subjects/get.php"));
}
