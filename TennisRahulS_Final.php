<!DOCTYPE html>
<html>
    <head>
        <title>ATP Tennis ++</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <style type="text/css">
        .container {
            width: 300px;
            text-align: left;
        }
        .container input {
            width: 100%;
            text-align: center;
        }
        </style>
    </head>

    <body>
        <h1 style="color:blue;" >ATP Random Draw and New Features</h1>
        <div class="container">
        <form method="post" action="">
            <label for="l1">Number of ATP Players</label>
            <select name="pcount">
                <option value="128">128</option>
                <option value="32">32</option>
            </select>
            <input type="submit" name="Generate" value="Generate Draw" />
            <br />
            <label for="l2">GrandSlam Wins by Age</label>
            <select name="pgAge">
                <option value="0">All</option>
                <option value="1">15-20</option>
                <option value="2">21-25</option>
                <option value="3">26-30</option>
                <option value="4">31-35</option>
                <option value="5">36-40</option>
            </select>
            <input type="submit" name="GSAg" value="Submit" />
            <br />
            <label for="l3">GrandSlam Wins by Rank</label>
            <select name="pgRank">
                <option value="0">All</option>
                <option value="1">1-5</option>
                <option value="2">6-10</option>
                <option value="3">11-15</option>
                <option value="4">16-20</option>
                <option value="5">21-128</option>
            </select>
            <input type="submit" name="GSRk" value="Submit" />
            <br />
            <label for="l4">ATP1000 Wins by Age</label>
            <select name="pa1000Age">
                <option value="0">All</option>
                <option value="1">15-20</option>
                <option value="2">21-25</option>
                <option value="3">26-30</option>
                <option value="4">31-35</option>
                <option value="5">36-40</option>
            </select>
            <input type="submit" name="a1000Ag" value="Submit" />
            <br />
            <label for="l4">ATP1000 Wins by Rank</label>
            <select name="pa1000Rank">
                <option value="0">All</option>
                <option value="1">1-5</option>
                <option value="2">6-10</option>
                <option value="3">11-15</option>
                <option value="4">16-20</option>
                <option value="5">21-128</option>
            </select>
            <input type="submit" name="a1000Rk" value="Submit" />
            <br />
        </form>
        </div>

    <?php

   //==========================================
   //Connect to DB and get data
   //==========================================

   $ini_array = parse_ini_file("TennisRahul.ini.php");
   $db_host_name = $ini_array['db_host_name']; 
   $db_name = $ini_array['db_name'];
   $db_user_name = $ini_array['db_user_name'];
   $db_user_pwd = $ini_array['db_user_pwd'];

   $players = array(); // Array of players ordered based on their ATP points
   $leftArr = array_fill(0, 64, 0); // Array of the seeded player ranking based on the draw
   $rightArr = array_fill(0, 64, 0);// Array of unseeded player ranking in random order
  
   // Setting defaults when user hits the submit button without setting a value
   if (isset($_POST['pcount']))
   {
	   $pc = $_POST['pcount'];
   }
   else
   {
	   $pc = 128;
   }
   if (isset($_POST['pgAge']))
   {
	   $pAge = $_POST['pgAge'];
   }
   else
   {
	   $pAge = 0;
   }
   if (isset($_POST['pgRank']))
   {
	   $pRank = $_POST['pgRank'];
   }
   else
   {
	   $pRank = 0;
   }
   if (isset($_POST['pa1000Age']))
   {
	   $p1000Age = $_POST['pa1000Age'];
   }
   else
   {
	   $p1000Age = 0;
   }
   if (isset($_POST['pa1000Rank']))
   {
	   $p1000Rank = $_POST['pa1000Rank'];
   }
   else
   {
	   $p1000Rank = 0;
   }

   try
   {
       $dbh = new PDO("mysql:host=$db_host_name;dbname=$db_name", $db_user_name, $db_user_pwd);
	   //print("<h1>HERE!</h1>");
       if($dbh)
       {
           if (isset( $_POST['Generate']))
           {
               $stmt = $dbh->prepare("select PlayerId, FirstName, LastName, Country, ATPPoints from ATPPlayers order by ATPPoints desc");
               if ($stmt->execute())
               {
                   $rows = $stmt->fetchAll();
                   $players = createPlayers($rows);
                   if ($pc == 128)
                   {
                   		$leftArr = handleSeeds1to4($leftArr);
                   		$leftArr = handleSeeds5to8($leftArr);
                   		$leftArr = handleSeeds9to16($leftArr);
                   		$leftArr = handleSeeds17to32($leftArr);
                   		$leftArr = handleSeeds33to64($leftArr);
                   		$rightArr = handleOpponentArray($rightArr);
                   }
                   else if ($pc == 32)
                   {
                   		$leftArr = smallDraw1to4($leftArr);
                   		$leftArr = smallDraw5to8($leftArr);
                   		$leftArr = smallDraw9to16($leftArr);
                   		$rightArr = smallDrawOpponentArray($rightArr);
                   }
    
                   if ($pc == 128)
                   {
                   
                   		drawTable($leftArr, $rightArr, $players, 16, 4, 1, 128);
                   		drawTable($leftArr, $rightArr, $players, 16, 4, 2, 128);
                   		drawTable($leftArr, $rightArr, $players, 16, 4, 3, 128);
                   		drawTable($leftArr, $rightArr, $players, 16, 4, 4, 128);
                   }
                   else if ($pc == 32)
                   {
                   		drawTable($leftArr, $rightArr, $players, 4, 4, 1, 32);
                   		drawTable($leftArr, $rightArr, $players, 4, 4, 2, 32);
                   		drawTable($leftArr, $rightArr, $players, 4, 4, 3, 32);
                   		drawTable($leftArr, $rightArr, $players, 4, 4, 4, 32);
                   }
               }
               else
               {
                   print_r($stmt->errorInfo());
               }
           }
           else if (isset( $_POST['GSAg']))
           {
               if ($pAge == 0)
               {
                   $pAgeLow = 15;
                   $pAgeHigh = 40;
               }
               else if ($pAge == 1)
               {
                   $pAgeLow = 15;
                   $pAgeHigh = 20;
               }
               else if ($pAge == 2)
               {
                   $pAgeLow = 21;
                   $pAgeHigh = 25;
               }
               else if ($pAge == 3)
               {
                   $pAgeLow = 26;
                   $pAgeHigh = 30;
               }
               else if ($pAge == 4)
               {
                   $pAgeLow = 31;
                   $pAgeHigh = 35;
               }
               else if ($pAge == 5)
               {
                   $pAgeLow = 36;
                   $pAgeHigh = 40;
               }
                   
               $stmt = $dbh->prepare("select FirstName, LastName, Age, GrandslamName, count from ATPPlayers, Grandslams, Grandslamwins where count >= 1 and Age >= $pAgeLow and Age <= $pAgeHigh AND ATPPlayers.PlayerId = Grandslamwins.PlayerId and Grandslamwins.GrandslamId = Grandslams.GrandslamId order by count desc;");
               if ($stmt->execute())
               {
                   $rows = $stmt->fetchAll();
                   echo "<br>"."Grand Slam Wins by players in the age group $pAgeLow to $pAgeHigh"."<br>";
                   displayPAgeData($rows, 0);
               }
           }

           else if (isset( $_POST['GSRk']))
           {
               if ($pRank == 0)
               {
                   $pRankLow = 1;
                   $pRankHigh = 128;
               }
               else if ($pRank == 1)
               {
                   $pRankLow = 1;
                   $pRankHigh = 5;
               }
               else if ($pRank == 2)
               {
                   $pRankLow = 6;
                   $pRankHigh = 10;
               }
               else if ($pRank == 3)
               {
                   $pRankLow = 11;
                   $pRankHigh = 15;
               }
               else if ($pRank == 4)
               {
                   $pRankLow = 16;
                   $pRankHigh = 20;
               }
               else if ($pRank == 5)
               {
                   $pRankLow = 21;
                   $pRankHigh = 128;
               }
                   
               $stmt = $dbh->prepare("select  FirstName, LastName, ATPPlayers.PlayerId, GrandslamName, count from ATPPlayers, Grandslams, Grandslamwins where count >= 1 and ATPPlayers.PlayerId >= $pRankLow and ATPPlayers.PlayerId <= $pRankHigh AND ATPPlayers.PlayerId = Grandslamwins.PlayerId and Grandslamwins.GrandslamId = Grandslams.GrandslamId order by count desc;");
               if ($stmt->execute())
               {
                   $rows = $stmt->fetchAll();
                   echo "<br>"."Grand Slam Wins by players in the Rank group $pRankLow to $pRankHigh"."<br>";
                   displayPAgeData($rows, 1);
               }
           }
           else if (isset( $_POST['a1000Ag']))
           {
               if ($p1000Age == 0)
               {
                   $p1000AgeLow = 15;
                   $p1000AgeHigh = 40;
               }
               else if ($p1000Age == 1)
               {
                   $p1000AgeLow = 15;
                   $p1000AgeHigh = 20;
               }
               else if ($p1000Age == 2)
               {
                   $p1000AgeLow = 21;
                   $p1000AgeHigh = 25;
               }
               else if ($p1000Age == 3)
               {
                   $p1000AgeLow = 26;
                   $p1000AgeHigh = 30;
               }
               else if ($p1000Age == 4)
               {
                   $p1000AgeLow = 31;
                   $p1000AgeHigh = 35;
               }
               else if ($p1000Age == 5)
               {
                   $p1000AgeLow = 36;
                   $p1000AgeHigh = 40;
               }
                   
               $stmt = $dbh->prepare("select FirstName, LastName, Age, ATP1000Name, numWins from ATPPlayers, ATP1000, ATP1000wins where numWins >= 1 and Age >= $p1000AgeLow and Age <= $p1000AgeHigh AND ATPPlayers.PlayerId = ATP1000wins.PlayerId and ATP1000wins.ATP1000Id = ATP1000.ATP1000Id order by numWins desc;");
               if ($stmt->execute())
               {
                   $rows = $stmt->fetchAll();
                   echo "<br>"."ATP 1000 wins by players in the age group $p1000AgeLow to $p1000AgeHigh"."<br>";
                   displayPAgeData($rows, 2);
               }
           }
           else if (isset( $_POST['a1000Rk']))
           {
               if ($p1000Rank == 0)
               {
                   $p1000RankLow = 1;
                   $p1000RankHigh = 128;
               }
               else if ($p1000Rank == 1)
               {
                   $p1000RankLow = 1;
                   $p1000RankHigh = 5;
               }
               else if ($p1000Rank == 2)
               {
                   $p1000RankLow = 6;
                   $p1000RankHigh = 10;
               }
               else if ($p1000Rank == 3)
               {
                   $p1000RankLow = 11;
                   $p1000RankHigh = 15;
               }
               else if ($p1000Rank == 4)
               {
                   $p1000RankLow = 16;
                   $p1000RankHigh = 20;
               }
               else if ($p1000Rank == 5)
               {
                   $p1000RankLow = 21;
                   $p1000RankHigh = 128;
               }
                   
               $stmt = $dbh->prepare("select  FirstName, LastName, ATPPlayers.PlayerId, ATP1000Name, numWins from ATPPlayers, ATP1000, ATP1000wins where numWins >= 1 and ATPPlayers.PlayerId >= $p1000RankLow and ATPPlayers.PlayerId <= $p1000RankHigh AND ATPPlayers.PlayerId = ATP1000wins.PlayerId and ATP1000wins.ATP1000Id = ATP1000.ATP1000Id order by numWins desc;");
               if ($stmt->execute())
               {
                   $rows = $stmt->fetchAll();
                   echo "<br>"."ATP1000 Wins by players in the Rank group $p1000RankLow to $p1000RankHigh"."<br>";
                   displayPAgeData($rows, 3);
               }
           }

       }

    }
    catch (PDOException $e)
    {
        echo 'Connection failed: ' . $e->getMessage();
    }

   //===============================================
   //This function fills up the Players name array
   //===============================================
    function createPlayers($dbRows)
	{
        $pl = array();
        foreach($dbRows as $row)
        {
            $strN = $row['FirstName'] . " " . $row['LastName'];
            array_push($pl, $strN);
        }
        return $pl;
	}

   //===============================================
   //This function displays GS data by age
   //===============================================
    function displayPAgeData($dbRows, $type)
	{
        $tr=1;
        $rr = 0;
        echo '<table border="1" style="margin-top:10px; color:black; float: left">';
        foreach($dbRows as $row)
        {
            $strN = $row['FirstName'] . " " . $row['LastName'];

            $pl = substr($strN, 0, 16);
            echo "<tr>";
            $td=1;
            echo "<td align='center'>".$pl."</td>";
            $td++;
            if ($type == 0 OR $type == 2)
            {
                echo "<td align='center'>".$row['Age']."</td>";
            }
            else if ($type == 1 OR $type == 3)
            {
                //echo "<td align='center'>".$row['ATPPlayers.PlayerId']."</td>";
                echo "<td align='center'>".$row['PlayerId']."</td>";
            }
            $td++;
            if ($type==0 OR $type==1)
            {
                  echo "<td align='center'>".$row['GrandslamName']."</td>";
                  $td++;
                  echo "<td align='center'>".$row['count']."</td>";
            }
            else if ($type==2 OR $type==3)
            {
                  echo "<td align='center'>".$row['ATP1000Name']."</td>";
                  $td++;
                  echo "<td align='center'>".$row['numWins']."</td>";
            }
            $td++;
            $tr++;
            $rr++;
        }
        if ($rr == 0)
        {
            if($type == 0)
            {
                echo "No Grand Slam winners in this Age Group!!!!!!!"."<br>";
            }
            else if ($type == 1)
            {
                echo "No Grand Slam winners in this Rank Group!!!!!!!"."<br>";
            }
            else if($type == 2)
            {
                echo "No ATP1000 winners in this Age Group!!!!!!!"."<br>";
            }
            else if ($type == 3)
            {
                echo "No ATP1000 winners in this Rank Group!!!!!!!"."<br>";
            }
        }
	}
   //===============================================
   //= This function handles the first 4 seeds
   //= Number1 seed always in the top half
   //= Number2 seed always in the bottom half
   //= Number3 and Number4 randomly assigned to top/bottom
   //=====================================================
    function handleSeeds1to4($lArr)
	{
        $lArr[0] = 0;
        $lArr[63] = 1;
        $i3to4 = array(31, 32);
        $a = array(2, 3);

        for($i=0; $i < 2; $i++)
        {
            $ran_Num = array_rand($a);
            $lArr[$i3to4[$i]] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $lArr;

	}
	
   //===============================================
   //= This function handles the first 4 seeds for smallDraw
   //= Number1 seed always in the top half
   //= Number2 seed always in the bottom half
   //= Number3 and Number4 randomly assigned to top/bottom
   //=====================================================
    function smallDraw1to4($lArr)
	{
        $lArr[0] = 0;
        $lArr[15] = 1;
        $i3to4 = array(7, 8);
        $a = array(2, 3);

        for($i=0; $i < 2; $i++)
        {
            $ran_Num = array_rand($a);
            $lArr[$i3to4[$i]] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $lArr;

	}

   //===============================================
   //This function handles the seeds 5 to 8
   //following ATP seeding rules
   //These should be in different quarters
   //===============================================
    function handleSeeds5to8($lArr)
	{
        $i5to8 = array(15, 16, 47, 48);
        $a = array(4, 5, 6, 7);

        for($i=0; $i < 4; $i++)
        {
            $ran_Num = array_rand($a);
            $lArr[$i5to8[$i]] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $lArr;
	}
	
   //===============================================
   //This function handles the seeds 5 to 8 for smallDraw
   //following ATP seeding rules
   //These should be in different quarters
   //===============================================
    function smallDraw5to8($lArr)
	{
        $i5to8 = array(3, 4, 11, 12);
        $a = array(4, 5, 6, 7);

        for($i=0; $i < 4; $i++)
        {
            $ran_Num = array_rand($a);
            $lArr[$i5to8[$i]] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $lArr;
	}

   //===============================================
   //This function handles the seeds 9 to 16 
   //following ATP seeding rules
   //===============================================
    function handleSeeds9to16($lArr)
	{
        $i9to16 = array(7, 8, 23, 24, 39, 40, 55, 56);
        $a = array(8, 9, 10, 11, 12, 13, 14, 15);

        for($i=0; $i < 8; $i++)
        {
            $ran_Num = array_rand($a);
            $lArr[$i9to16[$i]] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $lArr;
	}
	
   //===============================================
   //This function handles the seeds 9 to 16 
   //following ATP seeding rules
   //===============================================
    function smallDraw9to16($lArr)
	{
        $i9to16 = array(1,2,5,6,9,10,13,14);
        $a = array(8, 9, 10, 11, 12, 13, 14, 15);

        for($i=0; $i < 8; $i++)
        {
            $ran_Num = array_rand($a);
            $lArr[$i9to16[$i]] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $lArr;
	}
	


   //===============================================
   //This function handles the seeds 17 to 32 
   //following ATP seeding rules
   //===============================================
    function handleSeeds17to32($lArr)
	{
        $i17to32 = array(3, 4, 11, 12, 19, 20, 27, 28, 35, 36, 43, 44, 51, 52, 59, 60);
        $a = array(16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);

        for($i=0; $i < 16; $i++)
        {
            $ran_Num = array_rand($a);
            $lArr[$i17to32[$i]] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $lArr;
	}

   //===============================================
   //This function handles the seeds 33 to 64 
   //following ATP seeding rules
   //===============================================
    function handleSeeds33to64($lArr)
	{
        $i33to64 = array(1,2,5,6,9,10,13,14,17,18,21,22,25,26,29,30,33,34,37,38,41,42,45,46,49,50,53,54,57,58,61,62);
        $a = array(32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63);

        for($i=0; $i < 32; $i++)
        {
            $ran_Num = array_rand($a);
            $lArr[$i33to64[$i]] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $lArr;
	}
	
   //===============================================
   //This function handles the seeds 65 to 128
   //Totally random assignment
   //===============================================
    function handleOpponentArray($rArr)
    {
        $a = array_fill(0, 64, 0);
        for($i=0; $i < 64; $i++)
        {
            $a[$i] = $i + 64;
        }

        for($i=0; $i < 64; $i++)
        {
            $ran_Num = array_rand($a);
            $rArr[$i] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $rArr;
    }
    
    	
   //===============================================
   //This function handles the seeds 17-32 for small draw
   //Totally random assignment
   //===============================================
    function smallDrawOpponentArray($rArr)
    {
        $a = array_fill(0, 16, 0);
        for($i=0; $i < 16; $i++)
        {
            $a[$i] = $i + 16;
        }

        for($i=0; $i < 16; $i++)
        {
            $ran_Num = array_rand($a);
            $rArr[$i] = $a[$ran_Num];
            unset($a[$ran_Num]);
        }
        return $rArr;
    }

    function drawTable($la, $ra, $pl, $rows, $cols, $qtr, $size)
	{
			if ($qtr == 1)
            {
				echo '<table border="1" style="margin-top:10px; color:black; float: left">'; 
            }
			else
            {
				echo '<table border="1" style="margin-top:10px; margin-left:10px; color:black; float: left">'; 
            }
			if ($size == 128)
			{
				$rr = ($qtr - 1) * 16;
			}
			else if ($size == 32)
			{
				$rr = ($qtr - 1) * 4;
		    }
			for($tr=1;$tr<=$rows;$tr++)
            {
			    $pl1 = substr($pl[$la[$rr]], 0, 16);
                $pl2 = substr($pl[$ra[$rr]], 0, 16);
			    echo "<tr>"; 
					$td=1;
					echo "<td align='center'>".($la[$rr] + 1)."</td>";
					$td++;
					echo "<td align='center'>".$pl1."</td>";
					$td++;
					echo "<td align='center'>".($ra[$rr] + 1)."</td>";
					$td++;
					echo "<td align='center'>".$pl2."</td>";
					
			    echo "</tr>"; 
				$rr++;
			} 
			echo "</table>";
	}
	?>

</body>
</html>
