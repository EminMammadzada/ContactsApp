<?php


    //get the user entered values
    $inData = getRequestInfo();
    $login = $inData['login'];
    $password = $inData['password'];

    $id = 0;
    $firstName = "";
    $lastName = "";

    //establish connection with the database
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error){
        returnWithError($conn->connect_error);
    }


    else{
        $stmt = $conn->prepare("SELECT ID, FirstName, LastName FROM Users WHERE Login=? AND Password=?");
        $stmt->bind_param("ss", $login, $password);
        $stmt->execute();

        $result = $stmt->get_result();

        //if the user exists print the info; otherwise give an error message

        if ($row = $result->fetch_assoc()){
            $sql = "UPDATE Users SET DateLastLoggedIn=now() WHERE Login='$login'";
            $conn->query($sql);
            returnWithInfo( $row['FirstName'], $row['LastName'], $row['ID']);
        }

        else{
            returnWithError("No records found");
        }
        
        $stmt->close();
		$conn->close();
    }

    function getRequestInfo(){
        return json_decode(file_get_contents('php://input'), true);
    }

    function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}

    function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

    function returnWithInfo( $firstName, $lastName, $id)
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>
