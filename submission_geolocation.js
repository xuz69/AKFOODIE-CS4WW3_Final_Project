function showlocation() {
    // One-shot position request.
    navigator.geolocation.getCurrentPosition(callback);
}

function callback(position) {
    document.getElementById('inputlatitude').value = position.coords.latitude; // replace the text "latitude" with the actual current latitude
    document.getElementById('inputlongitude').value = position.coords.longitude; // replace the text "longitude" with the actual current longitude
}