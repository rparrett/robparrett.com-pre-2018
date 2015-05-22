<?php

class Bootstrap extends Yaf_Bootstrap_Abstract {
	public function _initRoutes(yaf_Dispatcher $dispatcher) {
		$router = $dispatcher->getRouter();
		$router->addRoute(
			"login",
			new Yaf_Route_Rewrite(
				"/login/:redirect",
				array(
					"controller" => "index",
					"action" => "login"
				)
			)
		);
	}

	public function _initYaf(Yaf_Dispatcher $dispatcher) {
		ini_set('yaf.action_prefer', 1);
		ini_set('yaf.library', '/library');
	}

	public function _initErrors() {
		if (Yaf_Application::app()->getConfig()->application->showDetailedErrors) {
			error_reporting (-1);
			ini_set('display_errors','On');
		}
	}

	public function _initDependencies(Yaf_Dispatcher $dispatcher) {
		$dic = new GhettoDIC();
		$dic->set('db', function() {
			return new SQLite3(Yaf_Application::app()->getConfig()->application->sqlite3->path);
		});
		$dic->set('withings', function() {
			return new WithingsAPIClient(
				Yaf_Application::app()->getConfig()->application->withings->oauthToken,
				Yaf_Application::app()->getConfig()->application->withings->oauthTokenSecret,
				Yaf_Application::app()->getConfig()->application->withings->oauthConsumerKey,
				Yaf_Application::app()->getConfig()->application->withings->oauthConsumerSecret
			);
		});
		$dic->set('authenticationModel', function() {
			return new AuthenticationModel(Yaf_Registry::get('dic')->get('db'));
		});
		$dic->set('withingsModel', function() {
			return new WithingsModel(
				Yaf_Registry::get('dic')->get('db'), 
				Yaf_Registry::get('dic')->get('withings')
			);
		});
		$dic->set('bunnyModel', function() {
			return new BunnyModel(Yaf_Application::app()->getConfig()->application->toArray());
		});
		
		Yaf_Registry::set('dic', $dic);
	}
}
