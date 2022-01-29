<?php

  /*
    User ID is cached in a variable, and the inputs array contains the first name, last name,
    email, and phone number. This endpoint searches all possible permutations of the inputs
    associated with the current user and returns a
  */

  // scraped info
  $inData = getRequestInfo();
  $arrayOfInputs = $inData["inputs"];

  // session info
  //TODO: change all "id" to 'userID'
  $userID = $inData["userId"];

  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error)
  {
    returnWithError($conn->connect_error);
  }
  // else if the sql session was opened without error
  else
  {
    // One search bar need input seperated by spaces into an array, arrayOfInputs
    // length of array of inputs array is stored in $numInputs


    // sets up the start of the command
    $userAttemptedSQLInjection = false;
    $index = 0;
    $numInputs = sizeof($arrayOfInputs);

    $command = 'SELECT * FROM Records WHERE UserID=' . $userID;
    while ($numInputs > 0)
    {
      // if the user is not attempting an SQL injection at this index, continue to concatenate the massive SQL command
      if (!str_contains($arrayOfInputs[$index], ';'))
      {
        $command .= ' AND (FirstName LIKE "%' . $arrayOfInputs[$index] . '%" OR LastName LIKE "%' . $arrayOfInputs[$index] . '%" OR Phone LIKE "%' . $arrayOfInputs[$index] . '%" OR Email LIKE "%' . $arrayOfInputs[$index] . '%")';
        $index++;
        $numInputs--;
        // every index after the first should INTERSECT with the following concatenated commands
      }
      else
      {
        $userAttemptedSQLInjection = true;
        break;
      }
    }

    // if the user did not attempt an SQL injection, execute the command
    if (!$userAttemptedSQLInjection)
    {
      $stmt = $conn->prepare($command);
      $stmt->execute();
      $result = $stmt->get_result();
      $rows = resultToArray($result);

      $rowCount = sizeof($rows);
      $index = 0;
      // if there is such contact, then return its details
      echo '{"results": [';
      while ($index < $rowCount)
      {
        returnWithInfo($rows[$index]['ID'], $rows[$index]['FirstName'], $rows[$index]['LastName'], $rows[$index]['Email'], $rows[$index]['Phone']);
        $index++;
        if ($index != $rowCount)
        {
          echo ",";
        }
      }
      echo '], "error":""}';
      
      $result->free_result();
    }
    else
    {
      youSuck();
    }
    $conn->close();

  }

  function resultToArray($result)
  {
    $rows = array();
    $index = 0;
    while($row = $result->fetch_assoc())
    {
        $rows[$index] = $row;
        $index++;
    }
    return $rows;
  }

  function getRequestInfo()
  {
    return json_decode(file_get_contents('php://input'), true);
  }
  function youSuck()
  {
    echo "You Suck.";
  }

  function sendResultInfoAsJson($obj)
  {
    header('Content-type: application/json');
    echo $obj;
  }

  function returnWithError($err)
  {
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
  }

  function returnWithInfo($recordID, $firstName, $lastName, $email, $phone)
  {
    $retValue = '{"recordID": "' . $recordID . '", "firstName": "' . $firstName . '", "lastName": "' . $lastName . '", "email":"' . $email . '", "phone":"' . $phone . '"}';
    sendResultInfoAsJson($retValue);
  }
?>