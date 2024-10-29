<?php
/*
Plugin Name: Aditrorecruite Widget
Plugin URI: http://wordpress.org/extend/plugins/aditrorecruite-widget/
Description: List aditrorecruite jobs in a widget and simple shortcode feature depending on widget settings.
Author: jonashjalmarsson
Version: 1.3
Author URI: http://www.hultsfred.se
*/

/*  Copyright 2013 Jonas Hjalmarsson (email: jonas.hjalmarsson@hultsfred.se)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* 
 * ADITRO RSS WIDGET 
 */ 
 class hk_aditro_rss_widget extends WP_Widget {
	protected $vars = array();

	public function __construct() {
		parent::__construct(
	 		'hk_aditro_rss_widget', // Base ID
			'Aditrorecruit', // Name
			array( 'description' => "Widget showing available jobs from aditrorecruit." ) // Args
		);
	}

 	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {	$title = $instance[ 'title' ];
		} else { $title = "Lediga jobb"; }
		if ( isset( $instance[ 'show_work' ] ) ) {	$show_work = $instance[ 'show_work' ];
		} else { $show_work = ""; }
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">Widget title</label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'show_work' ); ?>">Show only in category (in format 23,42,19)</label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'show_work' ); ?>" name="<?php echo $this->get_field_name( 'show_work' ); ?>" type="text" value="<?php echo esc_attr( $show_work); ?>" />
		</p>
		
		<?php
		echo "<p><i>Other settings can be found under Aditroreqruit Settings.</i></p>";
	}

	public function update( $new_instance, $old_instance ) {
		$instance['show_work'] = strip_tags( $new_instance['show_work'] );
		$instance['title'] = $new_instance['title'];		
		return $instance;
	}

	public function widget( $args, $instance ) {
	    extract( $args );

		$render = hk_aditro_render(true);
		$retValue = "";
		if ($instance["show_work"] == "" || in_array(get_query_var("cat"), split(",",$instance["show_work"]))) {
			if (!empty($render)) {
				$retValue .= $before_widget;
				$title = apply_filters( 'widget_title', $instance['title'] );
				if ( ! empty( $title ) ) {
					$retValue .= $before_title . $title . $after_title;
				}
				
				$retValue .= $render;			
				$retValue .= $after_widget;
			}
		}
		echo $retValue;
	}
}
/* add the widget  */
add_action( 'widgets_init', create_function( '', 'register_widget( "hk_aditro_rss_widget" );' ) );




/* 
 * add settings page 
 */
 
// create custom plugin settings menu
add_action('admin_menu', 'hk_aditro_plugin_create_menu');

function hk_aditro_plugin_create_menu() {

	//create new top-level menu
	
	add_menu_page('Aditroreqruit Plugin Settings', 'Aditroreqruit', 'administrator', __FILE__, 'hk_aditro_plugin_settings_page' , NULL /*plugins_url('/images/icon.png', __FILE__)*/ );

	//call register settings function
	add_action( 'admin_init', 'register_hk_aditro_plugin_settings' );
}


function register_hk_aditro_plugin_settings() {
	//register our settings
	register_setting( 'hk_aditro_settings', 'hk_aditro' );
}

