<?php
// Add $_CONFIG['_dontStartSession'] to stop kcfinder from starting it's own session
$_CONFIG['_dontStartSession'] = true;

// Drupal integration utility functions
spl_autoload_register('__autoload');

// gets a valid drupal_path
function get_drupal_path() {
	if (!empty($_SERVER['SCRIPT_FILENAME'])) {
		$drupal_path = dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))));
		if (!file_exists($drupal_path . '/includes/bootstrap.inc')) {
			$drupal_path = dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));
			$depth = 2;
			do {
				$drupal_path = dirname($drupal_path);
				$depth++;
			} while (!($bootstrap_file_found = file_exists($drupal_path . '/includes/bootstrap.inc')) && $depth < 10);
		}
	}

	if (!isset($bootstrap_file_found) || !$bootstrap_file_found) {
		$drupal_path = '../../../../..';
		if (!file_exists($drupal_path . '/includes/bootstrap.inc')) {
			$drupal_path = '../..';
			do {
				$drupal_path .= '/..';
				$depth = substr_count($drupal_path, '..');
			} while (!($bootstrap_file_found = file_exists($drupal_path . '/includes/bootstrap.inc')) && $depth < 10);
		}
	}
	return $drupal_path;
}

function CheckAuthentication($drupal_path) {
    static $authenticated;
	
    if (!isset($authenticated)) {
        
		if (!isset($bootstrap_file_found) || $bootstrap_file_found) {
			$current_cwd = getcwd();
			if (!defined('DRUPAL_ROOT')){
				define('DRUPAL_ROOT', $drupal_path);
			}
			
			// Simulate being in the drupal root folder so we can share the session
			chdir(DRUPAL_ROOT);
			
			global $base_url;
			$base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
			$base_url = $base_root .= '://'. preg_replace('/[^a-z0-9-:._]/i', '', $_SERVER['HTTP_HOST']);
			
			if ($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) {
				$base_path = "/$dir";
				$base_url .= $base_path;
			}
			
			// correct base_url so it points to Drupal root
			$pos = strpos($base_url, '/sites/');
			$base_url = substr($base_url, 0, $pos); // drupal root absolute url
			
			// bootstrap
			require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
			drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
			
			// if user has access permission...
			if ($authenticated = user_access('access kcfinder')) {
				if (!isset($_SESSION['KCFINDER'])) {
					$_SESSION['KCFINDER'] = array();
					$_SESSION['KCFINDER']['disabled'] = false;
				}
				global $user;
				$_SESSION['KCFINDER']['uploadURL'] = strtr(variable_get('kcfinder_upload_url', 'sites/default/files/kcfinder'), array('%u' => $user->uid, '%n' => $user->name));
				$_SESSION['KCFINDER']['uploadDir'] = variable_get('kcfinder_upload_dir', '');
				
				//echo 'uploadURL: ' . $_SESSION['KCFINDER']['uploadURL'];
				//echo 'uploadDir: ' . $_SESSION['KCFINDER']['uploadDir'];
				
				chdir($current_cwd);
				
				return true;
			}
			
			chdir($current_cwd);
			return false;
		}
    }
}

CheckAuthentication(get_drupal_path());
?>