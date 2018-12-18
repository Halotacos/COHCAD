<?php
/**
 Hydrid CAD/MDT - Computer Aided Dispatch / Mobile Data Terminal for use in GTA V Role-playing Communities.
 Copyright (C) 2018 - Hydrid Development Team
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 *
 */
error_reporting(0);
require '../../includes/connect.php';

include '../../includes/config.php';

session_start();
include '../../includes/isLoggedIn.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']))
	{
	header('Location: ' . $url['login'] . '');
	exit();
	}

$a = strip_tags($_GET['a']);
switch ($a)
	{
case 'get911calls':
	if (strip_tags($_GET['selfassign']))
		{
		$call_id = strip_tags($_GET['selfassign']);
		$sql = "UPDATE `911calls` SET `call_status`=:new_unit WHERE call_id=:call_id";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':new_unit', $_SESSION['identifier']);
		$stmt->bindValue(':call_id', $call_id);
		$selfassign = $stmt->execute();
		header('Location: ../../leo-index.php');
		exit();
		}

	if (strip_tags($_GET['endcall']))
		{
		$call_id = strip_tags($_GET['endcall']);
		$stmt = $pdo->prepare("DELETE FROM 911calls WHERE call_id =:call_id");
		$stmt->bindParam(':call_id', $call_id);
		$stmt->execute();
		logAction('(LEO) Ended Call #' . $call_id . '', $user_username . ' / ' . $_SESSION['identifier']);
		header('Location: ../../leo-index.php');
		exit();
		}

	$my_id = $_SESSION['identifier'];
	$stmt = $pdo->prepare("SELECT * FROM 911calls WHERE call_status LIKE '%$my_id%'");
	$stmt->execute();
	$callRow = $stmt->fetch(PDO::FETCH_ASSOC);
	if (empty($callRow['call_id']))
		{
		echo "<h5 style='margin-top:20px; color:white;'>NO ASSIGNED CALLS</h5>";
		}
	  else
		{
		echo "<h5 style='margin-top:20px; color:white;'>MY CALLS</h5>
      <div style='overflow-y: scroll; height:200px;'>
      <table style='border: 1px solid black;'>
  <tr>
  <th><center>Description</center></th>
  <th><center>Location</center></th>
  <th><center>Postal</center></th>
  <th><center>ASSIGNED UNITS</center></th>
  </tr>";
		$get911calls = "SELECT * FROM 911calls WHERE call_status LIKE '%$my_id%'";
		$result = $pdo->prepare($get911calls);
		$result->execute();
		while ($row = $result->fetch(PDO::FETCH_ASSOC))
			{
			echo "<tr>";
			echo "<td style='max-width:200px;'><center>" . $row['call_description'] . "</center></td>";
			echo "<td><center>" . $row['call_location'] . '/' . $row['call_crossstreat'] . "</center></td>";
			echo "<td><center>" . $row['call_postal'] . "</center></td>";
			echo "<td><center>" . $row['call_status'] . "</center></td>";
			echo "</tr>";
			}

		echo "</table></div>";
		}

	break;

