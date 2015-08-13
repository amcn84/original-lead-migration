<html>
	<head>
		<title> V1 Lead Migration Utility </title>
	</head>
	<body>
	<div style="width:100%">
		<h3>Upload Lead Export</h3>
		<p>When exporting the leads from the account do not specify any additional criteria other than date range</p>
		
<?php
	$array = $fields = array(); 
	$i = 0;
	if(isset($_FILES['file'])) {
		//$handle = @fopen("v1leads.csv", "r");
		$tmp = $_FILES['file']['tmp_name'];
		$name = "temp.csv";
		$tempdir = "/";
		if(move_uploaded_file($tmp, $tempdir.$name)){ echo "<div style='width:50%;margin:0px auto;'>file uploaded<br/><a href='output.csv'>Download Now</a></div>"; }
		$csvfile = $tempdir.$name;
		$handle = fopen($csvfile, "r");
		if ($handle) {
			while (($row = fgetcsv($handle, 4096)) !== false) {
				if (empty($fields)) {
					$fields = $row;
					continue;
				}
				foreach ($row as $k=>$value) {
					$array[$i][$k] = $value;
				}
				$i++;
			}
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
		}
	}
	function convert() {
		global $array;
		$translation = array();
		$i = 0;
		foreach ($array as $record) {
			preg_match('#^(\w+\.)?\s*([\'\’\w]+)\s+([\'\’\w]+)\s*(\w+\.?)?$#', $record[5], $results);
			$translation[] = array (
				"id" => $record[0],
				"disabled" => $record[1],
				"canLogin" => $record[2],
				"receiveUpdates" => $record[3],
				"unsubscribed" => $record[4],
				"leadDisplayName" => $record[5],
				"leadFirstName" => @$results[2],
				"leadLastName" => @$results[3],
				"leadEmail" => $record[6],
				"leadEmail2" => $record[7],
				"password" => $record[8],
				"verificationCode" => $record[9],
				"leadEmailPref" => $record[10],
				"leadPhoneArea" => $record[11],
				"leadPhonePrefix" => $record[12],
				"leadPhoneSuffix" => $record[13],
				"leadAddress" => $record[14],
				"leadCity" => $record[15],
				"leadState" => $record[16],
				"leadZip" => $record[17],
				"leadSubscribeDate" => $record[18],
				"leadLoginDate" => $record[19],
				"leadUpdateDate" => $record[20],
				"leadAgentOwner" => $record[21],
				"leadCategory" => $record[22],
				"message" => $record[23],
				"messageTimestamp" => $record[24],
				"leadFlag" => $record[25],
				"agentFirstName" => $record[26],
				"agentLastName" => $record[27]
			);
		}
		$v2leads = array();
		foreach ($translation as $lead) {
			$v2leads[] = array (
				"firstName" => $lead['leadFirstName'],
				"lastName" => $lead['leadLastName'],
				"address" => $lead['leadAddress'],
				"city" => $lead['leadCity'],
				"stateProvince" => $lead['leadState'],
				"zipCode" => $lead['leadZip'],
				"phone" => "".$lead['leadPhoneArea']."-".$lead['leadPhonePrefix']."-".$lead['leadPhoneSuffix'],
				"email" => $lead['leadEmail'],
				"emailFormat" => $lead['leadEmailPref'],
				"password" => $lead['password'],
				"agentOwner" => "".$lead['agentFirstName']." ".$lead['agentLastName'],
				"notes" => $lead['message']
			);
		}
		$file = fopen('output.csv', 'w');
		$firstLineKeys = false;
		foreach ($v2leads as $line)
		{
			if (empty($firstLineKeys))
			{
				$firstLineKeys = array_keys($line);
				fputcsv($file, $firstLineKeys);
				$firstLineKeys = array_flip($firstLineKeys);
			}
			fputcsv($file, array_merge($firstLineKeys, $line),",",'"');
		}
	}
	if(isset($_POST['submit'])) {
		convert();
	}
?>
			<table width="600">
			<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">

			<tr>
			<td width="20%">Select file</td>
			<td width="80%"><input type="file" name="file" id="file" /></td>
			</tr>

			<tr>
			<td>Submit</td>
			<td><input type="submit" name="submit" /></td>
			</tr>

			</form>
			</table>
		</div>
	</body>
</html>
