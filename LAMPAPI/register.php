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


            //get the ID of the newly created user
            $stmt3 = $conn->prepare("SELECT * FROM Users WHERE Login=?");
            $stmt3->bind_param("s", $login);
            $stmt3->execute();
    
            $result2 = $stmt3->get_result();
            
            if ($row2 = $result2->fetch_assoc()){
                returnWithInfo( $row2['FirstName'], $row2['LastName'], $row2['Login'], $row2['ID']);
            }

            else{
                returnWithError("User could not be created for some reason");
            }

            $stmt3->close();

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

    function returnWithInfo( $firstName, $lastName, $login, $id)
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","login":"' . $login . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>