<?php

class IndexController extends Yaf_Controller_Abstract {
	private $_layout;

	public function init() {
		$this->_layout = new LayoutPlugin('layout.phtml');
		Yaf_Dispatcher::getInstance()->registerPlugin($this->_layout);

		$auth = Yaf_Registry::get('dic')->get('authenticationModel');
		$auth->authenticateCookie();

		$this->_layout->isAuthenticated = $auth->isAuthenticated();
	}

	public function indexAction() {
	}

	public function fanbotAction() {
		$this->_layout->subtitle = 'Fanbot';
	}

	public function sbcAction() {
		$this->_layout->subtitle = 'Small Beverage Collider';
	}

	public function lbcAction() {
		$this->_layout->subtitle = 'Large Beverage Collider';
	}

	public function pongclockAction() {
		$this->_layout->subtitle = 'Pongclock';
	}

	public function biometricsAction() {
		$this->_layout->subtitle = 'Biometrics';
		$this->_layout->include_scripts = array(
			"//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js",
			"//code.highcharts.com/stock/highstock.js"
		);

		$withings = Yaf_Registry::get('dic')->get('withingsModel');
		$weights = $withings->getWeightsLocal(7862);

		$this->_view->data = $withings->formatWeightsForHighCharts($weights);
	}

	public function tipitAction() {
		$this->_layout->subtitle = 'TipiT';
		$this->_layout->include_scripts = array(
			"//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js",
			"/static/js/nouislider/jquery.nouislider.all.min.js",
			"/static/js/tipit.js"
		);
		$this->_layout->include_css = array(
			"/static/js/nouislider/jquery.nouislider.min.css"
		);
	}

	public function bunnyAction() {
		$this->_layout->subtitle = 'Bunny';

		$auth = Yaf_Registry::get('dic')->get('authenticationModel');
		if (!$auth->isAuthenticated()) {
			$this->redirect('/login/' . Util::base64url_encode('/bunny'));
			return false;
		}

		$bunny = Yaf_Registry::get('dic')->get('bunnyModel');	

		$ago = Util::time_ago($bunny->getLastFed());

		if (isset($_POST['feed'])) {
			$result = $bunny->feed(isset($_POST['test']));

			if (!$result) {
				$this->_view->error = $bunny->getFeedError();
			}

			$ago = "1 second";
		}

		$this->_view->ago = $ago;
	}

	public function rngAction() {
		$this->_layout->subtitle = 'Random Name Generator';

		$config = Yaf_Application::app()->getConfig()->application->randomname->toArray();
		$rng = new RandomNameGenerator($config);

		$names = array();
		for ($i = 0; $i < 20; $i++) {
			$names[] = $rng->get(20);
		}

		$this->_view->names = $names;
	}

	public function loginAction($redirect = false) {
		$this->_layout->subtitle = 'Login';

		if (!$redirect) {
			$redirect = '/';
		} else {
			$this->_view->error = 'You must be logged in to view this page.';
			$redirect = Util::base64url_decode($redirect);
		}

		if (isset($_POST['username']) && isset($_POST['password'])) {
			$auth = Yaf_Registry::get('dic')->get('authenticationModel');

			$user = $auth->authenticate($_POST['username'], $_POST['password'], isset($_POST['cookie']));
			if ($user === false) {
				$this->_view->error = 'Login failed.';
				return true;
			}

			$this->redirect($redirect);
		}		
	}

	public function logoutAction() {
		$auth = Yaf_Registry::get('dic')->get('authenticationModel');

		$auth->logout();
		$this->redirect('/');
		return false;
	}
}
