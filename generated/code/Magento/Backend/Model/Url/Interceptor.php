<?php
namespace Magento\Backend\Model\Url;

/**
 * Interceptor class for @see \Magento\Backend\Model\Url
 */
class Interceptor extends \Magento\Backend\Model\Url implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Route\ConfigInterface $routeConfig, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\Url\SecurityInfoInterface $urlSecurityInfo, \Magento\Framework\Url\ScopeResolverInterface $scopeResolver, \Magento\Framework\Session\Generic $session, \Magento\Framework\Session\SidResolverInterface $sidResolver, \Magento\Framework\Url\RouteParamsResolverFactory $routeParamsResolverFactory, \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Url\RouteParamsPreprocessorInterface $routeParamsPreprocessor, $scopeType, \Magento\Backend\Helper\Data $backendHelper, \Magento\Backend\Model\Menu\Config $menuConfig, \Magento\Framework\App\CacheInterface $cache, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Framework\Encryption\EncryptorInterface $encryptor, \Magento\Store\Model\StoreFactory $storeFactory, \Magento\Framework\Data\Form\FormKey $formKey, array $data = [], ?\Magento\Framework\Url\HostChecker $hostChecker = null, ?\Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->___init();
        parent::__construct($routeConfig, $request, $urlSecurityInfo, $scopeResolver, $session, $sidResolver, $routeParamsResolverFactory, $queryParamsResolver, $scopeConfig, $routeParamsPreprocessor, $scopeType, $backendHelper, $menuConfig, $cache, $authSession, $encryptor, $storeFactory, $formKey, $data, $hostChecker, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function findFirstAvailableMenu()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'findFirstAvailableMenu');
        return $pluginInfo ? $this->___callPlugins('findFirstAvailableMenu', func_get_args(), $pluginInfo) : parent::findFirstAvailableMenu();
    }
}
