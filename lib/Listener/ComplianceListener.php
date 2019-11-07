<?php declare(strict_types=1);

namespace OCA\Mail\Listener;

use OCA\Mail\AppInfo\Application;
use OCP\Compliance\Event\ComplianceRequestEvent;
use OCP\Compliance\IGdprExportRequest;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;

class ComplianceListener implements IEventListener {

	public function handle(Event $event): void {
		if (!($event instanceof ComplianceRequestEvent)) {
			return;
		}

		$request = $event->getRequest();
		if ($request instanceof IGdprExportRequest) {
			$request->accept(Application::APP_ID);
		}
	}

}
