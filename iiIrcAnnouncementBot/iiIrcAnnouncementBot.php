<?php

class iiIrcAnnouncementBotPlugin extends MantisPlugin
{
	function register()
	{
		$this->name = 'II Irc Annoncement Bot';                          # Proper name of plugin
		$this->description = 'Writes new issues to the II IRC bot pipe (<a href="http://tools.suckless.org/ii/">II</a>).'; # Short description of the plugin
		$this->page = '';                                                # Default plugin page

		$this->version = '0.1.1';     # Plugin version string
		$this->requires = array(      # Plugin dependencies, array of basename => version pairs
			'MantisCore' => '1.2.14', #   Should always depend on an appropriate version of MantisBT
		);

		$this->author = "Kevin 'Cyberkef' Gaytant";    # Author/team name
		$this->contact = 'Kevin.Gaytant@ArgusTech.be'; # Author/team e-mail address
		$this->url = 'http://www.argustech.be';        # Support webpage
	}

	function config()
	{
		// Enter the exact configuration as your ii setup
		return array(
			'path' => '/tmp/mantisircbot',
			'server' => 'irc.server.org',
			'channel' => '#channel',
		);
	}

	function hooks()
	{
		return array(
			/* This event allows plugins to perform post-processing of the bug data structure after being reported from the user and being saved to the database.
			 * At this point, the issue ID is actually known, and is passed as a second parameter.
			 *
			 * Parameters:
			 *     <Complex>: Bug data structure (see core/bug_api.php)
			 *     <Integer>: Bug ID
			 */
			'EVENT_REPORT_BUG' => 'announce_issue',

			/* This event allows plugins to do post-processing of bugnotes added to an issue.
			 *
			 * Parameters:
			 *     <Integer>: Bug ID
			 *     <Integer>: Bugnote ID
			 */
			'EVENT_BUGNOTE_ADD' => 'announce_note',
		);
	}

	function announce_issue($event_name, $bug_data_structure, $bug_id)
	{
		file_put_contents(plugin_config_get('path') . '/' . plugin_config_get('server') . '/' . plugin_config_get('channel') . '/in', '[New Issue @ ' . project_get_name($bug_data_structure->project_id) . '] ' . $bug_data_structure->summary . ' (by ' . user_get_name($bug_data_structure->reporter_id) . ') http://bugtracker.powermonitor.be/view.php?id=' . $bug_id . "\n",  FILE_APPEND);
	}

	function announce_note($event_name, $bug_id, $bugnote_id)
	{
		file_put_contents(plugin_config_get('path') . '/' . plugin_config_get('server') . '/' . plugin_config_get('channel') . '/in', '[New Note] http://bugtracker.powermonitor.be/view.php?id=' . $bug_id . '#c' . $bugnote_id . "\n",  FILE_APPEND);
	}
}