function hk_aditro_plugin_settings_page() {
?>
<div class="wrap">
	<h2>Aditro widget and shortcode</h2>

	<form method="post" action="options.php">
    <?php settings_fields( 'hk_aditro_settings' ); ?>
    <?php do_settings_sections( 'hk_aditro_settings' ); ?>
	<?php
		$options = get_option("hk_aditro");
	
		/* get values */
		$hk_aditro_rss = $options["hk_aditro_rss"];
		$enable_cron  = ($options["enable_cron"] != "")?$options["enable_cron"]:"";
		$hk_aditro_days_new  = ($options["hk_aditro_days_new"] != "")?$options["hk_aditro_days_new"]:"1";
		$hk_aditro_num  = ($options["hk_aditro_num"] != "")?$options["hk_aditro_num"]:"10";
		$hk_aditro_guidgroup  = ($options["hk_aditro_guidgroup"] != "")?$options["hk_aditro_guidgroup"]:"";
		$hk_aditro_full_description = ($options["hk_aditro_full_description"] != "")?$options["hk_aditro_full_description"]:"";
		$hk_aditro_shortcode_full_description = ($options["hk_aditro_shortcode_full_description"] != "")?$options["hk_aditro_shortcode_full_description"]:"";
		$hk_aditro_searchbeforetext = ($options["hk_aditro_searchbeforetext"] != "")?$options["hk_aditro_searchbeforetext"]:"";
		$hk_aditro_show_num_new = ($options["hk_aditro_show_num_new"] != "")?$options["hk_aditro_show_num_new"]:"";
		$hk_aditro_more_text = ($options["hk_aditro_more_text"] != "")?$options["hk_aditro_more_text"]:"";
		$hk_aditro_more_link = ($options["hk_aditro_more_link"] != "")?$options["hk_aditro_more_link"]:"";
		
		/* enable cron if set */
		if ($enable_cron) {
			if ($options['hk_aditro_rss'] != "") {
				if ( !wp_next_scheduled( 'hk_aditro_event' ) ) {
					wp_schedule_event( time(), 'hk_aditro_schedule', 'hk_aditro_event');
				}
			}
			else
			{
				if ( wp_next_scheduled( 'hk_aditro_event' ) ) {
					wp_clear_scheduled_hook('hk_aditro_event');
				}
			}
		}
		else {
			if ( wp_next_scheduled( 'hk_aditro_event' ) ) {
				wp_clear_scheduled_hook('hk_aditro_event');	
			}
		}
		


		
		?>
		<style type="text/css">
		label { display: block; }
		input[type=text] { width: 400px; }
		input[type=checkbox] { display: inline-block; float: left; }
		textarea { width: 400px; height: 120px; }
		</style>

		<p>
			<?php 
				
				if (1 == $options["hk_aditro_generate_cache"]) {
					hk_aditro();
					echo "<i>New cache generated.</i><br/>";
				}
			?>
		</p>
		<p>
		<label title="in format http://www.aditrorecruit.com/External/Feeds/AssignmentList.ashx?guidGroup=b4fecb31-12e5-4906-a1f2-16bc15b810ff, just replace the guidGroup ID">Url to your RSS containing available jobs at aditroreqruite.com (required)</label> 
		<input name="hk_aditro[hk_aditro_rss]" type="text" value="<?php echo esc_attr( $hk_aditro_rss); ?>" />
		</p>
		<p>
		<label title="in format c5cc16a7-a968-4347-aecd-aa4ab09c58ef">Optionally add the Guidgroup ID here to get correct visual look in your Aditro-ad</label> 
		<input name="hk_aditro[hk_aditro_guidgroup]" type="text" value="<?php echo esc_attr( $hk_aditro_guidgroup); ?>" />
		</p>
		<p>
		<label>"Complete job application before <i>date</i>" text</label> 
		<input name="hk_aditro[hk_aditro_searchbeforetext]" type="text" value="<?php echo esc_attr( $hk_aditro_searchbeforetext); ?>" />
		</p>
		<p>
		<label>Number of days a job is triggered as new (css-class has_new and is_new is set)</label> 
		<input name="hk_aditro[hk_aditro_days_new]" type="text" value="<?php echo esc_attr( $hk_aditro_days_new); ?>" />
		</p>
		

		<h3>Widget only</h3>
		<p>
		<input type="checkbox" name="hk_aditro[hk_aditro_full_description]" value="1"<?php checked( 1 == $hk_aditro_full_description ); ?> /> 
		<label>Show job description</label>
		</p>
		<p>
		<label>Max number available jobs to show</label> 
		<input name="hk_aditro[hk_aditro_num]" type="text" value="<?php echo esc_attr( $hk_aditro_num); ?>" />
		</p>
		<p>
		<label title="">Show subtitle, first line = one job, second line = two jobs ... last line = if more jobs (replaces [nr] with number available jobs)</label> 
		<textarea name="hk_aditro[hk_aditro_show_num_new]"><?php echo $hk_aditro_show_num_new; ?></textarea>
		</p>
		<p>
		<label>Show more link text</label> 
		<input name="hk_aditro[hk_aditro_more_text]" type="text" value="<?php echo esc_attr( $hk_aditro_more_text); ?>" />
		</p>
		<p>
		<label>Show more link</label> 
		<input name="hk_aditro[hk_aditro_more_link]" type="text" value="<?php echo esc_attr( $hk_aditro_more_link); ?>" />
		</p>


		<h3>Shortcode only</h3>
		<p><i>The shortcode [aditrorecruit] always show all available jobs.</i></p>
		<p>
		<input type="checkbox" name="hk_aditro[hk_aditro_shortcode_full_description]" value="1"<?php checked( 1 == $hk_aditro_shortcode_full_description ); ?> /> 
		<label>Show job description</label>
		</p>
		
		<h3>Cron</h3>
		<p>
		<input type="checkbox" name="hk_aditro[enable_cron]" value="1"<?php checked( 1 == $enable_cron ); ?> /> 
		<label>Use cron to generate cache every quarter</label>
		</p>
		<p>
		<?php
			if (wp_next_scheduled( 'hk_aditro_event' )) {
				echo "Next cron is run " . Date("Y-m-d H:i:s" , wp_next_scheduled( 'hk_aditro_event' )) . ".";
			} 
			else {
				echo "Cron not used. The widget will be updated every half hour when visited.";
			}
		?>
		</p>
		<p>
		<input type="checkbox" name="hk_aditro[hk_aditro_generate_cache]" value="1" /> 
		<label>Force generate cache now (one time when save).</label>
		</p>

		<p>
			<label>Log</label>
			<textarea name="hk_aditro[hk_aditro_log]"><?php echo $options["hk_aditro_log"]; ?></textarea> 
		</p>
    <?php submit_button(); ?>

</form>
</div>
<?php }