case 'get911callsDispatch':
	if ($_SESSION['is_dispatch'] === "No")
		{
		header('Location: ' . $url['index'] . '?np=dispatch');
		exit();
		}

	if ($_GET['endcall'])
		{
		$call_id = strip_tags($_GET['endcall']);
		$stmt = $pdo->prepare("DELETE FROM 911calls WHERE call_id =:call_id");
		$stmt->bindParam(':call_id', $call_id);
		$stmt->execute();
		logAction('(DISPATCH) Ended Call #' . $call_id . '', $user_username . ' / ' . $_SESSION['identifier']);
		header('Location: ../../dispatch-index.php');
		exit();
		}

	$stmt = $pdo->prepare("SELECT * FROM 911calls");
	$stmt->execute();
	$callRow = $stmt->fetch(PDO::FETCH_ASSOC);
	if (empty($callRow['call_id']))
		{
		echo "<h5 style='margin-top:20px; color:white;'>NO ACTIVE CALLS</h5>";
		}
	  else
		{
		echo "<h5 style='margin-top:20px; color:white;'>ACTIVE CALLS</h5><table style='border: 1px solid black;'>
  <tr>
  <th><center>Description</center></th>
  <th><center>Location</center></th>
  <th><center>Postal</center></th>
  <th><center>Assigned To</center></th>
  <th><center>Available Units</center></th>
  <th><center></center></th>
  </tr>";
		$get911calls = "SELECT * FROM 911calls";
		$result = $pdo->prepare($get911calls);
		$result->execute();
		while ($row = $result->fetch(PDO::FETCH_ASSOC))
			{
			$call_id_test = $row['call_id'];
			$call_desc_test = $row['call_description'];
			$call_location_test = $row['call_location'];
			$call_crossstreat_test = $row['call_crossstreat'];
			$call_postal_test = $row['call_postal'];
			$call_status_test = $row['call_status'];
			$call_units_test = $row['call_status'];
			echo "<tr>";
			echo "<td style='max-width:200px;'><center>" . $call_desc_test . "</center></td>";
			echo "<td><center>" . $call_location_test . '/' . $call_crossstreat_test . "</center></td>";
			echo "<td><center>" . $call_postal_test . "</center></td>";
			echo "<td><center>" . $call_units_test . "</center></td>";
			echo "<td><center><select name='assignCall' style='width:150px;' class='select-units' id='" . $call_id_test . "' onChange='assignCall(this)'>
                <option selected='true' disabled='disabled'>Available Units</option>";
			$getavaUnits = "SELECT * FROM on_duty WHERE status='10-8' OR status='On-Duty'";
			$result2 = $pdo->prepare($getavaUnits);
			$result2->execute();
			while ($row2 = $result2->fetch(PDO::FETCH_ASSOC))
				{
				echo "<option value='" . $row2['identifier'] . "'>" . $row2['identifier'] . "</option>";
				}

			echo "</select></center></td>";
			echo '<td><a class="btn btn-danger btn-sm" href="functions/leo/api.php?a=get911callsDispatch&endcall=' . $call_id_test . '" data-title="Self Assign">End Call</a></td>';
			echo "</tr>";
			}

		echo "</table>";
		}

	break;

case 'assignCall':
	$unit = strip_tags($_GET['unit']);
	$id = strip_tags($_GET['id']);

	// blah

	$sql1 = "SELECT call_status FROM 911calls WHERE call_id = :id";
	$stmt1 = $pdo->prepare($sql1);
	$stmt1->bindValue(':id', $id);
	$stmt1->execute();
	$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
	$current_units = $row1['call_status'];

	//

	if ($current_units === "NOT ASSIGNED")
		{
		$sql = "UPDATE `911calls` SET `call_status`=:unit WHERE `call_id`=:id";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':unit', $unit);
		$stmt->bindValue(':id', $id);
		$assignCallExecute = $stmt->execute();
		$sql2 = "UPDATE `on_duty` SET `status`='10-6 / Call Assigned' WHERE `identifier`=:unit";
		$stmt2 = $pdo->prepare($sql2);
		$stmt2->bindValue(':unit', $unit);
		$stmt2->bindValue(':id', $id);
		$assignCallExecute = $stmt2->execute();
		}
	  else
		{
		$test = $current_units . ', ' . $unit;
		$sql = "UPDATE `911calls` SET `call_status`=:unit WHERE `call_id`=:id";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':unit', $test);
		$stmt->bindValue(':id', $id);
		$assignCallExecute = $stmt->execute();
		$sql2 = "UPDATE `on_duty` SET `status`='10-6 / Call Assigned' WHERE `identifier`=:unit";
		$stmt2 = $pdo->prepare($sql2);
		$stmt2->bindValue(':unit', $unit);
		$stmt2->bindValue(':id', $id);
		$assignCallExecute = $stmt2->execute();
		}

	break;

case 'dynamicTime':
	echo $time;
	break;

case 'endShift':
	$i = $_SESSION['identifier'];
	$sql = "DELETE FROM `on_duty` WHERE identifier = :i";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':i', $i);
	$endShift = $stmt->execute();
	logAction('(LEO) Ended Shift', $user_username . ' / ' . $_SESSION['identifier']);
	if ($endShift)
		{
		header('Location: ../../' . $url['index'] . '');
		}

	break;

case 'getActiveUnits':
	echo "<table>";
	$getActiveUnits = "SELECT * FROM on_duty";
	$result = $pdo->prepare($getActiveUnits);
	$result->execute();
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
		{
		if ($row['status'] !== "10-42")
			{
			echo "<tr>";
			echo "<td><center>" . $row['identifier'] . "</center></td>";
			echo "<td><center>" . $row['status'] . "</center></td>";
			echo "<td><center>" . $row['type'] . "</center></td>";
			echo "</tr>";
			}
		}

	echo "</table>";
	break;

