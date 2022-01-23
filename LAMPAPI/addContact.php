<?php

    $inData = getRequestInfo();
	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
    $email = $inData["email"];
    $phone = $inData["phone"];
    $userId = $inData["userId"];

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Id=?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();

        $result = $stmt->get_result();

        //check if we are adding contacts for registered users. If the user is not registered, do not create contacts for them

        if ($row = $result->fetch_assoc()){

			//check if the user already has a contact with given credentials. If yes, then don't add repetitive field

			$stmt2 = $conn->prepare("SELECT * FROM Records WHERE FirstName=? AND LastName=? AND Email=? AND Phone=? AND UserID=?");
			$stmt2->bind_param("sssss", $firstName, $lastName, $email, $phone, $userId);
			$stmt2->execute();

			$result2 = $stmt2->get_result();


			//there is a contact with given credentials
			if ($row2 = $result2->fetch_assoc()){
				returnWithError("You already have this contact");
			}


			//the record being added is new
			else{
				$stmt3 = $conn->prepare("INSERT into Records (UserID,FirstName,LastName,Email,Phone) VALUES(?,?,?,?,?)");
				$stmt3->bind_param("sssss", $userId, $firstName, $lastName, $email, $phone);
				$stmt3->execute();
				$stmt3->close();
				returnWithInfo( $row['FirstName'], $row['LastName'], $row['ID']);
			}
        }

        else{
            returnWithError("There is no such user. You cannot add contacts for an unexisting user");
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