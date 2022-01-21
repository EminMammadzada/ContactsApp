<?php

    $inData = getRequestInfo();
    $id = 0;
    $firstName = "";
    $lastName = "";
    date_default_timezone_set("America/New_York");
    $currentTime = date("Y-m-d h:i:sa");

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error){
        returnWithError($conn->connect_error);
    }

    else{
        $stmt = $conn->prepare("SELECT ID, FirstName, LastName FROM Users WHERE Login=? AND Password=?");
        $login = $inData['login'];
        $password = $inData['password'];
        $stmt->bind_param("ss", $login, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()){
            returnWithInfo( $row['FirstName'], $row['LastName'], $row['ID']);
        }

        else{
            returnWithError("No records found");
        }

        $sql = "UPDATE Users SET DateLastLoggedIn=$currentTime WHERE Login=$login";
        $conn->query($sql);

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

    function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>