case 'getActiveUnitsDispatch':
	if ($_SESSION['is_dispatch'] === "No")
		{
		header('Location: ' . $url['index'] . '?np=dispatch');
		exit();
		}

	$stmt = $pdo->prepare("SELECT * FROM on_duty where `type` = 'LEO' or `type` = 'FIRE/EMS'");
	$stmt->execute();
	$activeUnitsRow = $stmt->fetch(PDO::FETCH_ASSOC);
	if (empty($activeUnitsRow['id']))
		{
		echo "<h5 style='margin-top:20px; color:white;'>NO ACTIVE UNITS</h5>";
		}
	  else
		{
		echo "
            <tr>
            <th><center>Identifier</center></th>
            <th><center>Type</center></th>
            <th><center>Status</center></th>
            </tr>";
		$getActiveUnits = "SELECT * FROM on_duty where `type` = 'LEO' or `type` = 'FIRE/EMS'";
		$result = $pdo->prepare($getActiveUnits);
		$result->execute();
		while ($row = $result->fetch(PDO::FETCH_ASSOC))
			{
			echo "<tr>";
			echo "<td><center>" . $row['identifier'] . "</center></td>";
			echo "<td><center>" . $row['type'] . "</center></td>";
			echo "<td><center><select style='width:150px;' name='updateUnitStatus' id='" . $row['id'] . "' class='select-units' onChange='updateUnitStatus(this)'>
                            <option selected='true' disabled='disabled'>" . $row['status'] . "</option>";
			if ($row['type'] === "FIRE/EMS")
				{
				echo "<option value='On-Duty'>On-Duty</option>
                              <option value='Off-Duty'>Off-Duty</option>";
				}
			  else
				{
				echo "<option value='10-6'>10-6</option>
                              <option value='10-7'>10-7</option>
                              <option value='10-8'>10-8</option>
                              <option value='10-15'>10-15</option>
                              <option value='10-23'>10-23</option>
                              <option value='10-97'>10-97</option>
                              <option disabled='disabled'>-----</option>
                              <option value='10-42'>10-42</option>";
				}

			"</select></center></td>";
			echo "</tr>";
			}
		}

	break;

case 'getAOP':
	$stmt = $pdo->prepare("SELECT * FROM settings");
	$stmt->execute();
	$status_row = $stmt->fetch(PDO::FETCH_ASSOC);
	$status_row = $status_row['aop'];
	$status = "$status_row";
	echo "AOP: " . '' . $status;
	break;

case 'getBolos':
	if ($_GET['deletebolo'])
		{
		$bolo_id = strip_tags($_GET['deletebolo']);
		$stmt = $pdo->prepare("DELETE FROM bolos WHERE bolo_id =:bolo_id");
		$stmt->bindParam(':bolo_id', $bolo_id);
		$stmt->execute();
		logAction('(LEO) Deleted BOLO #' . $bolo_id . '', $user_username . ' / ' . $_SESSION['identifier']);
		header('Location: ../../leo-index.php');
		exit();
		}

	$stmt = $pdo->prepare("SELECT * FROM bolos");
	$stmt->execute();
	$bolosRows = $stmt->fetch(PDO::FETCH_ASSOC);
	if (empty($bolosRows['bolo_id']))
		{
		echo "<h5 style='margin-top:20px; color:white;'>NO ACTIVE BOLOS</h5>";
		}
	  else
		{
		echo "<h5 style='margin-top:20px; color:white;'>BOLOS</h5>
                      <div style='overflow-y: scroll; height:200px;'>
                      <table style='border: 1px solid black;'>
                  <tr>
                  <th><center>Reason</center></th>
                  <th><center>Plate</center></th>
                  <th><center>Color</center></th>
                  <th><center>Model</center></th>
                  <th><center></center></th>
                  </tr>";
		$getBolos = "SELECT * FROM bolos";
		$result = $pdo->prepare($getBolos);
		$result->execute();
		while ($row = $result->fetch(PDO::FETCH_ASSOC))
			{
			echo "<tr>";
			echo "<td><center>" . $row['bolo_reason'] . "</center></td>";
			echo "<td><center>" . $row['vehicle_plate'] . "</center></td>";
			echo "<td><center>" . $row['vehicle_color'] . "</center></td>";
			echo "<td><center>" . $row['vehicle_model'] . "</center></td>";
			echo '<td><a class="btn btn-danger btn-sm" href="functions/leo/api.php?a=getBolos&deletebolo=' . $row['bolo_id'] . '" data-title="Delete"><i class="fas fa-minus-circle"></i></a></td>';
			echo "</tr>";
			}

		echo "</table></div>";
		}

	break;

