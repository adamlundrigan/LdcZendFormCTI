<?php

namespace LdcZendFormCTI;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return array();
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'formNonuniformCollection' => 'LdcZendFormCTI\View\Helper\FormNonuniformCollectionFactory',
            ),
        );
    }

    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        if (! $sm instanceof \Zend\ServiceManager\ServiceLocatorInterface) {
            return;
        }
        if (! $sm->has('ViewTemplateMapResolver')) {
            return;
        }

        $vr = $sm->get('ViewTemplateMapResolver');
        $vr->add('ldc-zend-form-cti-outer-container-template', __DIR__.'/../view/outer-container.phtml');
        $vr->add('ldc-zend-form-cti-instance-container-template', __DIR__.'/../view/instance-container.phtml');
    }
}
