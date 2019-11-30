// Variables used for grabbing form information and elements.
const form = document.getElementById('form');
const email = document.getElementById('email_input');
const password = document.getElementById('password_input');
const errorOut = document.getElementById('error_message');

// List for holding error messages.
var errors = [];
if(form){
    form.addEventListener('submit', (e) => {
    //  Check if values are empty, if the email is correct and if the password meets requirements
        validateEmpty();
        validateEmail();
        if (errors.length > 0){
            e.preventDefault();
            errorOut.innerText = errors.join(', ');
            errors = [];
        }
    });
}

// Checking if inputs are empty
function validateEmpty(){
    if (email.value === '' || email.value == null){
        errors.push('Email field is Empty');
    }
    if (password.value === '' || password.value == null){
        errors.push('Password field is Empty');
    }
}

// Regular Expression Pattern
// /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
function validateEmail(){
    if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))) {
        errors.push('email invalid');
    }
}
