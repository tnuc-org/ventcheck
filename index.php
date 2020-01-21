<?php

ini_set("display_errors", "On");
error_reporting(~0);
set_time_limit(60);
//echo "<h1>debugging live system because fuck you</h1>";

class Vcheck
{
private static $ip = '';
private static $port = '';
private static $id = '';
private static $ptitle = null;

private static $show_phantoms = false;
private static $min = false;
private static $unhidden = array();

private static $set = 0;
private static $data = null;
private static $output = '';

private static function append_header() {	
	if(self::$set === 2) $title = self::$ip . ":" . self::$port . " - Ventcheck";
	elseif(self::$set === 1) $title =& self::$ptitle;
	else $title = "Ventcheck";
	if(!self::$min) {
	self::$output .= <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<meta name="description" content="Advanced Ventrilo Lookup script for hidden Servers. Shows users ordered by the channels they are in and more. By Flo - tnuC.org.">
<meta name="author" content="Flo">
<meta name="keywords" content="tnuc,tibia,flo,ventcheck,ventrilo,lookup,vent,check,status,najko">
<link rel="stylesheet" href="style.css" type="text/css"> 
<link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon"> 
<link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon">
<title>$title</title>
</head><body>
<div id="outer" align="center";">
<div id="wrap" align="center";">

EOD;
	} else {
	self::$output .= <<<EOD
<html><head>
<link rel="stylesheet" href="style.css" type="text/css"> 
<link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon"> 
<link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon">
<title>$title</title></head><body>

EOD;
	}
}

private static function append_footer() {
	if(!self::$min) {
	self::$output .= <<<EOD
	
<div style='clear:both;'></div>
<div class="foot">
<br /><br />
by Flo - <a href="http://tnuc.org/">tnuC.org</a><br />
</div>
</div>
</div>
</div>
</body></html>

EOD;
	} else {
	self::$output .= <<<EOD

</body></html>
EOD;
	}
}

private static function append_help() {
	if(self::$set) {
		$ti =& self::$ip;
		$tp =& self::$port;
		$tph = self::$show_phantoms ? '&amp;phantoms=yes' : '';
		$txt = "<a href=\"./?ip=$ti&amp;port=$tp$tph\">back to $ti:$tp</a>";
	} else $txt = '<a href="./">back</a>';
	
	self::$output .= <<<EOD
	
<h1>Ventcheck - Help</h1><br />
$txt<br /><br />
<div class="l">
There are thousands of Ventrilo status scripts on the internet, all of which show exactly the same as the one of Ventrilo.com, 
except with more fancy colours and icons. But what if channels, names, comments and all that are hidden by the admin? 
All you'll see is a table with a ton of Xs and seemingly random numbers.
<br /><br />
But hey, you still got me.<br />
This script is designed to get the maximum amount of information out of said table. You'll be able to see how many users 
are in the same channel, how many of them are admins or phantoms, how long they have been online and more. You might want 
to go as far as calling it the next generation of espionage on Tibia, if only because appreciation gives me a raging hardon.
<br /><br /><br />
<h2>Recent Changes:</h2>
-Rewrote everything from scratch, old code was just shit 1.5 years ago<br />
-Replaced JavaScript 'Refresh' link with normal one (wtf was I thinking?)<br />
-Now using cURL instead of file_get_contents() so no more loading errors<br />
-Removed 'possible MCs' as detected by ping, was completely useless<br />
-Added phantom support for the channel overview (though hidden by default)<br />
-Phantoms are now hidden from all statistics by default unless explicitly requested<br />
-Fixed bug that would ignore users with spaces in names (if names unhidden)<br />
-Added a 4th colour indicator (blue) for the default view (not min)<br />
-Changed Ontime to showing ((hh:)mm:)ss rather than seconds depending on ontime<br />
-Fixed CSS float bug (menu wrapping around the left table)<br />
-Removed that motherfucking semicolon in the top left corner<br />
-Script will now tell you when channels or names are unhidden in the server settings,<br />
&nbsp;&nbsp;you can then check vent.com or any other script for more info on those<br />
-Added hit counter on min view out of curiosity, my server stat logs are shit.<br />
&nbsp;&nbsp;(invisible unless you run NoScript anyway)<br /><br />
<strong>Note:</strong><br />
I just formatted my comp and haven't re-installed all my browsers for CSS testing yet.<br />
Layout works flawlessly in Chrome, FF3.5 and IE6.<br />
Contact me using the form on tnuC.org if it looks messed up in IE7/IE8.<br />
(incl. screenshot if possible)
<br /><br /><br />
<h2>Default View:</h2>
While the main idea behind the entire script is the minimized version, a more graphical approach is way more eye-friendly for 
the casual lookup.
<br /><br />
<strong>Default View shows:</strong><br />
-Total number of real users online (excluding phantoms)<br />
-Numbers of admins online<br />
-Number of phantoms online (hidden elsewhere unless you click 'Show Phantoms')<br />
-Server uptime<br />
-Additional unhidden info (Channels and/or User names), only shown if unhidden<br />
&nbsp;&nbsp;Check Ventrilo.com for more information like channel/name listings if so<br /><br />
Additionally, you will see a list of channels ordered by the number of users inside and a list of the (up to) five last users 
that logged on. Flags can be A (Admin), P (Phantom) or L (Lobby). A channel with 3 As in the flags column means there are 3 
Admins in the channel. The lobby is treated separetely because with its fixed channel ID it is the only channel you can 
actually identify while channels are hidden in the server settings. It will only be shown if the lobby is not empty.<br /><br />
The colours in the Recent column correlate to those in the Recent Connections table. For example, a red mark next to a channel 
on the left, means that the user that logged in last, and is also red on the right table, is currently in that channel.
<br /><br /><br />
<h2>Minimized View:</h2>
This is what it's all about. Minimized view features most of the information from the default view compressed to use minimum 
space and server load and presents it in a way that allows you to see everything you need to know at a quick glance.
<br /><br />
<strong>Minimized View shows:</strong><br /><br />
<div align="center"><img src="./min.png" alt="" title=""></div><br />
1. Number of users online<br />
2. Users by channels**<br />
3. Users in the lobby (only shown if >0)<br />
4. Online times of the last 3 users that connected<br />
5. Current CET time<br />
6. Total number of Admins online<br />
7. Additional unhidden information (C=Channels, N=Names, X=Comments)<br /><br />
**As explained for the default view, the colours correlate to the users that logged on last (4.) so you know what channels 
the last people are in. Note that the colour of the "newer" user has priority, so if "red" and "green" are in the same 
channel, the channel is red.
<br /><br /><br />
<h2>The Full Potential:</h2>
Use min view together with a 3rd party program such as <a href="http://www.abstractpath.com/powermenu/" target="_blank">PowerMenu</a> 
that allows you to make a window 'Always on Top', so it stays on top of Tibia while playing and all you need to check again is 
clicking the window and pressing F5.
<br /><br />
Still too much effort?
Use Firefox and get the <a href="https://addons.mozilla.org/en-US/firefox/addon/115" target="_blank">ReloadEvery</a> addon to make 
the page refresh automatically every X seconds or minutes. Since all vent lookup scripts function in realtime you'll have a live 
alarm system and can prepare for a masslog just a few seconds after the enemy starts gathering.<br /><br />
<div align="center"><img src="./134.jpg" width="487" height="502" alt="" title=""></div><br />
(I use a resolution of 1600*1200 so the window takes but the space of 1-2 Tibia sqm)<br />
(Screenshot shows an ancient version with way less details by the way)
</div><br /><br />
$txt

EOD;
}

private static function append_form() {
	$tp =& self::$port;
	$ti =& self::$ip;
	self::$output .= <<<EOD
<h1>Ventcheck 2.1</h1><br />
<form action="./" name="ventdata" method="GET">
<input type="text" class="txt" name="ip" size="25" maxlength="50" value="$ti"> <strong>:</strong> 
<input type="text" class="txt" name="port" size="5" maxlength="5" value="$tp">
<input type="submit" class="btn" value=' Search '>
</form>
<br /><br />
<a href="./?help">Help</a>

EOD;
}

private static function append_error($err) {
	$err = str_replace(array("#ip#","#port#"),array(self::$ip,self::$port),$err);
	if(!self::$min) {
		$prefix = '<div id="tables"><span class="err">';
		$suffix = "</span><br /><br /></div>";
		self::$output .= "$prefix$err$suffix";
	} else {
		self::$output.= "<span class=\"err\">ERROR:<br />$err</span>";
	}
}

private static function curlget() {
	$url = "http://www.ventrilo.com/status.php?hostname=" . urlencode(self::$ip) . "&port=" . urlencode(self::$port);
	//var_dump($url);
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/5.0 Come the fuck on");
	curl_setopt($ch,CURLOPT_FAILONERROR, 1);  
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
	//curl_setopt($ch,CURLOPT_HEADER, 1);
	//curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($ch,CURLOPT_TIMEOUT, 30);
	self::$data = curl_exec($ch);
	//echo curl_error($ch);
	curl_close($ch);
	echo(self::$data);
	exit();
	if(self::$data !== null && self::$data !== false) {
		return true;
	} else {
		return false;
	}
	
}

private static function ontime($v) {

	if($v > 60*60*24) return ">24h";
	else {
		if($v < 60) return $v;
		elseif($v < 60*60) return (int)($v/60).":".($v%60<=9 ? "0" . ($v%60) : $v%60 );
		else return (int)($v/3600).":".(( (int)(($v%3600)/60) <=9) ? "0" . (int)(($v%3600)/60) : (int)(($v%3600)/60)).":".($v%60<=9 ? "0" . ($v%60) : $v%60);
	}
}

private static function parse_and_append() {
	if(self::$data === false || self::$data === null) throw new Exception("Failed to connect to ventrilo.com.<br />Refresh the page to try again.");
	self::$data = strip_tags(self::$data,"<tr>");
	
	//$test = substr(self::$data,strpos(self::$data,'> Client Count <'),128);
	//	var_dump($test);
	
	if(!preg_match('~Client Count ([^<]*)<~iU',self::$data,$pre)>0) throw new Exception("Server #ip#:#port# not found.");
	else {
		$number = preg_replace('/[^\\d]*?/','',$pre[1]);
		
		if(preg_match('~Channel Count([^<]*\\d+[^<]*)</tr>~iU',self::$data,$pre) > 0)  self::$unhidden[]='Channels';
		if(!self::$min)
		{
			preg_match('~Max Clients([\\s\\S]+)</tr>~iU',self::$data,$pre);
			$max = trim($pre[1]);
			preg_match('~Uptime([\\s\\S]+)</tr>~iU',self::$data,$pre);
			$uptime = $pre[1];
			$uptime = substr($pre[1],strpos($pre[1],"(")+1,3) === "0 d" ? substr($pre[1],strpos($pre[1],"(")+12,8) : substr($pre[1],strpos($pre[1],"(")+1,strlen($pre[1])-strpos($pre[1],"(")-6);
			if(substr($uptime,0,3) === "1 d") $uptime = str_replace("days","day",$uptime);
			
		}
		//preg_match_all('~<tr>\n\s*(\S*)\n\s*(\S*)\n\s*(\S*)\n\s*(\S*)\n\s*(\S*)\n\s*(\S*)\n\s*(\S*)\n\s+<\/tr>~',self::$data,$pre);
		//preg_match_all('~<tr>\n\s*(.*)\n\s*(.*)\n\s*(.*)\n\s*(.*)\n\s*(.*)\n\s*(.*)\n\s*(.*)\n\\s*<\/tr>~',self::$data,$pre);
		//preg_match_all('~<tr>\n([^\n]+)\n([^\n]+)\n([^\n]+)\n([^\n]+)\n([^\n]+)\n([^\n]+)\n([^\n]+)\n[^<]+<\/tr>~',self::$data,$pre);
		preg_match_all('~\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n.*<\/tr>~',self::$data,$pre);

		$cid = array(); $ontime = array(); $ping = array(); $uid = array(); $flags = array();
		$tm = false;
		for($j=1;$j<sizeof($pre[3]);$j+=1)
		{
			$flags[$j] = trim($pre[1][$j]);
			$cid[$j] = trim($pre[3][$j]);
			$ontime[$j] = trim($pre[4][$j]);
			$ping[$j] = trim($pre[5][$j]);
			$uid[$j] = trim($pre[6][$j]);
			if(!$tm && trim($pre[7][$j]) !== '') {self::$unhidden[]='Comments'; $tm = true;}
			if($uid[$j] !== 'XXXXXXXX' && !in_array('Names',self::$unhidden)) self::$unhidden[]='Names';
		}
		$admcount = 0;
		$phantoms = 0;
		if($number > 0) {
			foreach($flags as $key => $val)	{
				if($val === "A") $admcount += 1;
				if($val === "P") {
					$phantoms += 1;
					$uid[$key] = "-phntm-";
				}
				
			}
		}
		$real = $number - $phantoms;
		$channels=array();
		$inlobby=0;
		asort($ontime);
		$que=0;
		$recent=array(null,null,null);
		if($number>0) {
			foreach($ontime as $key => $val) {
				if($flags[$key] !== "P" || self::$show_phantoms) {
					$recent[$que]=$cid[$key];
					$que+=1;
				}
				if($que==4||$que==$number) break;
			}
		}
		if(self::$min)
		{
			if($recent[0]==$recent[1]) {$recent[1]=null; if($recent[0]==$recent[2]) {$recent[2]=null;}}
			else {if($recent[1]==$recent[2]) {$recent[2]=null;}}
		}
		if($number>0) {
			foreach($cid as $ind => $id) {
				if($flags[$ind] !== "P" || self::$show_phantoms) {
					if($id != 0)
						if(!(array_key_exists($id,$channels)))
							$channels[$id]=1;
						else $channels[$id]=$channels[$id]+1;
					else $inlobby+=1;
				}
			}
		}
		arsort($channels);
		$now=date("H:i:s");
		$que=0;
		if(self::$min) {
			if($number>0) {
				self::$output .= "<b><span class='fr'>".($number-$phantoms)."</span>";
				self::$output .= "&nbsp;&nbsp;@&nbsp;&nbsp;";
				if(sizeof($channels)>0) {
					foreach($channels as $ind => $chan)	{
						$que+=1;
						if ($recent[0]==$ind) self::$output .= "<span class='fr'>";
						elseif ($recent[1] == $ind) self::$output .= "<span class='fy'>";
						elseif ($recent[2] == $ind) self::$output .= "<span class='fg'>";
						self::$output .= $chan;	
						if (in_array($ind,$recent)) self::$output .= "</span>";
						if($que<sizeof($channels))	self::$output .= "-";
					}
				}
				if ($inlobby>0) {
					if ($recent[0]==0) self::$output .= "<span class='fr'>";
					elseif ($recent[1] == 0) self::$output .= "<span class='fy'>";
					elseif ($recent[2] == 0) self::$output .= "<span class='fg'>";
					self::$output .= "+".$inlobby;
					if (in_array(0,$recent)) self::$output .= "</span>";
				}
			}
			else self::$output.="</head<body><strong><span style='color:red;'>0</span>";
			self::$output .= "</strong><br />";
			$que=0;
			if($number>0) {
				foreach($ontime as $key => $val)
				{
					if($flags[$key] !== "P") {
					$que += 1;
					if($que == 1) self::$output .= "<span class='fr'>";
					elseif ($que == 2) self::$output .= "<span class='fy'>";
					elseif ($que == 3) self::$output .= "<span class='fg'>";
					else self::$output .= "<span class='fb'>";
					if($que<3 && $que<=$number) self::$output .= self::ontime($ontime[$key])."</span>&nbsp;;&nbsp;"; else {self::$output .= self::ontime($ontime[$key])."</span>"; break;}
				}}
			}else $output.="-";
			if(empty(self::$unhidden)) $extra = '';
			else {
				$extra = '&nbsp;&nbsp;&nbsp;[';
				if(in_array('Channels',self::$unhidden)) $extra .= 'C';
				if(in_array('Names',self::$unhidden)) $extra .= 'N';
				if(in_array('Comments',self::$unhidden)) $extra .= 'X';
				$extra .= ']';
			}
				
			
			self::$output.="<br />" . $now . "&nbsp;&nbsp;-&nbsp;&nbsp;" . $admcount . "A$extra\n</body></html>"; 
		}
		else
		{
			$ti =& self::$ip;
			$tp =& self::$port;
			$tph = $phantoms !==  0 && !self::$show_phantoms ? " (hidden)" : '';
			$extra = empty(self::$unhidden) ? '' : "\n\t<tr class=\"sum\">\n\t<td class=\"r\" width=\"250\">Unhidden Info</td>\n\t<td class=\"l\" width=\"250\">".implode(', ',self::$unhidden)."</td>\n\t</tr>";
			self::$output .= <<<EOD

<h1>$ti:$tp</h1>

<div id="totals">
	<table border="0" width="500" cellpadding="4" cellspacing="1" class="bld" align="center">
	<tr class="hdr">
	<td class="r" width="250">Online Users</td>
	<td class="l" width="250"><strong>$real</strong> / $max</td>
	</tr>
	<tr class="sum">
	<td class="r" width="250">Admins</td>
	<td class="l" width="250">$admcount</td>
	</tr>
	<tr class="sum">
	<td class="r" width="250">Phantoms</td>
	<td class="l" width="250">$phantoms$tph</td>
	</tr>
	<tr class="sum">
	<td class="r" width="250">Server Uptime</td>
	<td class="l" width="250">$uptime</td>
	</tr>$extra
	</table>
</div>

<div id="tables">
<div id="chans">
	Channel Overview<br/>
	<table width="220px" border="0" cellpadding="4" cellspacing="1" class="ctr xo">
	<tr class='hdr'>
	<td>Flags</td>
	<td>Users</td>
	<td>Recent</td>
	</tr>
EOD;
			if(sizeof($channels)>0) {
				foreach($channels as $ccid => $ccount)
				{
					$cadm = 0;
					$cpth = 0;
					if(sizeof($cid)>0) {
						foreach($cid as $uind => $ucid) {
							if($ucid==$ccid && $flags[$uind] === "A") $cadm+=1;
							if(self::$show_phantoms && $ucid==$ccid && $flags[$uind] === "P") $cpth+=1;
						}
					}
					self::$output.="\n\t<tr>\n\t<td>";
					if($cadm !== 0) for($m=1;$m<=$cadm;$m+=1) self::$output.="A";
					if(self::$show_phantoms && $cpth !== 0) for($m=1;$m<=$cpth;$m+=1) self::$output.="P";
					self::$output.="</td>\n\t<td>$ccount</td>\n\t<td>";
					if ($recent[0]==$ccid) self::$output .= "<span class=\"fr br\">&#149;&#149;</span>";
					if ($recent[1]==$ccid) self::$output .= "<span class=\"fy by\">&#149;&#149;</span>";
					if ($recent[2]==$ccid) self::$output .= "<span class=\"fg bg\">&#149;&#149;</span>";
					if ($recent[3]==$ccid) self::$output .= "<span class=\"fb bb\">&#149;&#149;</span>";
					self::$output.="</td>\n\t</tr>\n";
				}
			}
			if($inlobby>0)
			{
				$cadm=0;
				if(sizeof($cid)>0) {
				foreach($cid as $uind => $ucid)
					if($ucid==0) if($flags[$uind]=="A") $cadm+=1;
				}
				self::$output.="<tr class='sum'>\n\t<td>";
				if(!$cadm==0) for($m=1;$m<=$cadm;$m+=1) self::$output.="A";
				self::$output.="L</td>\n\t<td>$inlobby</td>\n\t<td>";
				if ($recent[0]==0) self::$output .= "<span class=\"fr br\">&#149;&#149;</span>";
				if ($recent[1]==0) self::$output .= "<span class=\"fy by\">&#149;&#149;</span>";
				if ($recent[2]==0) self::$output .= "<span class=\"fg bg\">&#149;&#149;</span>";
				if ($recent[3]==0) self::$output .= "<span class=\"fb bb\">&#149;&#149;</span>";
				self::$output.="</td>\n</tr>\n";
			}
			
			$phant1 = self::$show_phantoms ? '&amp;phantoms=yes' : '';
			$phant2 = self::$show_phantoms ? "<a href=\"./?ip=$ti&amp;port=$tp\">Hide<br />Phantoms</a>" : "<a href=\"./?ip=$ti&amp;port=$tp&amp;phantoms=yes\">Show<br />Phantoms</a>";
			
			self::$output.= <<<EOD

</table>
</div>

<div id="menu">
<a href="./?ip=$ti&amp;port=$tp">Refresh</a><br />
<a href="./?ip=$ti&amp;port=$tp$phant1&amp;min">Minimize</a><br />
<a href="http://www.ventrilo.com/status.php?hostname=$ti&amp;port=$tp" target="_blank">Vent.com</a><br />
<a href="./?ip=$ti&amp;port=$tp$phant1&amp;help">Help</a><br />
<a href="./">Home</a><br /><br />
$phant2
</div>

<div id="conns" style='float:left;'>\nRecent Connections\n<br />
<table width='220px' border='0' cellpadding='4' cellspacing='1' class="ctr xo">
<tr class='hdr'>
<td>Flags</td>
<td>Ontime</td>
<td>Name</td>
</tr>
EOD;
			$que=0;
			if($number>0) {
			foreach($ontime as $key => $val) {
			if($flags[$key] !== "P" || self::$show_phantoms) {
				$que+=1;
				$flag=($flags[$key]==false)?'-':$flags[$key];
				$uid[$key]=($uid[$key]=="XXXXXXXX")?"-hidden-":$uid[$key];
				if($que==1)self::$output.="<tr class=\"fr\">\n\t<td>$flag</td>\n\t<td>".self::ontime($val)."</td>\n\t<td>$uid[$key]</td>\n</tr>\n";
				elseif($que==2)self::$output.="<tr class=\"fy\">\n\t<td>$flag</td>\n\t<td>".self::ontime($val)."</td>\n\t<td class=\"xo\">$uid[$key]</td>\n</tr>\n";
				elseif($que==3)self::$output.="<tr class=\"fg\">\n\t<td>$flag</td>\n\t<td>".self::ontime($val)."</td>\n\t<td class=\"xo\">$uid[$key]</td>\n</tr>\n";
				elseif($que==4)self::$output.="<tr class=\"fb\">\n\t<td>$flag</td>\n\t<td>".self::ontime($val)."</td>\n\t<td class=\"xo\">$uid[$key]</td>\n</tr>\n";
				else self::$output.="<tr>\n\t<td>$flag</td>\n\t<td>".self::ontime($val)."</td>\n\t<td class=\"xo\">$uid[$key]</td>\n</tr>\n";
				if($que==5) break;
			}}}
		self::$output.= "</table>\n</div>\n";

		}
	}
}

public static function go() {
	ini_set('date.timezone','Europe/Berlin');
	self::$show_phantoms = isset($_GET['phantoms']) && $_GET['phantoms'] === 'yes' ? true : false;
	self::$set = isset($_GET['id']) ? 1 : 0;
	self::$set = isset($_GET['ip']) && isset($_GET['port']) ? 2 : self::$set;
	if(self::$set === 2) {
		self::$ip = trim($_GET['ip']);
		self::$port = trim($_GET['port']);
		$min = isset($_GET['min']);
		if(self::$ip === '' || self::$port === '') {
			self::$ip = '';
			self::$port = '';
			self::$set = isset($_GET['id']) ? 1 : 0;
		}
	}
	if (self::$set === 1) self::$id = trim($_GET['id']);
	if(isset($_GET['help'])) {
	// -- HELP PAGE -- //
	self::append_header();
	self::append_help();
	self::append_footer();
	
	}// * INSERT *
	elseif(self::$set) {
		// -- LOOKUP (NORMAL/MIN) -- //
		self::$min = isset($_GET['min']) ? true : false;
		
		if(self::$set === 2)
		{
			self::curlget();
			self::append_header();
			try {
				self::parse_and_append();
				self::append_footer();
			} catch(Exception $e) {
				if(!self::$min) self::append_form();
				self::append_error($e->getMessage());
				self::append_footer();
			}
		} else {
			echo "set=DBP";
			exit(1);
		}
	} else {
		// -- HOME PAGE -- //
		self::append_header();
		self::append_form();
		self::append_footer();
	}
	echo self::$output;
}

}
Vcheck::go();
?>