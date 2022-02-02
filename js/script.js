/*  
    TODO: 
    1. handle errors better using separate html file
    2. before going to contacts page from login check if there is a cookie
    3. go to edit page only if we have recordID


    4. add logout button for registered user
    5. 
*/



const urlBase = 'http://primaljet.com/LAMPAPI';
const extension = '.php';

function doLogout()
{
    removeCookies(true)
    window.location.href = "http://primaljet.com/index.html"; 
}

function removeCookies(deleteBoth){
    document.cookie = "recordID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/"
    if (deleteBoth){
        document.cookie = "userID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    }
}

function saveCookie(cookieName,id)
{
	let minutes = 10;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
    const expires = "expires=" + date.toUTCString();
    document.cookie =  cookieName + "=" + id + "; " + expires + "; path=/";
}

function getCookie(name) {
    // Split cookie string and get all individual name=value pairs in an array
    var cookieArr = document.cookie.split(";");
    
    // Loop through the array elements
    for(var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split("=");
        
        /* Removing whitespace at the beginning of the cookie name
        and compare it with the given string */
        if(name == cookiePair[0].trim()) {
            // Decode the cookie value and return
            return decodeURIComponent(cookiePair[1]);
        }
    }
    
    // Return null if not found
    return null;
}

function createContact(firstName, lastName, email, phone){
    const container = document.querySelector("#results")

    const row = document.createElement("div")
    row.classList.add("row", "text-center")

    const firstNameCol = document.createElement("div")
    firstNameCol.classList.add("col")
    const firstNameNode = document.createTextNode(firstName)
    firstNameNode.id = "editFname"
    firstNameCol.appendChild(firstNameNode)

    const lastNameCol = document.createElement("div")
    lastNameCol.classList.add("col")
    const lastNameNode = document.createTextNode(lastName)
    lastNameNode.id = "editLname"
    lastNameCol.appendChild(lastNameNode)

    const emailCol = document.createElement("div")
    emailCol.classList.add("col")
    const emailNode = document.createTextNode(email)
    emailNode.id = "editEmail"
    emailCol.appendChild(emailNode)
    

    const phoneCol = document.createElement("div")
    phoneCol.classList.add("col")
    const phoneNode = document.createTextNode(phone)
    phoneNode.id = "editPhone"
    phoneCol.appendChild(phoneNode)
    

    const editCol = document.createElement("div")
    editCol.classList.add("col")
    const editTag = document.createElement("a")
    editTag.classList.add("btn", "btn-default", "btn-lg")
    editTag.type = "button"
    const itag = document.createElement("i")
    itag.classList.add("bi", "bi-pencil-square")
    editTag.appendChild(itag)
    editCol.appendChild(editTag)


    row.appendChild(firstNameCol)
    row.appendChild(lastNameCol)
    row.appendChild(emailCol)
    row.appendChild(phoneCol)

    editTag.onclick = async function(){
        const phoneEle = editTag.parentElement.previousElementSibling
        const emailEle = phoneEle.previousElementSibling
        const lastnameEle = emailEle.previousElementSibling
        const firstNameEle = lastnameEle.previousElementSibling

        try{
            const userID = getCookie("userID")
            payload = {userID: userID, firstName:firstNameEle.textContent, lastName:lastnameEle.textContent, email: emailEle.textContent, phone: phoneEle.textContent}
            const res = await axios.post(urlBase + '/loadContact' + extension, payload)

            if (res.data.error != ""){
                throw new Error(res.data.error)
            }
    
            else{
                const recordID = res.data.recordID
                const name = res.data.firstName
                const lname = res.data.lastName
                const email = res.data.email
                const phone = res.data.phone
                saveCookie("recordID", recordID)
                window.location.href = "http://primaljet.com/HTML/edit.html?name="+name+"&lname="+lname+"&email="+email+"&phone="+phone; 
            }

        }
        catch(e){
            console.log("Error happened, ugh", e)
        }
    }


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
        window.alert("Search field cannot be blank");
        return false;
    }

    try{
        const userID = getCookie("userID")
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
        const userID = getCookie("userID")
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

async function deleteContact(){

    let result = window.confirm("Are you sure you want to delete this contact?")
    if (result){
        try{
            const recordID = getCookie("recordID")
            const payload = {recordID:recordID}
            const res = await axios.post(urlBase + '/deleteContact' + extension, payload)

            if (res.data.error != ""){
                throw new Error(res.data.error)
            }

            else{
                window.location.href = "http://primaljet.com/HTML/contact.html"; 
                console.log("contact was deleted successfully")
            }
        }

        catch(e){
            console.log("Error happened ugh", e)
        }

        removeCookies(false)
    }
}

async function updateContact(){
    let firstname = document.getElementById("editFname").value;
    let lastname = document.getElementById("editLname").value;
    let email = document.getElementById("editEmail").value;
    let phone = document.getElementById("editPhone").value;

    const isRequired = value => value === '' ? false : true;

    if (!isRequired(firstname)) {
        window.alert("First name cannot be blank.");
        return false;
    }

    if (!isRequired(lastname)) {
        window.alert("Last name cannot be blank");
        return false;
    }

    if (!isRequired(email)) {
        window.alert("Email cannot be blank.");
        return false;
    }

    if (!isRequired(phone)) {
        window.alert("Phone cannot be blank");
        return false;
    }

    try{
        const recordID = getCookie("recordID")
        const payload = {recordID:recordID, firstName:firstname, lastName:lastname, email:email, phone:phone}
        const res = await axios.post(urlBase + '/updateContact' + extension, payload)

        if (res.data.error != ""){
            throw new Error(res.data.error)
        }

        else{
            window.location.href = "http://primaljet.com/HTML/contact.html"; 
            console.log("contact was updated successfully")
        }
    }

    catch(e){
        console.log("Error happened ugh", e)
    }
      
}