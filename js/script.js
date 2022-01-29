const urlBase = 'http://primaljet.com/LAMPAPI';
const extension = 'php';

function validRegister() {
    let firstname = document.getElementById("rfirst").value;
    let lastname = document.getElementById("rlast").value;
    let username = document.getElementById("ruser").value;
    let password = document.getElementById("rpassword").value;

    const isRequired = value => value === '' ? false : true;
    const isBetween = (length, min, max) => length < min || length > max ? false : true;
    const min = 3, max = 25;

    if (!isRequired(firstname)) {
        window.alert("First name cannot be blank.");
        firstname.focus();
        return false;
    }

    if (!isRequired(lastname)) {
        window.alert("Last name cannot be blank");
        lastname.focus();
        return false;
    }

    if (!isRequired(username)) {
        window.alert("Username cannot be blank.");
        username.focus();
        return false;
    }

    if (!isBetween(username.length, min, max)) {
        window.alert("Username must be between 3 and 25 characters.");
        username.focus();
        return false;
    }

    if (!isRequired(password)) {
        window.alert("Password cannot be blank.");
        password.focus();
        return false;
    }

    alert("Registration Form Success!")
    register()
}

async function validLogin() {
    let username = document.getElementById("user").value;
    let password = document.getElementById("password").value;

    const isRequired = value => value === '' ? false : true;
    const isBetween = (length, min, max) => length < min || length > max ? false : true;
    const min = 3, max = 25;

    if (!isRequired(username)) {
        window.alert("Username cannot be blank.");
        username.focus();
        return false;
    }

    if (!isBetween(username.length, min, max)) {
        window.alert("Username must be between 3 and 25 characters.");
        username.focus();
        return false;
    }

    if (!isRequired(password)) {
        window.alert("Password cannot be blank.");
        password.focus();
        return false;
    }


    try{
        const payload = {login: username, password:password}
        JSON.stringify(payload)
        const res = await axios.post(urlBase + '/login' + extension, payload)
        return res
    }

    catch(e){
        console.log("Error", e)
    }
}

async function register() {
    userID = 0;
    let firstname = document.getElementById("rfirst").value;
    let lastname = document.getElementById("rlast").value;
    let username = document.getElementById("ruser").value;
    let password = document.getElementById("rpassword").value;

    document.getElementById("registerResult").innerHTML = "";

    let temp = {firstname:firstname, lastname:lastname, login:username, password:password};
    let jsonPayload = JSON.stringify( tmp );

    let url = urlBase + '/register.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    

    try {
        xhr.onreadystatechange = function() {
            
            if (this.readyState == 4 && this.status == 200) {
                
                let jsonobject = JSON.parse( xhr.responseText );
                userID = jsonObject.ID;

                firstname = jsonobject.firstname;
                lastname = jsonobject.lastname;

                window.location.href = "contact.html";
            }
        };
        xhr.send(jsonPayload);
    }
    catch(err) {
        document.getElementById("loginResult").innerHTML = err.message;
    }
}
