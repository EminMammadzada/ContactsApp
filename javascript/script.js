const form = document.getElementById('form')
const fname = document.getElementById('fname')
const lname = document.getElementById('lname')
const password = document.getElementById('password')
const errorElement = document.GetElementById('error')

form.addEventListener('submit', (e) => {
	e.preventDefault()
	let messages = []
	if(fname.value === '' || fname.value == null)
	{
		messages.push('First Name is Required.')
	}

	if(messages.length > 0)
	{
		errorElement.innerText = messages.join(', ')
	}

})