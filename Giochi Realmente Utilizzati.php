<?php
define('api_key','--INSERT API KEY HERE!--');
$first_gamer = "-- INSERT STEAM ID FIRST USER!--";
function fill_array($gamer)
{
	echo "Parsing in corso...".PHP_EOL;

	$n_giochi=0;
 	$giochi_tot = 0;

	$url_friends =  file_get_contents("https://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=".api_key."&steamid=".$gamer."&relationship=friend");
	$array_friends = json_decode($url_friends,true);

	$dati = [];
	echo "Parsing di ".sizeof($array_friends['friendslist']['friends'])." utenti".PHP_EOL;
	for($i = 0; $i < sizeof($array_friends['friendslist']['friends']); $i++)
	{
		echo "Parsing di $i su ".sizeof($array_friends['friendslist']['friends'])." utenti".PHP_EOL;
    		$giocatore = $array_friends['friendslist']['friends'][$i]['steamid'];
    		$n_giochi=0;
		$api_giochi =  file_get_contents("https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key=".api_key."&steamid=".$giocatore."&include_appinfo=1&include_played_free_games=1");
		$array_giochi = json_decode($api_giochi,true);
		
		if(@$array_giochi["response"]["game_count"]>0)
		{
     			$giochi_tot = $array_giochi["response"]["game_count"];
			for($j = 0; $j < sizeof($array_giochi["response"]["games"]); $j++)
			{
				if($array_giochi["response"]["games"][$j]["playtime_forever"]>5)
				{
					$n_giochi++;
				}
			}
			
			$obj = (object) array('id' => $giocatore,'num_giochi'=>$giochi_tot, 'num_giochi_5min'=>$n_giochi);

			array_push($dati,$obj);
		}
	}

	return $dati;
	
}
$dati = fill_array($first_gamer);
$fp = fopen('dati.json','w');
fwrite($fp, json_encode($dati));
fclose($fp);
?>



