<?php

namespace Buckaroo\Magento2\Model\Push;

use Buckaroo\Magento2\Api\PushRequestInterface;
use Buckaroo\Magento2\Helper\Data;
use Buckaroo\Magento2\Service\LockerProcess;
use Magento\Framework\Exception\FileSystemException;

class IdealProcessor extends DefaultProcessor
{
    public const BUCK_PUSH_IDEAL_PAY = 'C021';

    /**
     * @var Data
     */
    public Data $helper;

    /**
     * @var LockerProcess
     */
    private LockerProcess $lockerProcess;

    public function __construct(
        LockerProcess $lockerProcess,
        Data $helper
    ) {
        $this->lockerProcess = $lockerProcess;
        $this->helper = $helper;
    }

    /**
     * @throws FileSystemException
     */
    public function processPush(PushRequestInterface $pushRequest): void
    {
        $this->pushRequest = $pushRequest;

        if ($this->lockPushProcessingCriteria()) {
            $this->lockerProcess->lockProcess($this->getOrderIncrementId());
        }
        parent::processPush($pushRequest);

        $this->lockerProcess->unlockProcess();
    }

    /**
     * Determine if the lock push processing criteria are met.
     *
     * @return bool
     */
    private function lockPushProcessingCriteria(): bool
    {
        $statusCodeSuccess = $this->helper->getStatusCode('BUCKAROO_MAGENTO2_STATUSCODE_SUCCESS');

        return ($this->pushRequest->hasPostData('statuscode', $statusCodeSuccess)
            && $this->pushRequest->hasPostData('transaction_type', self::BUCK_PUSH_IDEAL_PAY));
    }

    public function processSucceded()
    {
        $statusCodeSuccess = $this->helper->getStatusCode('BUCKAROO_MAGENTO2_STATUSCODE_SUCCESS');

        if ($this->pushRequest->hasPostData('statuscode', $statusCodeSuccess)
            && $this->pushRequest->hasPostData('transaction_method', 'ideal')
            && $this->pushRequest->hasPostData('transaction_type', self::BUCK_PUSH_IDEAL_PAY)
        ) {
            return true;
        }
    }

    public function processFailed()
    {
        // TODO: Implement processFailed() method.
    }


}