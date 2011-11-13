<?php  // $Id: lib.php,v 1.7.2.5 2009/04/22 21:30:57 skodak Exp $

/**
 * Library of functions and constants for module twitter
 * This file should have two well differenced parts:
 *   - All the core Moodle functions, neeeded to allow
 *     the module to work integrated in Moodle.
 *   - All the twitter specific functions, needed
 *     to implement all the module logic. Please, note
 *     that, if the module become complex and this lib
 *     grows a lot, it's HIGHLY recommended to move all
 *     these module specific functions to a new php file,
 *     called "locallib.php" (see forum, quiz...). This will
 *     help to save some memory when Moodle is performing
 *     actions across all modules.
 */

// moodle www-root directory, e.g. http://localhost/moodle
define("twitter_WWWROOT", "http://alexander.kumbeiz.de/moodle");


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $twitter An object from the form in mod_form.php
 * @return int The id of the newly inserted twitter record
 */
function twitter_add_instance($twitter) {

    $twitter->timecreated = time();

    # You may have to add extra stuff in here #

    return insert_record('twitter', $twitter);
}


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $twitter An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function twitter_update_instance($twitter) {

    $twitter->timemodified = time();
    $twitter->id = $twitter->instance;

    # You may have to add extra stuff in here #

    return update_record('twitter', $twitter);
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function twitter_delete_instance($id) {

    if (! $twitter = get_record('twitter', 'id', $id)) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records('twitter', 'id', $twitter->id)) {
        $result = false;
    }

    return $result;
}


/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function twitter_user_outline($course, $user, $mod, $twitter) {
    return $return;
}


/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function twitter_user_complete($course, $user, $mod, $twitter) {
    return true;
}


/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in twitter activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function twitter_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}


/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function twitter_cron () {
    require_once(dirname(__FILE__).'/api/twitter.php');
	//require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
	//$myFile = "log_cron.txt";
	//$fh = fopen($myFile, 'a') or die("can't open file");
	$twitter_accounts = get_records('twitter',null,'','','id,course,name,account_name,account_password,last_time_executed');
	
	if($twitter_accounts!=NULL)	
	foreach($twitter_accounts as $account){
		$executed = time();
		// get changes in course, added and updatet modules itself
		$changelist = twitter_getChangelist($account->course, $account->last_time_executed);
		// get changes in modules, added and updatet entries
		$changelist = array_merge($changelist, twitter_getModuleChangelist($account->course, $account->last_time_executed));
		// post messages to twitter
		foreach ($changelist as $changeinfo => $change) {
            $msg_send = twitter_call($account->account_name, $account->account_password,"http://twitter.com/statuses/update.xml?status=".urlencode(stripslashes(urldecode($change[text]))),"POST");
			if(isset($msg_send->error)) {
				echo "error in twitter request\n";
			}
        }
		
		$account->last_time_executed = $executed;
		update_record('twitter', $account);
	}
	return true;
}


/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of twitter. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $twitterid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function twitter_get_participants($twitterid) {
    return false;
}


/**
 * This function returns if a scale is being used by one twitter
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $twitterid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function twitter_scale_used($twitterid, $scaleid) {
    $return = false;

    //$rec = get_record("twitter","id","$twitterid","scale","-$scaleid");
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}


/**
 * Checks if scale is being used by any instance of twitter.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any twitter
 */
