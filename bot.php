<?php
$query = 'decorrespondent.nl/';

// passwords, keys, db-settings
require_once('settings.local.php');
// Create our twitter API object
require_once("twitteroauth.php");
include_once ('simple_html_dom.php');

// database, mysql, why not?
include('db.php');


$since = get_since();

echo 'sinds: '.$since."\n";
// go to https://dev.twitter.com/apps and create new application
// and obtain [CONSUMER_KEY], [CONSUMER_SECRET], [oauth_token], [oauth_token_secret]
// then put them in settings.local.php
$oauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_KEY, OAUTH_SECRET);

// Make up a useragent
$oauth->useragent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/13.6.0.9';

$tweets_found = json_decode($oauth->get('http://api.twitter.com/1.1/search/tweets.json',
array('q' => $query, 'count' => 100, 'since_id' => $since)));

if(is_object($tweets_found)) foreach ($tweets_found->statuses as $tweet){
	//print_r($tweet->entities->urls);
	update_since($tweet->id);

	foreach($tweet->entities->urls as $url)
	{
		$tco = $url->url;
		$query = 'select * from artikelen where t_co = "'.$tco.'"';
		$res = mysql_query($query);
		if(mysql_num_rows($res))
			continue; // hebben we al!

		$share = $url->expanded_url;
		if(strstr($share, 'decorrespondent'))
		{
			$parsed = parse_url ($share);
			if (isset($parsed['path']))
			{
				$path = $parsed['path'];
				$path = explode('/', $path);
				if(isset($path[2]))
				{
					$path = $path[1].'/'.$path[2];
					$clean = $parsed['scheme'].'://'.$parsed['host'].'/'.$path;
					$query = 'select * from artikelen where clean_url = "'.$clean.'"';
					$res = mysql_query($query);
					if(mysql_num_rows($res))
						continue; // hebben we al!
					// even de url opvragen om de auteur te vinden
					$html = file_get_html($share);
					$og = array();
					if (is_object($html))
					{
						foreach( $html->find('meta[property^=og:], meta[name^=twitter:], meta[property^=twitter:]') as $meta )
						{
							if(strstr($meta->property, 'og:'))
							{
								$key = substr($meta->property,3);
								$og[$key] = stripslashes($meta->content);
							}
						}
					}
					$og = serialize($og);

					echo 'inserting: insert into artikelen (t_co, clean_url, share_url, og) values ("'.$tco.'", "'.$clean.'", "'.$share.'", "'.substr($og,0,20).'")'."\n";
					mysql_query('insert into artikelen (t_co, clean_url, share_url, og) values ("'.$tco.'", "'.$clean.'", "'.$share.'", "'.addslashes($og).'")');
				}
			}
		}
	}
}
// alle meta-waardes wegschrijven in de meta-table voor makkelijker cross-linken:
// selecteer alle artikelen die geen meta_artikel rows bezitten
$res = mysql_query ('select artikelen.ID as art_id, og from artikelen left outer join meta_artikel on artikelen.ID = meta_artikel.art_id where meta_artikel.art_id IS NULL');
$skip_keys = array('url', 'locale', 'site_name');
while ($row = mysql_fetch_array($res))
{
	$og = unserialize($row['og']);
	$art_id = $row['art_id'];
	foreach($og as $key => $value)
	{
		if(in_array($key, $skip_keys))
			continue;

		$meta_res = mysql_query('select * from meta where `type` = "'.$key.'" and waarde = "'.$value.'"');
		if (mysql_num_rows($meta_res) == 0)
		{
			mysql_query('insert into meta (waarde, type) values ("'.$value.'", "'.$key.'")');
			$meta_id = mysql_insert_id();
		}
		else
		{
			$meta_arr = mysql_fetch_array($meta_res);
			$meta_id = $meta_arr['ID'];
		}
		// koppel aan het gevonden artikel
		$link_res = mysql_query('select * from meta_artikel where art_id = '.$art_id.' and meta_id = '.$meta_id);
		if( mysql_num_rows($link_res) == 0)
		{ // en maak de meta-link
			mysql_query('insert into meta_artikel (art_id, meta_id) values ('.$art_id.', '.$meta_id.')');
		}
	}
}

function update_since($since)
{
	$query = 'update app_keys set app_keys.app_value = "'.$since.'" where app_key = "since"';
	mysql_query($query);
}

function get_since()
{
	$res = mysql_query('select app_value from app_keys where app_key = "since"');
	$row = mysql_fetch_array($res);
	return $row['app_value'];
}