case 'getBolosDispatch':
	if ($_GET['deletebolo'])
		{
		$bolo_id = strip_tags($_GET['deletebolo']);
		$stmt = $pdo->prepare("DELETE FROM bolos WHERE bolo_id =:bolo_id");
		$stmt->bindParam(':bolo_id', $bolo_id);
		$stmt->execute();
		logAction('(DISPATCH) Deleted BOLO #' . $bolo_id . '', $user_username . ' / ' . $_SESSION['identifier']);
		header('Location: ../../dispatch-index.php');
		exit();
		}

	$stmt = $pdo->prepare("SELECT * FROM bolos");
	$stmt->execute();
	$bolosRows = $stmt->fetch(PDO::FETCH_ASSOC);
	if (empty($bolosRows['bolo_id']))
		{
		echo "<h5 style='margin-top:20px; color:white;'>NO ACTIVE BOLOS</h5>";
		}
	  else
		{
		echo "<h5 style='margin-top:20px; color:white;'>BOLOS</h5>
                        <div style='overflow-y: scroll; height:200px;'>
                        <table style='border: 1px solid black;'>
                    <tr>
                    <th><center>Reason</center></th>
                    <th><center>Plate</center></th>
                    <th><center>Color</center></th>
                    <th><center>Model</center></th>
                    <th><center></center></th>
                    </tr>";
		$getBolos = "SELECT * FROM bolos";
		$result = $pdo->prepare($getBolos);
		$result->execute();
		while ($row = $result->fetch(PDO::FETCH_ASSOC))
			{
			echo "<tr>";
			echo "<td><center>" . $row['bolo_reason'] . "</center></td>";
			echo "<td><center>" . $row['vehicle_plate'] . "</center></td>";
			echo "<td><center>" . $row['vehicle_color'] . "</center></td>";
			echo "<td><center>" . $row['vehicle_model'] . "</center></td>";
			echo '<td><a class="btn btn-danger btn-sm" href="functions/leo/api.php?a=getBolosDispatch&deletebolo=' . $row['bolo_id'] . '" data-title="Delete"><i class="fas fa-minus-circle"></i></a></td>';
			echo "</tr>";
			}

		echo "</table></div>";
		}

	break;

case 'getPendingIds':
	if ($_SESSION['leo_supervisor'] === "No")
		{
		header('Location: ' . $url['leo_index'] . '');
		exit();
		}

	if ($_GET['decline'])
		{
		$identity_id = strip_tags($_GET['decline']);
		$stmt = $pdo->prepare("DELETE FROM identities WHERE identity_id =:identity_id");
		$stmt->bindParam(':identity_id', $identity_id);
		$stmt->execute();
		logAction('(LEO) Declined New Identity (' . $identity_id . ')', $user_username . ' / ' . $_SESSION['identifier']);
		header('Location: ../../' . $url['leo_supervisor_view_pending_identities'] . '');
		}
	elseif ($_GET['approve'])
		{
		$identity_id = strip_tags($_GET['approve']);
		$approved_status = "Active";
		$sql2 = "UPDATE `identities` SET `status`=:approved WHERE identity_id=:identity_id";
		$stmt2 = $pdo->prepare($sql2);
		$stmt2->bindParam(':approved', $approved_status);
		$stmt2->bindParam(':identity_id', $identity_id);
		$stmt2->execute();
		logAction('(LEO) Approved New Identity (' . $identity_id . ')', $user_username . ' / ' . $_SESSION['identifier']);
		header('Location: ../../' . $url['leo_supervisor_view_pending_identities'] . '');
		}

	$pending_status = "Approval Needed";
	$stmt = $pdo->prepare("SELECT * FROM identities WHERE status=:pending_status");
	$stmt->bindValue(':pending_status', $pending_status);
	$stmt->execute();
	$idRows = $stmt->fetch(PDO::FETCH_ASSOC);
	if (empty($idRows['identity_id']))
		{
		echo "<h5 style='margin-top:20px; color:white;'>NO PENDING IDENTIFIERS</h5>";
		}
	  else
		{
		echo "<h5 style='margin-top:20px; color:white;'>PENDING IDENTIFIERS</h5><table style='border: 1px solid black;'>
                      <tr>
                      <th><center>Identifier</center></th>
                      <th><center>Owner</center></th>
                      <th><center>Approve</center></th>
                      <th><center>Decline</center></th>
                      </tr>";
		$pending_status = "Approval Needed";
		$stmt2 = $pdo->prepare("SELECT * FROM identities WHERE status=:pending_status");
		$stmt2->bindValue(':pending_status', $pending_status);
		$stmt2->execute();
		while ($row = $stmt2->fetch(PDO::FETCH_ASSOC))
			{
			echo "<tr>";
			echo "<td><center>" . $row['identifier'] . "</center></td>";
			echo "<td><center>" . $row['user_name'] . "</center></td>";
			echo '<td><a class="btn btn-success btn-sm" href="functions/leo/api.php?a=getPendingIds&approve=' . $row['identity_id'] . '" data-title="Approve"><i class="fas fa-check"></i></a></td>';
			echo '<td><a class="btn btn-danger btn-sm" href="functions/leo/api.php?a=getPendingIds&decline=' . $row['identity_id'] . '" data-title="Decline"><i class="fas fa-minus"></i></a></td>';
			echo "</tr>";
			}

		echo "</table>";
		}

	break;

