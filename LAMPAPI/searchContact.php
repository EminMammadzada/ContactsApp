<?php
// TODO: ensure that regular echos still fit into the json format
  /*
    User ID is cached, and the inputs array contains the first name, last name,
    email, and phone number. This endpoint searches all possible permutations of the inputs
    associated with the current user and returns the sql results as a json array
  */

  $inData = getRequestInfo();
  // user input
  $arrayOfInputs = $inData["inputs"];
  // cached data
  $userID = $inData["userID"];

  // establishes connection with mysqli, and errors out if the connection fails
  $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
  if ($conn->connect_error)
  {
    returnWithError($conn->connect_error);
  }
  // else if the sql session was opened without error
  else
  {
    $userAttemptedSQLInjection = false;

    // starts off filtering by UserID
    $command = 'SELECT * FROM Records WHERE UserID=' . $userID;

    $numInputs = sizeof($arrayOfInputs);
    $index = 0;

    // for each input, include a consideration of that input against each field
    while ($numInputs > 0)
    {
      // if the user has not attempted an SQL injection, continue to concatenate to the command
      if (!str_contains($arrayOfInputs[$index], ';'))
      {
        $command .= ' AND (FirstName LIKE "%' . $arrayOfInputs[$index] . '%" OR LastName LIKE "%' . $arrayOfInputs[$index] . '%" OR Phone LIKE "%' . $arrayOfInputs[$index] . '%" OR Email LIKE "%' . $arrayOfInputs[$index] . '%")';
        $index++;
        $numInputs--;
      }
      // if the user attempted an sql injection, break
      else
      {
        $userAttemptedSQLInjection = true;
        break;
      }
    }

    if (!$userAttemptedSQLInjection)
    {
      // executes the massive command
      $stmt = $conn->prepare($command);
      $stmt->execute();

      // stores the resulting jsons into $rows
      $result = $stmt->get_result();
      $rows = resultToArray($result);

      $rowCount = sizeof($rows);
      $index = 0;

      // echos the contents of an array of json rows
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
    // if the user attempted an sql injection
    else
    {
      youSuck();
    }
    
    $conn->close();
  }

  // takes in an SQL statement result and returns an array of the resulting rows
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

  // if the user attempts an sql injection or they just suck
  function youSuck()
  {
    echo "You Suck";
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
