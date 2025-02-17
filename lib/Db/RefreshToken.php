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

namespace OCA\OAuth2\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getToken()
 * @method void setToken(string $token)
 * @method int getClientId()
 * @method void setClientId(int $clientId)
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method int getAccessTokenId()
 * @method void setAccessTokenId(int $accessTokenId)
 */
class RefreshToken extends Entity {
	const EXPIRATION_DAYS = 30;

	protected $token;
	protected $clientId;
	protected $userId;
	protected $accessTokenId;
	protected $expires;

	public function __construct() {
		$this->addType('id', 'int');
		$this->addType('token', 'string');
		$this->addType('client_id', 'int');
		$this->addType('user_id', 'string');
		$this->addType('access_token_id', 'int');
		$this->addType('expires', 'int');
	}

	/**
	* Resets the expiry time to EXPIRATION_DAYS days from now.
	*/
	public function resetExpires() {
		$this->setExpires(\time() + (self::EXPIRATION_DAYS * 86400));
	}

	/**
	* Determines if an refresh token has expired.
	*
	* @return boolean true if the refresh token has expired, false otherwise.
	*/
	public function hasExpired() {
		return \time() >= $this->getExpires();
	}
}
