<?php
/**
 * @author Lukas Biermann
 * @author Nina Herrmann
 * @author Wladislaw Iwanzow
 * @author Dennis Meis
 * @author Jonathan Neugebauer
 *
 * @copyright Copyright (c) 2016, Project Seminar "PSSL16" at the University of Muenster.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

namespace OCA\OAuth2\AppInfo;

use OCP\AppFramework\App;
use OCA\OAuth2\Hooks\UserHooks;
use OCA\OAuth2\Db\AccessTokenMapper;


class Application extends App {

    /**
     * Application constructor.
     *
     * @param array $urlParams an array with variables extracted from the routes
     */
    public function __construct(array $urlParams=array()){
        parent::__construct('oauth2', $urlParams);

    $container = $this->getContainer();

		/**
		 * Hooks
		 */

		$container->registerService('UserHooks', function($c) {
			return new UserHooks(
				$c->query('ServerContainer')->getUserManager(),
				$c->query('AccessTokenMapper')
			);
		});

		/**
		 * Mapper (needed for the hooks)
		 */

		$container->registerService('AccessTokenMapper', function($c){
			return new AccessTokenMapper(
				$c->query('ServerContainer')->getDb()
			);
		});

    }

	/**
	 * Registers settings pages.
	 */

    public function registerSettings() {
		\OCP\App::registerAdmin('oauth2', 'settings-admin');
		\OCP\App::registerPersonal('oauth2', 'settings-personal');
	}

}