case 'getStatus':
	$i = $_SESSION['identifier'];
	$stmt = $pdo->prepare("SELECT * FROM on_duty WHERE identifier=:i");
	$stmt->bindValue(':i', $i);
	$stmt->execute();
	$status_row = $stmt->fetch(PDO::FETCH_ASSOC);
	$status_row = $status_row['status'];
	$status = "$status_row";
	if ($_SESSION['on_duty'] === "No")
		{
		if ($_SESSION['sub_division'] !== "None")
			{
			echo "Current Status: 10-42" . ' / ' . $_SESSION['sub_division'];
			}
		  else
			{
			echo "Current Status: 10-42";
			}
		}
	  else
		{
		echo "Current Status: " . '' . $status;
		}

	break;

case 'searchName':
	$q = strip_tags($_GET['q']);
	$getChar = "SELECT * FROM characters WHERE character_id='$q'";
	$result = $pdo->prepare($getChar);
	$result->execute();
	logAction('(LEO) Searched Character (' . $q . ')', $user_username . ' / ' . $_SESSION['identifier']);
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
		{
		$suspect_name = $row['first_name'] . ' ' . $row['last_name'];
		$charid = $row['character_id'];
		echo '<div class="float-right">';
		echo '<div style="border: 1px solid black; overflow-y: scroll; width:500px; height:150px;">';
		echo "<center>PREVIOUS TICKETS</center>";
		echo "<table>
                            <tr>
                            <th><center>Ticket_ID</center></th>
                            <th><center>Reason</center></th>
                            <th><center>Postal</center></th>
                            <th><center>Timestamp</center></th>
                            </tr>";
		$getPreviousTickets = "SELECT * FROM tickets WHERE suspect = '$charid'";
		$result = $pdo->prepare($getPreviousTickets);
		$result->execute();
		while ($row = $result->fetch(PDO::FETCH_ASSOC))
			{
			echo "<tr>";
			echo "<td><center>" . $row['ticket_id'] . "</center></td>";
			echo "<td><center>" . $row['reasons'] . "</center></td>";
			echo "<td><center>" . $row['postal'] . "</center></td>";
			echo "<td><center>" . $row['timestamp'] . "</center></td>";
			echo "</tr>";
			}

		echo "</table>";
		echo '</div><br-lookup-break-tables>';
		echo '<div style="border: 1px solid black; overflow-y: scroll; width:500px; height:150px;">';
		echo "<center>PREVIOUS ARRESTS</center>";
		echo "<table>
                            <tr>
                            <th><center>Arrest_ID</center></th>
                            <th><center>Charges</center></th>
                            <th><center>Arresting Officer</center></th>
                            <th><center>Timestamp</center></th>
                            </tr>";
		$getPreviousTickets = "SELECT * FROM arrest_reports WHERE suspect = '$charid'";
		$result = $pdo->prepare($getPreviousTickets);
		$result->execute();
		while ($row = $result->fetch(PDO::FETCH_ASSOC))
			{
			echo "<tr>";
			echo "<td><center>" . $row['arrest_id'] . "</center></td>";
			echo "<td><center>" . $row['summary'] . "</center></td>";
			echo "<td><center>" . $row['arresting_officer'] . "</center></td>";
			echo "<td><center>" . $row['timestamp'] . "</center></td>";
			echo "</tr>";
			}

		echo "</table>";
		echo '</div>';
		echo '</div>';
		}

	$q = strip_tags($_GET['q']);
	$getChar = "SELECT * FROM characters WHERE character_id='$q'";
	$result = $pdo->prepare($getChar);
	$result->execute();
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
		{
		echo "<h6>Name: " . $row['first_name'] . " " . $row['last_name'] . "</h6><br-leo-name-search>";
		echo "<h6>Date Of Birth: " . $row['date_of_birth'] . "</h6><br-leo-name-search>";
		echo "<h6>Sex: " . $row['sex'] . "</h6><br-leo-name-search>";
		echo "<h6>Address: " . $row['address'] . "</h6><br-leo-name-search>";
		echo "<h6>Height / Weight: " . $row['height'] . " / " . $row['weight'] . "</h6><br-leo-name-search>";
		echo "<h6>Eye Color / Hair Color: " . $row['eye_color'] . " / " . $row['hair_color'] . "</h6><br-leo-name-search>";
		echo "<h6>Blood Type: " . $row['blood_type'] . "</h6><br-leo-name-search>";
		echo "<hr>";
		echo '<h6>Drivers License: ' . $row['license_driver'] . '</h6><br-leo-name-search>';
		echo "<h6>Firearms License: " . $row['license_firearm'] . "</h6><br-leo-name-search>";
		echo '<a class="btn btn-danger btn-sm" href="functions/leo/api.php?a=searchName&suspendlicense=' . $row['character_id'] . '" data-title="Delete">Suspend Drivers License</a>';
		echo "<hr>";
		echo "<div class='float-left'";
		echo "<h6 style='color:black;'>WARRANTS</h6>";
		echo "<table>";
		$person = $row['first_name'] . " " . $row['last_name'];
		$wanted_status = "WANTED";
		$getWpn = "SELECT * FROM warrants WHERE wanted_person='$person' AND wanted_status='$wanted_status'";
		$result = $pdo->prepare($getWpn);
		$result->execute();
		while ($row = $result->fetch(PDO::FETCH_ASSOC))
			{
			echo "<tr>";
			echo "<td><center><font color='red'>" . $row['reason'] . "</font></center></td>";
			echo "<td><center><font color='red'>" . $row['issued_on'] . "</font></center></td>";
			if ($_SESSION['leo_supervisor'] === "Yes")
				{
				echo '<td><a class="btn btn-danger btn-sm" href="functions/leo/api.php?a=searchName&deletewarrant=' . $row['warrant_id'] . '" data-title="Delete"><i class="fas fa-minus-circle"></i></a></td>';
				}

			echo "</tr>";
			}

		echo "</table>";
		echo "</div>";
		}

	if ($_GET['suspendlicense'])
		{
		$char_id = strip_tags($_GET['suspendlicense']);
		$license_status = "Suspended";
		$sql = "UPDATE `characters` SET `license_driver`=:license_status WHERE character_id=:char_id";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':char_id', $char_id);
		$stmt->bindValue(':license_status', $license_status);
		$stmt->execute();
		if ($_SESSION['is_dispatch'] === "No")
			{
			header('Location: ../../leo-index.php?license=suspended');
			logAction('(LEO) Suspended License (' . $char_id . ')', $user_username . ' / ' . $_SESSION['identifier']);
			}
		  else
			{
			header('Location: ../../dispatch-index.php?license=suspended');
			logAction('(DISPATCH) Suspended License (' . $char_id . ')', $user_username . ' / ' . $_SESSION['identifier']);
			}

		exit();
		}

	if ($_GET['deletewarrant'])
		{
		$warrant_id = strip_tags($_GET['deletewarrant']);
		$stmt = $pdo->prepare("DELETE FROM warrants WHERE warrant_id =:warrant_id");
		$stmt->bindParam(':warrant_id', $warrant_id);
		$stmt->execute();
		logAction('(LEO) Deleted Warrant (' . $warrant_id . ')', $user_username . ' / ' . $_SESSION['identifier']);
		header('Location: ../../leo-index.php');
		exit();
		}

	break;