/*
 * shortcode [aditrorecruit], showing all available jobs with descriptions
 */
function hk_aditro_shortcode_func( $atts ){
	return hk_aditro_render(false);
}
add_shortcode( 'aditroreqruit', 'hk_aditro_shortcode_func' );


/*
 * Renders the actuall content
 */
function hk_aditro_render($is_widget) {
	$options = get_option('hk_aditro');
	$retValue = "";
	
	// check for new job here every 30 minutes if no cron enabled
	if (!wp_next_scheduled( 'hk_aditro_event' ) && ($options["hk_aditro_check_time"] == "" || strtotime("+30 minutes",$options["hk_aditro_check_time"]) - time() < 0)) {
		hk_aditro();
		$options = get_option('hk_aditro');
	}

	if ($is_widget) {
		// show widget if not empty and this category not is included
		if ($options["hk_aditro"] != "") {
			$retValue .= $options["hk_aditro"];
		}
	}
	else {
		if ($options["hk_aditro_shortcode"] == "") {
			$retValue .= "Det finns inga lediga jobb.";
		}
		else {
			// show widget if not empty and this category not is included
			$retValue .= "<div id='aditrowidget' class='shortcode'>";
			$retValue .= $options["hk_aditro_shortcode"];
			$retValue .= "</div>";
		}
	}

	return $retValue;
}
 
/*
 * ADITRO RSS CRONJOB
 */
