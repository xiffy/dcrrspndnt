<?php
$query = 'decorrespondent.nl/';

// passwords, keys, db-settings
require_once('settings.local.php');
// Create our twitter API object
require_once("twitteroauth.php");
include_once ('simple_html_dom.php');
// database, mysql, why not?
include('db.php');

$since = get_since($db);
echo 'sinds: '.$since."\n";
// go to https://dev.twitter.com/apps and create new application
// and obtain [CONSUMER_KEY], [CONSUMER_SECRET], [oauth_key], [oauth_secret]
// then put them in settings.local.php
$oauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_KEY, OAUTH_SECRET);

// Make up a useragent
$oauth->useragent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/13.6.0.9';

$tweets_found = json_decode(
                  $oauth->get( 'https://api.twitter.com/1.1/search/tweets.json',
                                array('q' => $query,
                                      'count' => 100,
                                      'since_id' => $since,
                                      'result_type' => 'recent')
                              )
                            );
$tweets = array();
if(is_object($tweets_found)) foreach ($tweets_found->statuses as $tweet){
	$tweets[] = $tweet;
}
if (count($tweets)) {
	$tweets = array_reverse($tweets);
} else {
	die ('Nothing found');
}

foreach ($tweets as $tweet)
{	//print_r($tweet->entities->urls);
	update_since($tweet->id, $db);
	foreach($tweet->entities->urls as $url)
	{
		$tco = $url->url;
		$share = $url->expanded_url;
		echo $share."\n";
		if(! strstr($share, 'decorrespondent'))
		{
			$short = $share;
			$short_res = mysqli_query($db,'select * from unshorten where short_url = "'.$short.'"');
			if(mysqli_num_rows($short_res) == 0)
			{
				echo $short."\n";
				$share = unshorten_url($short);
				echo 'became-> '.$share."\n\n";
				// opslaan opdat we deze niet nogmaals opvragen
				mysqli_query($db,'insert into unshorten (short_url, url) values ("'.addslashes($short).'","'.addslashes($share).'")');
			}
			else
			{ // misschien moeten we hem tellen?
				$short_arr = mysqli_fetch_array($short_res);
				$share = $short_arr['url'];
			}
		}

		if(strstr($share, 'decorrespondent'))
		{
			if (strstr($share, 'http://'))
			{
				$share = str_replace('http:', 'https:', $share);
			}

			$parsed = parse_url ($share);
			if (isset($parsed['path']))
			{
				if (! strstr($parsed['host'], 'decorrespondent.nl') || $parsed['host'] == 'blog.decorrespondent.nl' || $parsed['host'] == 'dynamic.decorrespondent.nl')
				{
					continue;
				}
				$path = $parsed['path'];
				$path = explode('/', $path);
				if(isset($path[2]) && isset($path[3]))
				{
					$path = $path[1].'/'.$path[2];
					$clean = $parsed['scheme'].'://'.$parsed['host'].'/'.$path;
					$query = 'select * from artikelen where clean_url = "'.$clean.'"';
					$res = mysqli_query($db, $query);
					if(mysqli_num_rows($res))
					{
						if (COUNT_TWEETS == 1)
						{
							$art_row = mysqli_fetch_array($res);
							$tweet_res = mysqli_query($db,'select * from tweets where art_id = '.$art_row['ID'].' and tweet_id = "'.$tweet->id.'"');
							if (mysqli_num_rows($tweet_res) == 0)
							{
								echo 'counting tweet '.$tweet->id."\n";
								mysqli_query($db, 'insert into tweets (tweet_id, art_id) values ("'.$tweet->id.'", '.$art_row['ID'].')');
								mysqli_query($db, 'update artikelen set tweet_count = tweet_count + 1 where artikelen.ID = '.$art_row['ID']);
							}
						}

						continue; // hebben we al!
					}
					// even de url opvragen om de auteur te vinden
					$html = file_get_html($share);
					$og = array();
					if (is_object($html))
					{
						foreach( $html->find('meta[property^=og:], meta[name^=twitter:], meta[property^=twitter:], meta[property^=article:]') as $meta )
						{
							if(strstr($meta->property, 'og:'))
							{
								$key = substr($meta->property,3);
								$og[$key] = stripslashes($meta->content);
							}
							// meh dit is weer eens nieuw
							if(strstr($meta->property, 'article:'))
							{
								$key = $meta->property;
								$og[$key] = stripslashes($meta->content);
							}
						}
						foreach($html->find('meta[name=author]') as $author) {
							$og['article:author'] = stripslashes($author->content);
						}
						foreach($html->find('div[class=publication-authors-department]') as $section) {
							$og['article:section'] = str_replace(array('Gastcorrespondent ', 'Correspondent '), '', $section->plaintext);
						}
					}
					$tweet_text = $og['article:author'] .': '.$og['title'];
					$tweet_text = str_replace('&#039;', "'", $tweet_text);
					// nu mogen we serializen
					$og = serialize($og);

					echo 'inserting: insert into artikelen (t_co, clean_url, share_url, og) values ("'.$tco.'", "'.$clean.'", "'.$share.'", "'.substr($og,0,20).'")'."\n";
					mysqli_query($db, 'insert into artikelen (t_co, clean_url, share_url, og, tweet_count) values ("'.$tco.'", "'.$clean.'", "'.$share.'", "'.addslashes($og).'", 0)');
					$artikel_id = mysqli_insert_id();
					// if mysql failed be sure not to send a tweet
					if (! $artikel_id)
						continue;

					if (COUNT_TWEETS == 1)
					{
						echo 'counting tweet '.$tweet->id."\n";
						mysqli_query($db, 'insert into tweets (tweet_id, art_id) values ("'.$tweet->id.'", '.$artikel_id.')');
						mysqli_query($db, 'update artikelen set tweet_count = tweet_count + 1 where artikelen.ID = '.$artikel_id);
					}

					// stuur er ook een tweet uit op het speciale twitter account:
					if (SEND_TWEETS == 1)
					{
						$tw_text = substr($tweet_text, 0, 140 - 25).' '.$share;

						echo "sending tweet: {$tw_text} \n";
						$connection = new TwitterOAuth(
							OUTGOING_CONSUMER_KEY,
							OUTGOING_CONSUMER_SECRET,
							OUTGOING_OAUTH_KEY,
							OUTGOING_OAUTH_SECRET
						);
						$connection->useragent = 'dcrrspndt tweet-bot';
						$connection->post(
							'https://api.twitter.com/1.1/statuses/update.json',
							array(
								'status' => $tw_text,
								'source' => 'molecule.nl/decorrespondent',
								'callback_url' => 'http://molecule.nl/decorrespondent'
							)
						);
						if (strcmp($connection->http_code, '200') == 0)
							echo "Tweet sent \n";
						else
							echo "Posting failed. Twitter down?".$connection->http_code."\n";

					}
				}
			}
		}
	}
}