case 'searchNameAc':
	$status = 'Enabled';
	$getChars = "SELECT * FROM characters WHERE status='$status'";
	$result = $pdo->prepare($getChars);
	$result->execute();
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
		{
		echo '<option value="' . $row['character_id'] . '">' . $row['first_name'] . ' ' . $row['last_name'] . ' // ' . $row['date_of_birth'] . '</option>';
		}

	break;

case 'searchVehicleAc':
	$status = 'Enabled';
	$getVeh = "SELECT * FROM vehicles WHERE vehicle_status='$status'";
	$result = $pdo->prepare($getVeh);
	$result->execute();
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
		{
		echo '<option value="' . $row['vehicle_id'] . '">' . $row['vehicle_vin'] . ' - ' . $row['vehicle_plate'] . ' - ' . $row['vehicle_model'] . '</option>';
		}

	break;

case 'searchWeaponAc':
	$status = 'Enabled';
	$getWpn = "SELECT * FROM weapons WHERE wpn_status='$status'";
	$result = $pdo->prepare($getWpn);
	$result->execute();
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
		{
		echo '<option value="' . $row['wpn_id'] . '">' . $row['wpn_type'] . ' - ' . $row['wpn_serial'] . ' - ' . $row['wpn_ownername'] . '</option>';
		}

	break;

