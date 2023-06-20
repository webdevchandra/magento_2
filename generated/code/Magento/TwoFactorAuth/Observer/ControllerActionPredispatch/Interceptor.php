<?php
namespace Magento\TwoFactorAuth\Observer\ControllerActionPredispatch;

/**
 * Interceptor class for @see \Magento\TwoFactorAuth\Observer\ControllerActionPredispatch
 */
class Interceptor extends \Magento\TwoFactorAuth\Observer\ControllerActionPredispatch implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\TwoFactorAuth\Api\TfaInterface $tfa, \Magento\TwoFactorAuth\Api\TfaSessionInterface $tfaSession, \Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface $configRequestManager, \Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier $tokenManager, \Magento\Framework\App\ActionFlag $actionFlag, \Magento\Framework\UrlInterface $url, \Magento\Framework\AuthorizationInterface $authorization, \Magento\Authorization\Model\UserContextInterface $userContext)
    {
        $this->___init();
        parent::__construct($tfa, $tfaSession, $configRequestManager, $tokenManager, $actionFlag, $url, $authorization, $userContext);
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
