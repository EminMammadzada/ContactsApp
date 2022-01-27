<?php

    $inData = getRequestInfo();
	$recordID = $inData["recordID"];


    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        $stmt = $conn->prepare("SELECT * FROM Records WHERE ID=?");
		$stmt->bind_param("s", $recordID);
		$stmt->execute();

		$result = $stmt->get_result();


        //if there is such contact, then delete it
        if ($row = $result->fetch_assoc()){
            $stmt2 =  $conn->prepare("DELETE FROM Records WHERE ID=?");
            $stmt2->bind_param("s", $recordID);
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
?>