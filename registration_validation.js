// Variables used for grabbing form information and elements.
const form = document.getElementById('registration_form');
const f_name = document.getElementById('fn_input');
const l_name = document.getElementById('ln_input');
const email = document.getElementById('email_input');
const password = document.getElementById('pwd_input');
const postal_code = document.getElementById('postalcode_input');
const errorOut = document.getElementById('error_message');
const errorOut2 = document.getElementById('error_message2');

// patterns used for passwords.
const UpperCaseChars = /[A-Z]/g;
const LowerCaseChars = /[a-z]/g;
const numbers = /[0-9]/g;
const Length = 8;

// List for holding error messages.
var errors = [];
var errors2 = [];
var errors_headers = "Error(text field): ";
var errors_headers2 = "Error(check box): ";

if(form){
    form.addEventListener('submit', (e) => {
        //Check if values are empty, if the email is correct, if the password meets requirements and if the check box is checked
        validateEmpty();
        validateEmail();
        validatePassword();
        validateConfirm();
        if (errors.length > 0){
            e.preventDefault();
            errorOut.innerText = errors_headers.concat(errors.join(', '));
            errors = [];
        }
        else{
            errorOut.innerText = '';
        }
        if (errors2.length > 0){
            e.preventDefault();
            errorOut2.innerText = errors_headers2.concat(errors2.join(', '));
            errors2 = [];
        }
        else{
            errorOut2.innerText = '';
        }
    });
}

//empty field validation
function validateEmpty(){
    if (f_name.value === '' || f_name.value == null){
        errors.push('First Name field is Empty');
    }
    if (l_name.value === '' || l_name.value == null){
        errors.push('Last Name field is Empty');
    }
    if (email.value === '' || email.value == null){
        errors.push('Email field is Empty');
    }
    if (password.value === '' || password.value == null){
        errors.push('Password field is Empty');
    }
}

//confirm checkbox validation
function validateConfirm(){
    var x = document.getElementById('confirm_input').checked;
    if(x == false){
        errors2.push('Please check the box if you confirm your information.');
    }
}

//email format validation
// regular expression for email format: /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
function validateEmail(){
    if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))) {
        errors.push('email invalid');
    }
}

// Validating Password requirements, this can be split up more.
function validatePassword(){ //include at least one uppcase letter, at least one lowercase letter and at least one number
    if (!(password.value.match(UpperCaseChars) 
    && password.value.match(LowerCaseChars) 
    && password.value.match(numbers) 
    && password.value.length >= Length)){ //length of the password should be greater than or equal to 8
        errors.push('Password Invalid');
    }
}