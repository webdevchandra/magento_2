<?php
namespace Magento\User\Observer\Backend\ForceAdminPasswordChangeObserver;

/**
 * Interceptor class for @see \Magento\User\Observer\Backend\ForceAdminPasswordChangeObserver
 */
class Interceptor extends \Magento\User\Observer\Backend\ForceAdminPasswordChangeObserver implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\AuthorizationInterface $authorization, \Magento\User\Model\Backend\Config\ObserverConfig $observerConfig, \Magento\Backend\Model\UrlInterface $url, \Magento\Backend\Model\Session $session, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Framework\App\ActionFlag $actionFlag, \Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->___init();
        parent::__construct($authorization, $observerConfig, $url, $session, $authSession, $actionFlag, $messageManager);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute($observer);
    }
}
