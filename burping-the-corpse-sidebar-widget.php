<?php
/*
Plugin Name: Burping The Corpse Sidebar Widget
Plugin URI: http://burpingthecorpse.com/sidebar-widget/
Description: A Burping The Corpse widget that displays a Corpse badge and the latest posts from < a href="www.burpingthecorpse.com/">www.burpingthecorpse.com</a>.
Version: 0.1
Author: Joseph Reilly
Author URI: http://jupiterhost.com
*/

/*	
	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/



$pics=array(
"None"=>plugins_url("images/none.jpg", __FILE__),
"Blue and Red Poster 127x200"=>plugins_url("images/burp1.jpg", __FILE__),
"Black and White 127x127"=>plugins_url("images/burp2.jpg", __FILE__)
);

function corpse_badge_widget_getRSS($url, $numitems = '3', $before='<li class="corpse">', $after='</li>') {
	if(!is_null($url)) {
		require_once(ABSPATH. "wp-includes/rss-functions.php");
		$rss = fetch_rss($url);
		if($rss) {
			foreach(array_slice($rss->items,0,$numitems) as $item) {
				echo "$before<a title=\"".$item['title']."\" href=\"".htmlentities($item['link'])."\">".$item['title']."</a>$after";
			}
		} else {
			echo "There was an error processing the Burping The Corpse RSS feed. Please check your sidebar widget configuration.";
		}
	} else {
		echo "An error occured! No RSS url was specified. Please check your sidebar widget configuration.";
	}
}

function corpse_badge_widget_activate() {
	$default_options = array(
		'title'=>'Burping The Corpse Posts',
		'rssfeed'=>'http://www.burpingthecorpse.com/feed/',
		'badge'=>plugins_url("images/none.jpg", __FILE__),
		'numitems'=>'3',
		'beforebadge' => '',
		'afterbadge' => ''
	);
	$options = get_option('corpse_badge_widget');
	
	// set default options
	if (!is_array($options)) {
		update_option('corpse_badge_widget', $default_options);
	} else {
		foreach ($options as $i => $value) {
			$default_options[$i] = $options[$i];
		}
		update_option('corpse_badge_widget', $default_options);
	}
}

function corpse_badge_widget_init() {
	if (!function_exists('register_sidebar_widget')) {
		return;
	}
	
	function corpse_badge_widget($args) {
		// extract options
		extract($args);
		$options = get_option('corpse_badge_widget');
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$rssfeed = htmlspecialchars_decode($options['rssfeed']);
		$badge = htmlspecialchars_decode($options['badge']);
		$numitems = htmlspecialchars_decode($options['numitems'], ENT_QUOTES);
		$beforebadge = htmlspecialchars_decode($options['beforebadge'], ENT_QUOTES);
		$afterbadge = htmlspecialchars_decode($options['afterbadge'], ENT_QUOTES);
		
		// print widget
		echo $beforebadge;
		?>
		<div class="corpse-widget">
		<p class='corpse-badge'><img src="<?php echo $badge; ?>" alt="" title="Corpse '12" border="0"  /></p>
		<?php if($title != '') :?>
		<h2 class='corpse-feed-title'><?php echo $title; ?></h2>
		<?php endif; ?>
		<ul class="corpse-feed">
		<?php corpse_badge_widget_getRSS($rssfeed,$numitems); ?>
		</ul>
		</div>
		<?php
		echo $afterbadge;
	}
	
	function corpse_badge_widget_control() {
		$options = get_option('corpse_badge_widget');
			
		if ( $_POST['corpse_badge_widget-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['corpse_badge_widget-title']));
			$options['rssfeed'] = stripslashes($_POST['corpse_badge_widget-rssfeed']);
			$options['badge'] = stripslashes($_POST['corpse_badge_widget-pic-url']);
			$options['numitems'] = stripslashes($_POST['corpse_badge_widget-rss-items']);
			$options['beforebadge'] = stripslashes($_POST['corpse_badge_widget-beforebadge']);
			$options['afterbadge'] = stripslashes($_POST['corpse_badge_widget-afterbadge']);
			update_option('corpse_badge_widget', $options);
		}
		
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$rssfeed = htmlspecialchars($options['rssfeed'], ENT_QUOTES);
		$badge = htmlspecialchars_decode($options['badge']);
		$numitems = htmlspecialchars($options['numitems'], ENT_QUOTES);
		$beforebadge = htmlspecialchars($options['beforebadge'], ENT_QUOTES);
		$afterbadge = htmlspecialchars($options['afterbadge'], ENT_QUOTES);
		
?>
<p style="text-align:left;">
<label for="corpse_badge_widget-title"><?php echo __('Burping The Corpse RSS Title:'); ?></label>
<input style="width: 200px;" id="corpse_badge_widget-title" name="corpse_badge_widget-title" type="text" value="<?php echo $title; ?>" />
<br/><label for="corpse_badge_widget-pic-url"><?php _e('Choose a badge (<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CTDUKHMU9RETE"  target="_blank">donate</a>):'); ?></label>
<select id="corpse_badge_widget-pic-url" name="corpse_badge_widget-pic-url">
<?php 
	global $pics;
	foreach($pics as $picname=>$picurl) {
		echo '<option value="'.$picurl.'" '. ( $picurl == $badge ? "selected='selected'" : '' ) .' >'.$picname.'</option>';
	}
?>
</select>
<br/><label for="corpse_badge_widget-rssfeed"><?php echo __('Burping The Corpse RSS URL:'); ?></label>
<input style="width: 200px;" id="corpse_badge_widget-rssfeed" name="corpse_badge_widget-rssfeed" type="text" value="<?php echo $rssfeed; ?>" />
<br/><label for="corpse_badge_widget-rss-items"><?php _e('How many items would you like to display?'); ?></label>
<select id="corpse_badge_widget-rss-items" name="corpse_badge_widget-rss-items">
<?php
	for ( $i = 1; $i <= 20; ++$i ) {
		echo "<option value='$i' " . ( $numitems == $i ? "selected='selected'" : '' ) . ">$i</option>";
	}
?>
</select>
<br/><label for="corpse_badge_widget-beforebadge"><?php echo __('HTML/text before widget:'); ?></label>
<input style="width: 200px;" id="corpse_badge_widget-beforebadge" name="corpse_badge_widget-beforebadge" type="text" value="<?php echo $beforebadge; ?>" />
<br/><label for="corpse_badge_widget-afterbadge"><?php echo __('HTML/text after widget:'); ?></label>
<input style="width: 200px;" id="corpse_badge_widget-afterbadge" name="corpse_badge_widget-afterbadge" type="text" value="<?php echo $afterbadge; ?>" />
<br/>
</p>
<input type="hidden" id="corpse_badge_widget-submit" name="corpse_badge_widget-submit" value="1" />
<?php
	}
	register_sidebar_widget(array('Burping The Corpse', 'widgets'), 'corpse_badge_widget');
	register_widget_control(array('Burping The Corpse', 'widgets'), 'corpse_badge_widget_control');
}

function corpse_badge_widget_deactivate() {
	delete_option('corpse_badge_widget');
}

// activation
register_activation_hook(__FILE__, 'corpse_badge_widget_activate');
// initialization
add_action('plugins_loaded', 'corpse_badge_widget_init');
// deactivation
register_deactivation_hook( __FILE__, 'corpse_badge_widget_deactivate' );
?>