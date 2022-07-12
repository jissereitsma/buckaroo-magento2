<?php

namespace Buckaroo\Magento2\Gateway\Request;

use Buckaroo\Magento2\Model\ConfigProvider\Account;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order;

class ClientIPDataBuilder implements BuilderInterface
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @var Account
     */
    private $configProviderAccount;

    /**
     * Constructor
     *
     * @param Account $configProviderAccount
     */
    public function __construct(
        Account $configProviderAccount
    ) {
        $this->configProviderAccount = $configProviderAccount;
    }

    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $payment = $buildSubject['payment'];
        $this->setOrder($payment->getOrder()->getOrder());

        $ip = $this->getIp($this->getOrder());

        return [
            'ClientIP' => (object)[
                '_' => $ip,
                'Type' => strpos($ip, ':') === false ? 'IPv4' : 'IPv6',
            ],
        ];
    }

    protected function getIp($order)
    {
        $ip = $order->getRemoteIp();
        $store = $order->getStore();

        $ipHeaders = $this->configProviderAccount->getIpHeader($store);

        if ($ipHeaders) {
            $ipHeaders = explode(',', (string)strtoupper($ipHeaders));
            foreach ($ipHeaders as &$ipHeader) {
                $ipHeader = 'HTTP_' . str_replace('-', '_', (string)$ipHeader);
            }
            $ip = $order->getPayment()->getMethodInstance()->getRemoteAddress(false, $ipHeaders);
        }

        //trustly anyway should be w/o private ip
        if ((isset($order->getPayment()->getMethodInstance()->buckarooPaymentMethodCode) &&
                $order->getPayment()->getMethodInstance()->buckarooPaymentMethodCode == 'trustly'
            )
            && $this->isIpPrivate($ip)
            && $order->getXForwardedFor()
        ) {
            $ip = $order->getXForwardedFor();
        }

        if (!$ip) {
            $ip = $order->getPayment()->getMethodInstance()->getRemoteAddress();
        }

        return $ip;
    }

    private function isIpPrivate($ip)
    {
        if (!$ip) {
            return false;
        }

        $pri_addrs = [
            '10.0.0.0|10.255.255.255', // single class A network
            '172.16.0.0|172.31.255.255', // 16 contiguous class B network
            '192.168.0.0|192.168.255.255', // 256 contiguous class C network
            '169.254.0.0|169.254.255.255', // Link-local address also referred to as Automatic Private IP Addressing
            '127.0.0.0|127.255.255.255' // localhost
        ];

        $long_ip = ip2long($ip);
        if ($long_ip != -1) {

            foreach ($pri_addrs as $pri_addr) {
                list ($start, $end) = explode('|', $pri_addr);

                if ($long_ip >= ip2long($start) && $long_ip <= ip2long($end)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}
