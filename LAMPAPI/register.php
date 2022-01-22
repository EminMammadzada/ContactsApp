<?php  

    //get the user entered values
    $inData = getRequestInfo();
    $firstName = $inData['firstName'];
    $lastName = $inData['lastName'];
    $login = $inData['login'];
    $password = $inData['password'];

    //establish connection with the database

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error){
        returnWithError($conn->connect_error);
    }

    else{
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Login=?");
        $stmt->bind_param("s", $login);
        $stmt->execute();

        $result = $stmt->get_result();


        //check if the login that the user chose is taken or not; give an error if the username is already taken
        //otherwise create a new user
        
        if ($row = $result->fetch_assoc()){
            returnWithError("Username is taken. Please choose another one");
        }

        else{
            $stmt2 = $conn->prepare("INSERT into Users (FirstName, LastName, Login, Password) VALUES(?,?,?,?)");
            $stmt2->bind_param("ssss", $firstName, $lastName, $login, $password);
            $stmt2->execute();


            $stmt2->close();
            returnWithError("");
        }

        $stmt->close();
        $conn->close();
    }

    function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

    function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
?>
