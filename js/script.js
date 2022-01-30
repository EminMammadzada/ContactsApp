const urlBase = 'http://primaljet.com/LAMPAPI';
const extension = '.php';

function saveCookie(cookieName,id)
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	// document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
    document.cookie = cookieName + "=" + id
}

function doLogout()
{
	userId = 0;
	document.cookie = "";
	window.location.href = "http:primaljet.com/index.html";
}

async function validRegister() {
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

    try{
        //TODO: ACCOUNT FOR CASES WHEN IT IS NOT VALID
        const payload = {firstName:firstname, lastName:lastname, login:username, password:password}
        const res = await axios.post(urlBase + '/register' + extension, payload)
        const userID = res.data.id
        saveCookie("userID", userID)
        window.location.href = "http://primaljet.com/HTML/contact.html"; 
    }

    catch(e){
        // TODO: handle error better
        console.log("Error happened ugh", e)
    }

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
        const payload = {login:username, password:password}
        const res = await axios.post(urlBase + '/login' + extension, payload)
        const userID = res.data.id
        saveCookie("userID", userID)
        window.location.href = "http://primaljet.com/HTML/contact.html";
        
    }

    catch(e){
        console.log("Error happened ugh", e)
    }
}