// alle meta-waardes wegschrijven in de meta-table voor makkelijker cross-linken:
// selecteer alle artikelen die geen meta_artikel rows bezitten
$res = mysqli_query ($db,'select artikelen.ID as art_id, og from artikelen left outer join meta_artikel on artikelen.ID = meta_artikel.art_id where meta_artikel.art_id IS NULL');
$skip_keys = array('url', 'locale', 'site_name');
while ($row = mysqli_fetch_array($res))
{
	$og = unserialize($row['og']);
	$art_id = $row['art_id'];
	foreach($og as $key => $value)
	{
		if(in_array($key, $skip_keys))
			continue;

		$meta_res = mysqli_query($db, 'select * from meta where `type` = "'.$key.'" and waarde = "'.$value.'"');
		if (mysqli_num_rows($meta_res) == 0)
		{
			mysqli_query($db, 'insert into meta (waarde, type) values ("'.$value.'", "'.$key.'")');
			$meta_id = mysqli_insert_id();
		}
		else
		{
			$meta_arr = mysqli_fetch_array($meta_res);
			$meta_id = $meta_arr['ID'];
		}
		// koppel aan het gevonden artikel
		$link_res = mysqli_query($db, 'select * from meta_artikel where art_id = '.$art_id.' and meta_id = '.$meta_id);
		if( mysqli_num_rows($link_res) == 0)
		{ // en maak de meta-link
			mysqli_query($db, 'insert into meta_artikel (art_id, meta_id) values ('.$art_id.', '.$meta_id.')');
		}
	}
}

function update_since($since, $db)
{
	$query = 'update app_keys set app_keys.app_value = "'.$since.'" where app_key = "since"';
	mysqli_query($db, $query);
}

function get_since($db)
{
	$res = mysqli_query($db, 'select app_value from app_keys where app_key = "since"');
	$row = mysqli_fetch_array($res);
	return $row['app_value'];
}


function unshorten_url($url)
{
	$ch = curl_init($url);
	curl_setopt_array($ch, array(
                                CURLOPT_FOLLOWLOCATION => TRUE,  // the magic sauce
                                CURLOPT_RETURNTRANSFER => TRUE,
                                CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
                                CURLOPT_SSL_VERIFYPEER => FALSE,
                               )
                    );
	curl_exec($ch);
	$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_close($ch);
	return $url;
}