function twitter_scale_used_anywhere($scaleid) {
    if ($scaleid and record_exists('twitter', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}


/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function twitter_install() {
    return true;
}


/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function twitter_uninstall() {
    return true;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other twitter functions go here.  Each of them must have a name that
/// starts with twitter_
/// Remember (see note in first lines) that, if this section grows, it's HIGHLY
/// recommended to move all funcions below to a new "localib.php" file.

// returns an array with information to post on twitter
function twitter_getChangelist($course, $timestart) {

    $changelist = array();
	// get changes of all visible modules in course which are newer than 'timestart'
    $logs = get_records_select('log', "time > $timestart AND course = $course AND
                                       module = 'course' AND
                                       (action = 'add mod' OR action = 'update mod' OR action = 'delete mod')",
                               "id ASC");
	
    if ($logs) {
        $actions  = array('add mod', 'update mod', 'delete mod');
        $newgones = array(); // added and later deleted items
        foreach ($logs as $key => $log) {
            if (!in_array($log->action, $actions)) {
                continue;
            }
            $info = split(' ', $log->info);
			
            if ($info[0] == 'label') {     // Labels are ignored in recent activity
                continue;
            }
			if ($info[0] == 'twitter') {   // Twitter modules are also ignored
				continue;
			}
			// if info entry is incorrect
            if (count($info) != 2) {
                debugging("Incorrect log entry info: id = ".$log->id, DEBUG_DEVELOPER);
                continue;
            }

            $modname    = $info[0];
            $instanceid = $info[1];
			// get course name
			$course_record = get_record('course', 'id', $log->course);
			$course_name = $course_record->shortname;
			
            if ($log->action == 'delete mod') {
                // unfortunately we do not know if the mod was visible
				// it will be skipped, if you want to show it nevertheless, comment next line
				continue;
                if (!array_key_exists($log->info, $newgones)) {
                    $strdeleted = get_string('deletedactivity', 'moodle', get_string('modulename', $modname));
                    $changelist[$log->info] = array ('operation' => 'delete', 'text' => "$course_name, $strdeleted");
                }
            } else {
				// get module id
				$modid = explode('=', $log->url);
				$modid = $modid[1];
				// check if module is visible
				$module_record = get_record('course_modules', 'id', $modid);
				// if module visible post to twitter
				if($module_record->visible)
                if ($log->action == 'add mod') {
					// add-message with modulename
                    $stradded = get_string('added', 'moodle', get_string('modulename', $modname));
					// message with course-name add-message and link
                    $changelist[$log->info] = array('operation' => 'add', 'text' => "$course_name, $stradded: ".get_config('twitter', 'wwwroot').substr($log->url,2));
                } else if ($log->action == 'update mod' and empty($changelist[$log->info])) {
					// update-message with modulename
                    $strupdated = get_string('updated', 'moodle', get_string('modulename', $modname));
					// message with course-name update-message and link
                    $changelist[$log->info] = array('operation' => 'update', 'text' => "$course_name, $strupdated: ".get_config('twitter', 'wwwroot').substr($log->url,2));
                }
            }
        }
    }
	return $changelist;
}

// returns an array with information to post on twitter
function twitter_getModuleChangelist($course, $timestart) {
	$changelist = array();
	// select all visible module additions and updates from current course which is newer than 'timestart'
	$logs = get_records_sql("SELECT log.time, log.url, log.module, log.action, log.info, log.course FROM `modules` 
								join course_modules on modules.id=course_modules.module
								join log on course_modules.id=log.cmid
								WHERE course_modules.visible='1' AND log.course=$course AND log.module != 'twitter' AND 
								log.module != 'label' AND (log.action like 'add%' OR log.action like 'update%') 
								AND log.time > $timestart");

	foreach($logs as $key => $log) {
		// get course name
		$course_record = get_record('course', 'id', $log->course);
		$course_name = $course_record->shortname;
		if(strncmp($log->action, "add", 3) == 0) {
			// add-message with modulename
			$stradded = get_string('modulename', $log->module).". ".get_string('twitternewentry', 'twitter');
			// message with course-name add-message and link
			$changelist[$log->info] = array('operation' => 'add', 'text' => "$course_name, $stradded: ".get_config('twitter', 'wwwroot')."/mod/$log->module/".$log->url);
		} else {
			// update-message with modulename
			$strupdated = get_string('modulename', $log->module).". ".get_string('twitterupdateentry', 'twitter');
			// message with course-name update-message and link
			$changelist[$log->info] = array('operation' => 'update', 'text' => "$course_name, $strupdated: ".get_config('twitter', 'wwwroot')."/mod/$log->module/".$log->url);
		}
	}
	
	return $changelist;
}

?>
