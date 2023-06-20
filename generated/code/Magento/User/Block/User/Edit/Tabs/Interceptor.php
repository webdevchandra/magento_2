<?php
namespace Magento\User\Block\User\Edit\Tabs;

/**
 * Interceptor class for @see \Magento\User\Block\User\Edit\Tabs
 */
class Interceptor extends \Magento\User\Block\User\Edit\Tabs implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Backend\Model\Auth\Session $authSession, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toHtml');
        return $pluginInfo ? $this->___callPlugins('toHtml', func_get_args(), $pluginInfo) : parent::toHtml();
    }
}
