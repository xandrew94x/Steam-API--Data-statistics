<?php

define('api_key','--INSERT API KEY HEREI!--');
$first_gamer = "-- INSERT STEAM ID FIRST USER!--"; 
$giochi = array();

function controllo($nome_gioco){ 
	
	global $giochi;
	$control = 0;
	
	foreach($giochi as &$var){
		
		if($var['gioco'] == $nome_gioco){
			
			$var['count'] = $var['count']+1; 
			echo "Incrementa ", $var['gioco'] ,"\t", $var['count'] ,"\n"; // to read something
			$control = 1;
			break;			
		}
	}
	if($control == 0){
		
		$giochi[sizeof($giochi)] = array('gioco'=>$nome_gioco,'count'=>1);
		echo "Aggiunto " ,$nome_gioco,"\t" , 1 ,"\n";   // to read something
		
	}
}

function fill_array($gamer){
	
	global $giochi;
	
	$url_friends =  @file_get_contents("http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=".api_key."&steamid=".$gamer."&relationship=friend");
	$array_friends = json_decode($url_friends,true);
	
	for($i = 0; $i < sizeof($array_friends['friendslist']['friends']); $i++){
		
		$url_friends_games =  @file_get_contents("http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=".api_key."&steamid=".$array_friends['friendslist']['friends'][$i]["steamid"]."");
		$array_friends_games = json_decode($url_friends_games,true);
		
		if(@$array_friends_games["response"]["total_count"]>0){
			
			for($j = 0; $j < sizeof($array_friends_games["response"]["games"]); $j++){
				
				controllo($array_friends_games["response"]["games"][$j]["name"]);
				
			}
		}
	}
}

$fp = fopen("prova.txt", 'w');

fill_array($first_gamer);

fwrite($fp, json_encode($giochi)); // save output in json format

fclose($fp);

?>



