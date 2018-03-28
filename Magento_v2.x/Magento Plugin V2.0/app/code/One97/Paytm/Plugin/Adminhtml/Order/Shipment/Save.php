<?php

namespace One97\Paytm\Plugin\Adminhtml\Order\Shipment;

use Psr\Log\LoggerInterface;
use Magento\Sales\Model\OrderFactory;

class Save
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var OrderFactory
     */
    protected $_order;

    /**
     * UpdateAttributes constructor.
     */
    public function __construct(
        OrderFactory $order,
        LoggerInterface $logger
    )
    {
        $this->_order  = $order;
        $this->_logger = $logger;
    }

    public function afterExecute(
        \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save $save
    )
    {
        $order = null;
        try {
            $order_id = $save->getRequest()->getParam('order_id');
            $order = $this->_order->create()->load($order_id);
        } catch (\Exception $e) {
        }
        if(!$order) {
            $this->_logger->debug("Order not found");
        } else {
            if (!$order->canUnhold() && !$order->canShip() && !$order->isCanceled() && !$order->canInvoice()) {
                if($order->getStatus() != $order::STATE_COMPLETE) {
                    $order->setStatus($order::STATE_COMPLETE);
                    $order->setState($order::STATE_COMPLETE);
                    $order->save();
                }

            }
        }
    }
}
