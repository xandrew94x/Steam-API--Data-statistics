<?php

function controllo($paese){ 
	$control = 0;
  global $paesi;
	foreach($paesi as &$var){
		if($var['Paese'] == $paese){ 
		
			$var['count'] = $var['count']+1; 
			echo "Incrementa ", $var['Paese'] ,"\t", $var['count'] ,"\n";
			$control = 1;
			break;
		}
	}
	if($control == 0){
		$paesi[sizeof($paesi)] = array('Paese'=>$paese,'count'=>1);
		echo "Aggiunto ", $paese, "\n";
	}
}

function findGame($name){ 
  $url_request = @file_get_contents("http://api.steampowered.com/ISteamApps/GetAppList/v0001/");
  $array_all_games = json_decode($url_request,true);
  $appid = "";
  for($i=0; $i<sizeof($array_all_games['applist']['apps']['app']); $i++){
    if($array_all_games['applist']['apps']['app'][$i]['name'] == $name){
      $appid=$array_all_games['applist']['apps']['app'][$i]['appid'];
    }
  }
  return $appid;
}

function has_game($steamID, $appname){ 
  $owns = 0;
  $appid = findGame($appname); 
  if($appid){
    $url_request = @file_get_contents("http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=".api_key.
    "&steamid=".$steamID."&format=json&include_appinfo=1&include_played_free_games=1");
    $games_owned = json_decode($url_request,true);
    if(@$games_owned['response']['game_count'] >= 1){ 
      for($i=0; $i<sizeof($games_owned['response']['games']); $i++){
        if(@$games_owned['response']['games'][$i]["appid"] == $appid){
          echo "L'utente ".$steamID." possiede il gioco: ".$appname." => ".$games_owned['response']['games'][$i]["appid"]."\n";
          $owns = 1;
        }
      }
    }
  } else echo "Non esiste in steam un gioco con questo nome!\n";
  return $owns;
}

function fill_array($steamID, $numSteamID, $appname){
	global $paesi;
  for($i=0; $i<$numSteamID; $i++){
    $url_request = @file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".api_key.
    "&steamids=".$steamID."&format=json&include_appinfo=1");
    $steamID_array = json_decode($url_request,true);
    if(@$steamID_array['response']['players'][0]){ 
      $owns_game = has_game($steamID, $appname);
      if(@$steamID_array['response']['players'][0]['communityvisibilitystate'] == 3){
        if($owns_game == 1){
         
          $paese = @$steamID_array['response']['players'][0]['loccountrycode']; /
          if($paese){ 
            echo "Il paese dell'utente ". $steamID . " e':" . $paese . "\n";
            controllo($paese);
          }else echo "L'utente non ha inserito il campo paese\n";
        }else echo "L'utente ".$steamID." non gioca a ".$appname."\n";
      }else echo "L'utente ".$steamID." ha un profilo privato\n";
    }
    $steamID++;
  }
}

function salva_su_file($paesi){
	$fp = fopen('prova.txt','w');
	$dati = [];
	foreach($paesi as $var){
		$obj = (object) array('Paese'=>$var['Paese'], 'count'=>$var['count']);
		fwrite($fp, json_encode($obj));
		array_push($dati,$obj);
		echo $var['Paese']," = ",$var['count'], "\n";
	}
	fclose($fp);
	return $dati;
}


define('api_key','--INSERT API KEY HEREI!--');
$steamID = ;						//-- INSERT STEAM ID FIRST USER!--
$appname = "PLAYERUNKNOWN'S BATTLEGROUNDS";		// insert the game for the search
$paesi = array();
fill_array($steamID, 2000, $appname);

$dati = salva_su_file($paesi);
$fp = fopen('dati.json','w');
fwrite($fp, json_encode($dati));
fclose($fp);



?>
