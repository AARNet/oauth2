<?php
/**
 * @author Project Seminar "sciebo@Learnweb" of the University of Muenster
 * @copyright Copyright (c) 2017, University of Muenster
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

namespace OCA\OAuth2;

use OCA\OAuth2\AppInfo\Application;
use OCA\OAuth2\Db\AccessToken;
use OCA\OAuth2\Db\AccessTokenMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\Authentication\IAuthModule;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserManager;

class AuthModule implements IAuthModule {

	/**
	 * Authenticates a request.
	 *
	 * @param IRequest $request The request.
	 *
	 * @return null|IUser The user if the request is authenticated, null otherwise.
	 * @throws \Exception
	 */
	public function auth(IRequest $request) {
		$authHeader = $request->getHeader('Authorization');

		if (strpos($authHeader, 'Bearer ') === false) {
			return null;
		}

		$bearerToken = substr($authHeader, 7);

		$user = $this->authToken($bearerToken);
		if ($user === null) {
			throw new \Exception('Invalid token');
		}
		return $user;
	}

	/**
	 * Returns an empty string because the user's password is not handled in
	 * the app.
	 *
	 * Note: This means that only master key encryption is working with the app.
	 *
	 * @param IRequest $request The request.
	 *
	 * @return String An empty string.
	 */
	public function getUserPassword(IRequest $request) {
		return '';
	}

	/**
	 * @param $bearerToken
	 * @return null|IUser
	 */
	public function authToken($bearerToken) {
		$app = new Application();
		$container = $app->getContainer();

		/** @var AccessTokenMapper $accessTokenMapper */
		$accessTokenMapper = $container->query('OCA\OAuth2\Db\AccessTokenMapper');

		try {
			/** @var AccessToken $accessToken */
			$accessToken = $accessTokenMapper->findByToken($bearerToken);

			if ($accessToken->hasExpired()) {
				return null;
			}
		} catch (DoesNotExistException $exception) {
			return null;
		}

		/** @var IUserManager $userManager */
		$userManager = $container->query('UserManager');
		return $userManager->get($accessToken->getUserId());
	}

}