case 'searchVeh':
	$q = strip_tags($_GET['q']);
	$getVeh = "SELECT * FROM vehicles WHERE vehicle_id='$q'";
	$result = $pdo->prepare($getVeh);
	$result->execute();
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
		{
		echo "<h6>Plate: " . $row['vehicle_plate'] . "</h6><br-leo-name-search>";
		echo "<h6>Color: " . $row['vehicle_color'] . "</h6><br-leo-name-search>";
		echo "<h6>Model: " . $row['vehicle_model'] . "</h6><br-leo-name-search>";
		echo "<h6>Insurance Status: " . $row['vehicle_is'] . "</h6><br-leo-name-search>";
		echo "<h6>Registration Status: " . $row['vehicle_rs'] . "</h6><br-leo-name-search>";
		echo "<h6>VIN: " . $row['vehicle_vin'] . "</h6><br-leo-name-search>";
		echo "<h6>Owner: " . $row['vehicle_ownername'] . "</h6><br-leo-name-search>";
		$plate = $row['vehicle_plate'];
		$stmt = $pdo->prepare("SELECT * FROM bolos WHERE vehicle_plate =:veh_plate");
		$stmt->bindParam(':veh_plate', $row['vehicle_plate']);
		$stmt->execute();
		$bolosRows = $stmt->fetch(PDO::FETCH_ASSOC);
		if (empty($bolosRows['bolo_id']))
			{
			echo "<hr><h6>No Bolos On Vehicle</h6>";
			}
		  else
			{
			$getVehBolo = "SELECT * FROM bolos WHERE vehicle_plate=:plate";
			$result = $pdo->prepare($getVehBolo);
			$stmt->bindValue(':plate', $plate);
			$result->execute();
			while ($row = $result->fetch(PDO::FETCH_ASSOC))
				{
				echo "<table>";
				echo "<tr>";
				echo "<td><center><font color='red'>" . $row['bolo_reason'] . "</font></center></td>";
				echo "<td><center><font color='red'>" . $row['bolo_created_on'] . "</font></center></td>";
				echo "</tr>";
				}

			echo "</table>";
			}
		}

	break;

case 'searchWpns':
	$q = strip_tags($_GET['q']);
	echo "<table>
                                  <tr>
                                  <th><center>Type</center></th>
                                  <th><center>Serial</center></th>
                                  <th><center>Status</center></th>
                                  <th><center>Owner</center></th>
                                  </tr>";
	$getWpn = "SELECT * FROM weapons WHERE wpn_id='$q'";
	$result = $pdo->prepare($getWpn);
	$result->execute();
	while ($row = $result->fetch(PDO::FETCH_ASSOC))
		{
		echo "<tr>";
		echo "<td><center>" . $row['wpn_type'] . "</center></td>";
		echo "<td><center>" . $row['wpn_serial'] . "</center></td>";
		echo "<td><center>" . $row['wpn_rpstatus'] . "</center></td>";
		echo "<td><center>" . $row['wpn_ownername'] . "</center></td>";
		echo "</tr>";
		}

	echo "</table>";
	break;

case 'setAOP':
	$q = strip_tags($_GET['q']);
	$sql = "UPDATE `settings` SET `aop`=:q";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':q', $q);
	$updateAOP = $stmt->execute();
	logAction('(LEO) Updated AOP', $user_username . ' / ' . $_SESSION['identifier']);
	break;

