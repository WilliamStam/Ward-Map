<?php
/*
 * Date: 2011/06/27
 * Time: 4:33 PM
 */

class email {
	private $config = array(), $vars = array();

	function __construct($template, $folder = "") {
		$this->f3  = \base::instance();
		$this->folder = $folder;
		$this->template = $template;
		$this->cfg = $this->f3->get("cfg");
	}

	public function __get($name) {
		return $this->vars[$name];
	}

	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}

	public function send($to, $subject, $from) {
		$timer = new timer();
		if ($to) $this->vars['to'] = $to;
		if ($from) {
			$this->vars['from'] = $from;
		} else {
			$this->vars['from_name'] = $this->cfg['contact']['name'];
			$this->vars['from'] = $this->cfg['contact']['email'];
		}
		if ($subject) $this->vars['subject'] = $subject;





		ob_start();


		$folder = $this->folder;
		

		$loader = new Twig_Loader_Filesystem($folder);
		$twig = new Twig_Environment($loader, array(//'cache' => $this->config['cache_dir'],
		));


		//test_array($this->vars);

		echo $twig->render($this->template, $this->vars);


		$t = ob_get_contents();
		ob_end_clean();

		





		

		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Additional headers
		$headers .= 'To: <' . $this->vars['to'] . '>' . "\r\n";
		if (isset($this->vars['from_name'])){
			$headers .= 'From: ' . $this->vars['from_name'] . ' <' . $this->vars['from'] . '>' . "\r\n";
		} else {
			$headers .= 'From: ' . $this->vars['from'] . '' . "\r\n";
		}

		$mail = @mail($this->vars['to'], $this->vars['subject'], $t, $headers);
		$arg = array(
			"To"=> $this->vars['to'],
			"Subject"=> $this->vars['subject'],
			"Status"=> ($mail) ? "Success" : "failed"
		);
		$timer->stop("Email", $arg);
		// Mail it
		return $mail;





	}


}
