/*  
    TODO: 
    1. handle errors better using separate html file
    2. before going to contacts page from login check if there is a cookie
*/



const urlBase = 'http://primaljet.com/LAMPAPI';
const extension = '.php';

function doLogout()
{
	if(document.cookie.split("=")[1] != ""){
        document.cookie = "userID=0; expires = Thu, 01 Jan 1970 00:00:00 GMT";
    }
    window.location.href = "http://primaljet.com/index.html"; 
}

function saveCookie(cookieName,id)
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	// document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
    document.cookie = cookieName + "=" + id
}

function readCookie()
{
	let userID = -1;
    let recordID = -1;

	let data = document.cookie;
	let splits = data.split(",");
	for(let i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		
	    if( tokens[0] == "userID" )
		{
			userID = parseInt( tokens[1].trim() );
		}

        else if (tokens[0] == "recordID")
        {
            recordID = parseInt(tokens[1].trim())
        }
	}

    return [userID, recordID]  
}

function createContact(firstName, lastName, email, phone){
    const container = document.querySelector("#results")

    const row = document.createElement("div")
    row.classList.add("row", "text-center")

    const firstNameCol = document.createElement("div")
    firstNameCol.classList.add("col")
    firstNameCol.appendChild(document.createTextNode(firstName))

    const lastNameCol = document.createElement("div")
    lastNameCol.classList.add("col")
    lastNameCol.appendChild(document.createTextNode(lastName))

    const emailCol = document.createElement("div")
    emailCol.classList.add("col")
    emailCol.appendChild(document.createTextNode(email))

    const phoneCol = document.createElement("div")
    phoneCol.classList.add("col")
    phoneCol.appendChild(document.createTextNode(phone))

    const editCol = document.createElement("div")
    editCol.classList.add("col")
    const editTag = document.createElement("a")
    editTag.classList.add("btn", "btn-default", "btn-lg")
    editTag.href = "edit.html"
    editTag.type = "button"
    const itag = document.createElement("i")
    itag.classList.add("bi", "bi-pencil-square")
    editTag.appendChild(itag)
    editCol.appendChild(editTag)


    row.appendChild(firstNameCol)
    row.appendChild(lastNameCol)
    row.appendChild(emailCol)
    row.appendChild(phoneCol)
    row.appendChild(editCol)

    container.appendChild(row)
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
        password = md5(password)
        const payload = {firstName:firstname, lastName:lastname, login:username, password:password}
        const res = await axios.post(urlBase + '/register' + extension, payload)

        if (res.data.error != ""){
            throw new Error(res.data.error)
        }

        else{
            const userID = res.data.id
            saveCookie("userID", userID)
            window.location.href = "http://primaljet.com/HTML/contact.html"; 
        }
    }

    catch(e){
        console.log("Error happened ugh", e)
    }

}

async function searchContact(){
    let container = document.getElementById("results")
    while (container.children.length > 1){
        container.removeChild(container.lastChild)
    }
    
    let searchquery = document.getElementById("form1").value
    let splits = searchquery.split(" ")
    let search = []

    for (let split of splits){
        search.push(split)
    }

    if (searchquery == ""){
        window.alert("Last name cannot be blank");
        return false;
    }

    try{
        const userID = readCookie()[0]
        const payload = {userID: userID, inputs:search}
        const res = await axios.post(urlBase + '/searchContact' + extension, payload)

        if (res.data.error != ""){
            throw new Error(res.data.error) 
        }
        else if (res.data.results.length == 0){
            console.log("nothing found")
        }

        else{
            for (let row of res.data.results){
                createContact(row["firstName"], row["lastName"], row["email"], row["phone"])
            }
        }
    }
    catch(e){
        console.log("Error happened, ugh", e)
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
        password = md5(password)
        const payload = {login:username, password:password}
        const res = await axios.post(urlBase + '/login' + extension, payload)
        if (res.data.error != ""){
            throw new Error(res.data.error)
        }

        else{
            const userID = res.data.id
            saveCookie("userID", userID)
            window.location.href = "http://primaljet.com/HTML/contact.html";
        }
        
    }

    catch(e){
        console.log("Error happened ugh", e)
    }
}

async function addContact() {
    let firstname = document.getElementById("afname").value;
    let lastname = document.getElementById("alname").value;
    let email = document.getElementById("aemail").value;
    let phone = document.getElementById("aphone").value;

    const isRequired = value => value === '' ? false : true;

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

    if (!isRequired(email)) {
        window.alert("Email cannot be blank.");
        email.focus();
        return false;
    }

    if (!isRequired(phone)) {
        window.alert("Phone cannot be blank");
        phone.focus();
        return false;
    }

    try{
        const userID = readCookie()[0]
        const payload = {userID:userID, firstName:firstname, lastName:lastname, email:email, phone:phone}
        const res = await axios.post(urlBase + '/addContact' + extension, payload)

        if (res.data.error != ""){
            throw new Error(res.data.error)
        }

        else{
            window.location.href = "http://primaljet.com/HTML/contact.html"; 
            console.log("contact was created successfully")
        }
    }

    catch(e){
        console.log("Error happened ugh", e)
    }
}
