<?php  // $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $

/**
 * This page prints a particular instance of twitter
 *
 * @author  Your Name <your@email.address>
 * @version $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $
 * @package mod/twitter
 */

/// (Replace twitter with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // twitter instance ID

if ($id) {
    if (! $cm = get_coursemodule_from_id('twitter', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $twitter = get_record('twitter', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

} else if ($a) {
    if (! $twitter = get_record('twitter', 'id', $a)) {
        error('Course module is incorrect');
    }
    if (! $course = get_record('course', 'id', $twitter->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('twitter', $twitter->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, "twitter", "view", "view.php?id=$cm->id", "$twitter->id");

/// Print the page header
$strtwitters = get_string('modulenameplural', 'twitter');
$strtwitter  = get_string('modulename', 'twitter');

$navlinks = array();
$navlinks[] = array('name' => $strtwitters, 'link' => "index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => format_string($twitter->name), 'link' => '', 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);

print_header_simple(format_string($twitter->name), '', $navigation, '', '', true,
              update_module_button($cm->id, $course->id, $strtwitter), navmenu($course, $cm));

// Follow Twitter

if(isset($_POST['user'])&&isset($_POST['pwd'])) {
	include('follow.php');
} else {
?>
<div style="width:300px; border: 1px solid black; margin-right:auto; margin-left:auto; margin-top: 100px; padding:10px; background-color:white">
<form name='followform' action=<?php echo $_SERVER['PHP_SELF']."?id=".$_GET['id']; ?> method='post'>
<input type='hidden' value='<?php echo $twitter->account_name; ?>' name='account'/>
<h2 align=center><?php echo get_string('twitterfollow', 'twitter'); ?></h2>
<hr>
<table>
<tr>
<td><?php echo get_string('twitterusername', 'twitter'); ?>: </td><td><input type='text' name='user'/></td>
</tr>
<tr>
<td><?php echo get_string('twitterpassword', 'twitter'); ?>: </td><td><input type='password' name='pwd'/></td>
</tr>
<tr>
<td/><td><input type='submit' name='follow'/></td>
</tr>
</table>
<hr>
<div style='margin-left:20px; margin-right:20px; font-size:10px'><?php echo get_string('twitterfollowinfo', 'twitter'); ?></div>
</form>
</div>
<?php
}
//->END
/// Finish the page
print_footer($course);
?>
