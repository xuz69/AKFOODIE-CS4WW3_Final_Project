// Function to create the cookie 
function createCookie(name, value, days) { 
    var expires; 
    
    if (days) { 
        var date = new Date(); 
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000)); 
        expires = "; expires=" + date.toGMTString(); 
    } 
    else { 
        expires = ""; 
    } 
    
    document.cookie = escape(name) + "=" +  
        escape(value) + expires + "; path=/"; 
} 

function showlocation() {
    // One-shot position request.
    navigator.geolocation.getCurrentPosition(callback);
}
function callback(position) {
    createCookie("lat", position.coords.latitude, "1"); 
    createCookie("log", position.coords.longitude, "1"); 
    window.location.href = "searchbyloc.php";
}
