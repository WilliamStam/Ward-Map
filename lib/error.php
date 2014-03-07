<?php
/**
 * Custom error handling
 */
class Error {
	/**
	 * Handle error/s for specific error codes
	 *
	 * @param  object $f3 FatFree instance
	 * @return mixed      Custom error/Default FatFree error
	 */
	public static function handler($f3) {
		// F3 error: code, text, trace
		$error = $f3->get('ERROR');

		// custom error/s
		switch ($error['code']) {
			case 403:
				$f3->reroute("/user/login?return=" . $f3->get("return_here"));
				break;
			case 404:
				$page = models\default_pages::get("error404");
				$p = new controllers\front\pages();
				$p->load($page, $error);
				break;

			default:
				// restore F3 error handler (hackish)
				$f3->mset(array(
					          'ONERROR' => null,
					          'ERROR'   => null
				          )
				);
				$f3->error($error['code'], $error['text'], $error['trace']);
				break;
		}
		// exit after custom action
		exit;
	}
}