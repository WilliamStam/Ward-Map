<?php
/*
 * Date: 2011/06/27
 * Time: 4:33 PM
 */

class template {
	private $config = array(), $vars = array();

	function __construct($template, $folder = "") {
		$this->f3 = Base::instance();
		$this->config['cache_dir'] = $this->f3->get('TEMP');
		$this->vars['folder'] = $folder;
		$this->template = $template;
		$this->timer = new \timer();




	}
	function __destruct(){
		$page = $this->template;
		//test_array($page);
		if (isset($this->vars['page']['template'])){
			$page = $page . " -> " . $this->vars['folder'] . $this->vars['page']['template'];
		}
		$this->timer->stop("Template",  $page);
	}

	public function __get($name) {
		return $this->vars[$name];
	}

	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}


	public function load() {

		$curPageFull = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$cfg = $this->f3->get('CFG');
		unset($cfg['DB']);
		unset($cfg['package']);

		$v = $this->f3->get("VERSION");



		$this->vars['_nav_top'] ="_nav_top.tmpl";
		$this->vars['_v'] = $v;


		
		$cfg = $this->f3->get('CFG');
		unset($cfg['DB']);
		unset($cfg['package']);


		//$this->vars['_nav_top'] = $this->vars['folder'] . "_nav_top.tmpl";




		$this->vars['_uri'] = $_SERVER['REQUEST_URI'];
		$this->vars['_folder'] = $this->vars['folder'];
		$this->vars['_version'] = $this->f3->get('version');
		$this->vars['_cfg'] = $cfg;
		$this->vars['_docs'] = $this->f3->get('docs');
		$this->vars['isLocal'] = isLocal();
		


		//test_array($this->vars);
		


		if (isset($this->vars['page'])) {
			$page = $this->vars['page'];
			$tfile = $page['template'];

			$page['template'] = $tfile . '.tmpl';

			$folder = $this->vars['folder'];

			$page['js'] = array();
			$page['css'] = array();
			if (isset($this->vars['page']['js'])) {
				if (is_array($this->vars['page']['js'])){
					foreach ($this->vars['page']['js'] as $item){
						if ($item) $page['js'][] = $item;
					}
				} else {
					if ($this->vars['page']['js']) $page['js'][] = $this->vars['page']['js'];
				}
			}

			if (file_exists('' . $folder . '_js/' . $tfile . '.js')) {
				$page['js'][] = '/' . $folder . '_js/' . $tfile .".". $v.'.js';
			}


			if (isset($this->vars['page']['css'])) {
				if (is_array($this->vars['page']['css'])) {
					foreach ($this->vars['page']['css'] as $item) {
						if ($item) $page['css'][] = $item;
					}
				} else {
					if ($this->vars['page']['css']) $page['css'][] = $this->vars['page']['css'];
				}
			}


			if (file_exists('' . $folder . '_css/' . $tfile . '.css')) {
				$page['css'][] = '/' . $folder . '_css/' . $tfile .".". $v.'.css';
			}
			if (file_exists('' . $folder . '_css/style.css')) {
				$page['css'][] = '/' . $folder . '_css/style.'.$v.'.css';
			}



			if (file_exists('' . $folder . 'templates/' . $tfile . '_templates.jtmpl')) {
				$page['template_tmpl'] = 'templates/' . $tfile . '_templates.jtmpl';
			} else {
				if (!isset($page['template_tmpl'])) $page['template_tmpl'] = "";
			}
			if (file_exists('' . $folder . 'templates/' . $tfile . '.jtmpl')) {
				$page['template_tmpl'] = 'templates/' . $tfile . '.jtmpl';
			} else {
				if (!isset($page['template_tmpl'])) $page['template_tmpl'] = "";
			}


			if (!isset($page['help']) || !$page['help']){
				$app = $this->f3->get("app");
				$sub_section = $page['sub_section'];
				if (strpos($sub_section,"_")){
					$sub_section = explode("_", $page['sub_section']);
					$sub_section = implode("/", $sub_section);
					$page['help'] = "/app/$app/documentation/" . $page['section'] . "/" . $sub_section;
				} else {
					$page['help'] = "/app/$app/documentation/" . $page['section'] . "/" . $page['sub_section'];
				}

			}




			//test_array($page);

			


			//test_array($page);
			$this->vars['page'] = $page;
			return $this->render_template();
		} else {
			if (isset($this->vars['docs']['file']) || isset($this->vars['render'])){
				return $this->render_template();
			} else {
				return $this->render_string();
			}

		}




	}

	public function render_template() {

		if (is_array($this->vars['folder'])){
			$folder = $this->vars['folder'];
		} else {
			$folder = array(
				"ui/",
				$this->vars['folder']
			);
		}




		$loader = new Twig_Loader_Filesystem($folder);
		$twig = new Twig_Environment($loader, array(
			//'cache' => $this->config['cache_dir'],
		));


		//test_array($this->vars);

		return $twig->render($this->template, $this->vars);


	}

	public function render_string() {
		$loader = new Twig_Loader_String();
		$twig = new Twig_Environment($loader);
		return $twig->render($this->vars['template'], $this->vars);
	}


	public function output() {
		$this->f3->set("__runTemplate", true);
		$this->f3->set("json", false);
		echo $this->load();

	}

}
