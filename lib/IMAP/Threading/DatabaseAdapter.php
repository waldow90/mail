<?php

declare(strict_types=1);

/**
 * @copyright 2020 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2020 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Mail\IMAP\Threading;

use Horde_Mail_Rfc822_Identification;
use OCA\Mail\Db\Message as DbMessage;

class DatabaseAdapter {
	public function toModel(DbMessage $dbMessage): Message {
		return new Message(
			$dbMessage->getSubject(),
			$dbMessage->getMessageId(),
			$this->getReferences(
				$dbMessage->getReferences(),
				$dbMessage->getInReplyTo()
			)
		);
	}

	/**
	 * The References field is populated from the ``References'' and/or ``In-Reply-To'' headers. If both headers exist, take the first thing in the In-Reply-To header that looks like a Message-ID, and append it to the References header.
	 *
	 * @see https://www.jwz.org/doc/threading.html
	 */
	private function getReferences(?string $rawReferences,
								   ?string $rawInReplyTo): array {
		$parsedReferences = new Horde_Mail_Rfc822_Identification($rawReferences);
		$references = $parsedReferences->ids;
		$parsedInReplyTo = new Horde_Mail_Rfc822_Identification($rawInReplyTo);
		if (!empty($parsedInReplyTo->ids)) {
			$references[] = $parsedInReplyTo->ids[0];
		}
		return $references;
	}

	public function toDatabase(Message $message): DbMessage {
	}
}
