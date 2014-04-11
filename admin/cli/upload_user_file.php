<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script allows to perform uploading of the file to user private files area.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2014 Lancaster University, Ruslan Kabalin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', 1);

require(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot . '/repository/lib.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(array(
    'username' => '',
    'filepath' => '',
    'help' => false,
    ), array('h' => 'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help'] || !$options['username'] || !$options['filepath']) {
    $help = <<<EOL
Perform uploading of the file to user private files area.

Options:
--username=STRING           Moodle user.
--filepath=STRING           Path where file to upload is located.
-h, --help                  Print out this help.

Example:
\$sudo -u www-data /usr/bin/php admin/cli/upload_user_file.php --username=admin --filepath=/mount/drive/some_huge_file.ext

EOL;

    echo $help;
    die;
}

// Do we need to store backup somewhere else?
$filepath = trim($options['filepath']);
if (!file_exists($filepath) || !is_readable($filepath)) {
    mtrace("File does not exists or not readable.");
    die;
}

// Check that user exists.
$user = $DB->get_record('user', array('username'=>$options['username']), '*');
if (empty($user)) {
    mtrace("User does not exist, can't proceed.");
    die;
}
if (isguestuser($user)) {
    mtrace("Specified user is guest, can't proceed.");
    die;
}
if ($user->deleted) {
    mtrace("Specified user is deleted, can't proceed.");
    die;
}

cli_heading('Performing uploading...');

// Scan for viruses.
repository::antivir_scan_file($filepath, basename($filepath), false);
mtrace("File '$filepath' is being uploaded to user " . $user->username . " private files area.");

// Do the actual upload.
$fs = get_file_storage();
$context = context_user::instance($user->id);
$filename = $fs->get_unused_filename($context->id, 'user', 'private', 0, '/', basename($filepath));
$filerecord = new stdClass;
$filerecord->component = 'user';
$filerecord->contextid = $context->id;
$filerecord->filearea  = 'private';
$filerecord->filename  = $filename;
$filerecord->filepath  = '/';
$filerecord->itemid    = 0;
$fs->create_file_from_pathname($filerecord, $filepath);

mtrace("Uploading completed, the new file '" . $filename . "' has been added to the private files area of user " . $user->username . ".");
exit(0);