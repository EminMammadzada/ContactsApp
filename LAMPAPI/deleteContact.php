<?php

    $inData = getRequestInfo();
	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
    $email = $inData["email"];
    $phone = $inData["phone"];
    $id = $inData["id"]; //logged in user id, NOT record id


    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        $stmt = $conn->prepare("SELECT * FROM Records WHERE FirstName=? AND LastName=? AND Email=? AND Phone=? AND UserID=?");
		$stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $id);
		$stmt->execute();

		$result = $stmt->get_result();


        //if there is such contact, then delete it
        if ($row = $result->fetch_assoc()){
            $stmt2 =  $conn->prepare("DELETE FROM Records WHERE FirstName=? AND LastName=? AND Email=? AND Phone=? AND UserID=?");
            $stmt2->bind_param("sssss", $firstName, $lastName, $email, $phone, $id);
		    $stmt2->execute();

            returnWithError("");
            $stmt2->close();
        }

        //if contact does not exist, then error out
        else{
            returnWithError("No such contact exists");
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

    function returnWithInfo( $firstName, $lastName, $id)
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}

?>