function hk_aditro() {
	$options = get_option('hk_aditro');
	$hk_aditro_check_time = time();
	$options["hk_aditro_check_time"] = $hk_aditro_check_time;
	
	$hk_aditro_days_new  = ($options["hk_aditro_days_new"] != "")?$options["hk_aditro_days_new"]:"1";
	$hk_aditro_num  = ($options["hk_aditro_num"] != "")?$options["hk_aditro_num"]:"10";
	$hk_aditro_full_description  = ($options["hk_aditro_full_description"] != "")?$options["hk_aditro_full_description"]:"0";
	$hk_aditro_shortcode_full_description  = ($options["hk_aditro_shortcode_full_description"] != "")?$options["hk_aditro_shortcode_full_description"]:"0";
	$hk_aditro_show_num_new  = ($options["hk_aditro_show_num_new"] != "")?$options["hk_aditro_show_num_new"]:"";
	$hk_aditro_more_text  = ($options["hk_aditro_more_text"] != "")?$options["hk_aditro_more_text"]:"";
	$hk_aditro_more_link  = ($options["hk_aditro_more_link"] != "")?$options["hk_aditro_more_link"]:"";
	
	$log = "No rss is checked.";
	$widgetcache = "";
	$shortcodecache = "";
	if ($options['hk_aditro_rss'] != "") :
		$log = "Checked rss " . date("Y-m-d H:i:s", strtotime("now")) . ".";
		$url = $options['hk_aditro_rss'];
		$rss =  simplexml_load_file(strip_tags($url));
		$has_new = "";
		$numjobs = count($rss->Assignments->Assignment);
		if ($numjobs > 0 ) {
			$log .= "<br>Found " . count($rss->Assignments->Assignment) . " available jobs in RSS.\n";
			$num_new_text = explode("\n",$hk_aditro_show_num_new);
			if (count($num_new_text)-1 >= count($rss->Assignments->Assignment))
				$available_jobs_text = $num_new_text[count($rss->Assignments->Assignment)-1];
			else if (count($num_new_text)-1 < count($rss->Assignments->Assignment))
				$available_jobs_text = str_replace("[nr]",count($rss->Assignments->Assignment),$num_new_text[count($num_new_text)-1]);
			
			if ($available_jobs_text != "") 
			{
				$hk_aditro_more_link_pre = "";
				$hk_aditro_more_link_post = "";
				if ($hk_aditro_more_link != "") {
					$hk_aditro_more_link_pre = "<a href='$hk_aditro_more_link'>";
					$hk_aditro_more_link_post = "</a>";
				}
				$widgetcache .= "<div class='sub-title'>$hk_aditro_more_link_pre$available_jobs_text$hk_aditro_more_link_post</div>";
				$shortcodecache .= "<div class='sub-title'>$available_jobs_text</div>";
			}
			$baseurl = $rss->channel->link;
			$newrsstime = strtotime("-" . $hk_aditro_days_new . " days");
			$count = 0;
			$widgetcache .= "<ul>";
			$shortcodecache .= "<ul>";
			foreach ($rss->Assignments->Assignment as $item)
			{
				$ApplicationEndDate = strtotime($item->ApplicationEndDate);
				$time = strtotime($item->Created);
				$newclass = "";
				if ($time > $newrsstime) { 
					$has_new = "true";
					$newclass = " isnew";
				}
				$worklink = "http://www.aditrorecruit.com/External/OJCustomer3/Assignmentview.aspx?guid=".$item->Guid."&guidGroup=" . $options["hk_aditro_guidgroup"];
				$workdescr = $item->Localization->AssignmentLoc->WorkDescr;
				$worktitle = $item->Localization->AssignmentLoc->AssignmentTitle;
				$log .= "Found article: $worktitle\n";

				// hide in widget if more than count
				$if_hide_in_widget = "";
				if ($hk_aditro_num  <= $count++) {
					$if_hide_in_widget = " style='display:none;'";
				}
				$widgetcache .= "<li class='entry-wrapper$newclass'$if_hide_in_widget>"
				 . "<a title='" . $workdescr . "."
				 . "' href='". $worklink
				 . "' target='_blank'>" . $worktitle
				 . "</a>";
				if ($hk_aditro_full_description) {
					$widgetcache .= "<div class='entry-content'>". $workdescr . ".</div>";
				}
				$widgetcache .= "<span class='time'>" . $options["hk_aditro_searchbeforetext"] . " " . hk_nicedate($ApplicationEndDate) . "</span>";
				
				$widgetcache .= "</li>";
			

				// build shortcode
				$shortcodecache .= "<li class='entry-wrapper$newclass'>"
				 . "<a title='" . $workdescr . "."
				 . "' href='". $worklink
				 . "' target='_blank'>" . $worktitle
				 . "</a>";
				if ($hk_aditro_shortcode_full_description) {
					$shortcodecache .= "<div class='entry-content'>". $workdescr . ".</div>";
				}
				$shortcodecache .= "<span class='time'>" . $options["hk_aditro_searchbeforetext"] . " " . hk_nicedate($ApplicationEndDate) . "</span>";
				//$shortcodecache .= "<div class='entry-content'>".$workdescr . ". <span class='time'>" . $options["hk_aditro_searchbeforetext"] . " " . hk_nicedate($time) . "</span></div>";
				$shortcodecache .= "</li>";
			} 
			if ($hk_aditro_more_link != "" && $hk_aditro_more_text != "") {
				$widgetcache .= "<li class='read-more-link'><a href='$hk_aditro_more_link' title='$hk_aditro_more_link'>$hk_aditro_more_text</a></li>";
			}
			$widgetcache .= "</ul>";
			$shortcodecache .= "</ul>";
			
			
		}	
	endif;
	$options["hk_aditro_log"] = $log;
	$options["hk_aditro"] = $widgetcache;
	$options["hk_aditro_shortcode"] = $shortcodecache;
	$options["hk_aditro_has_new"] = $has_new;

	update_option("hk_aditro", $options);
}
add_action("hk_aditro_event", "hk_aditro");
 
// add special cron interval to wp schedules
function hk_aditro_add_scheduled_interval($schedules) {
 
    $schedules['hk_aditro_schedule'] = array('interval'=>900, 'display'=>'Aditro cron (15 minutes)');
 
    return $schedules;
}
add_filter('cron_schedules', 'hk_aditro_add_scheduled_interval');

if (!function_exists("hk_nicedate")) {
	function hk_nicedate($time) {
		$time = date("j F Y" , $time);
		$mo = array('januari' => 'January',
				'februari' => 'February',
				'mars' => 'March',
				'april' => 'April',
				'maj' => 'May',
				'juni' => 'June',
				'juli' => 'July',
				'augusti' => 'August',
				'september' => 'September',
				'oktober' => 'October',
				'november' => 'November',
				'december' => 'December');
				
		foreach ($mo as $swe => $eng)
		$time = preg_replace('/\b'.$eng.'\b/', $swe, $time);
		return $time;
	}
}
?>