case 'setStatus':
	$q = strip_tags($_GET['q']);
	$unit = $_SESSION['identifier'];
	$sql = "UPDATE `on_duty` SET `status`=:q WHERE `identifier`=:unit";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':q', $q);
	$stmt->bindValue(':unit', $unit);
	$updateStatus = $stmt->execute();
	break;

case 'UpdateUnitStatus':
	if ($_SESSION['is_dispatch'] === "No")
		{
		header('Location: ' . $url['index'] . '?np=dispatch');
		exit();
		}

	$q = strip_tags($_GET['q']);
	$i = strip_tags($_GET['i']);
	if ($q === "10-42" || $q === "Off-Duty")
		{
		$sql = "DELETE FROM `on_duty` WHERE id = :i";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':i', $i);
		$endShift = $stmt->execute();
		exit();
		}
	  else
		{
		$sql = "UPDATE `on_duty` SET `status`=:q WHERE `id`=:i";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':i', $i);
		$stmt->bindValue(':q', $q);
		$updateStatus = $stmt->execute();
		exit();
		}

	break;

case 'setSubDivision':
	$sd = strip_tags($_GET['sd']);
	$_SESSION['sub_division'] = $sd;
	break;

case 'updateNotepad':
	$update = strip_tags($_GET['txt']);
	$_SESSION['notepad'] = $update;
	break;

case 'getStatusFire':
	$i = $_SESSION['current_station'];
	$stmt = $pdo->prepare("SELECT * FROM on_duty WHERE identifier='$i'");
	$stmt->execute();
	$status_row = $stmt->fetch(PDO::FETCH_ASSOC);
	$status_row = $status_row['status'];
	$status = "$status_row";
	if ($_SESSION['on_duty'] === "Yes")
		{
		echo "Current Status: On-Duty";
		}
	  else
		{
		echo "Current Status: Off-Duty";
		}

	break;

case 'setFireStation':
	$q = strip_tags($_GET['q']);
	$_SESSION['current_station'] = $q;
	$sql = "INSERT INTO on_duty (identifier, status, type) VALUES (:identifier, :status, :type)";
	$duty_type = "FIRE/EMS";
	$status = "On-Duty";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':identifier', $q);
	$stmt->bindValue(':status', $status);
	$stmt->bindValue(':type', $duty_type);
	$result = $stmt->execute();
	$_SESSION['on_duty'] = "Yes";
	break;

case 'updateFireStation':
	$q = strip_tags($_GET['q']);
	$od = $_SESSION['current_station'];
	$sql = "UPDATE `on_duty` SET `identifier`='$q' WHERE `identifier`='$od'";
	$stmt = $pdo->prepare($sql);
	$updateStatus = $stmt->execute();
	$_SESSION['current_station'] = $q;
	break;

case 'get911callsFire':
	$my_id = $_SESSION['current_station'];
	$stmt = $pdo->prepare("SELECT * FROM 911calls WHERE call_status LIKE '$my_id%'");
	$stmt->execute();
	$callRow = $stmt->fetch(PDO::FETCH_ASSOC);
	if (empty($callRow['call_id']))
		{
		echo "<h5 style='margin-top:20px; color:white;'>NO ASSIGNED CALLS</h5>";
		}
	  else
		{
		echo "<h5 style='margin-top:20px; color:white;'>MY CALLS</h5>
                                                          <div style='overflow-y: scroll; height:200px;'>
                                                          <table style='border: 1px solid black;'>
                                                      <tr>
                                                      <th><center>Description</center></th>
                                                      <th><center>Location</center></th>
                                                      <th><center>Postal</center></th>
                                                      <th><center>ASSIGNED UNITS</center></th>
                                                      </tr>";
		$get911calls = "SELECT * FROM 911calls";
		$result = $pdo->prepare($get911calls);
		$result->execute();
		while ($row = $result->fetch(PDO::FETCH_ASSOC))
			{
			echo "<tr>";
			echo "<td style='max-width:200px;'><center>" . $row['call_description'] . "</center></td>";
			echo "<td><center>" . $row['call_location'] . '/' . $row['call_crossstreat'] . "</center></td>";
			echo "<td><center>" . $row['call_postal'] . "</center></td>";
			echo "<td><center>" . $row['call_status'] . "</center></td>";
			echo "</tr>";
			}

		echo "</table></div>";
		}

}
