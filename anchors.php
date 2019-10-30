<?php
namespace Grav\Plugin;

use \Grav\Common\Plugin;
use \Grav\Common\Grav;
use \Grav\Common\Page\Page;

class AnchorsPlugin extends Plugin
{
    /**
     * @return array
     */
    protected $isDoc;
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize configuration
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->active = false;
        } else {
            $this->enable([
                'onPageInitialized' => ['onPageInitialized', 0],
                'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
                'onTwigExtensions' => ['onTwigExtensions', 0]
            ]);
        }
    }

    public function onTwigExtensions()
    {
        $config = $this->config->get('plugins.anchors.selectors');
        require_once(__DIR__ . '/twig/AnchorsTwigExtension.php');
        $this->grav['twig']->twig->addExtension(new AnchorsTwigExtension($config));
    }

    /**
     * Initialize configuration
     */
    public function onPageInitialized()
    {
        $defaults = (array) $this->config->get('plugins.anchors');

        /** @var Page $page */
        $page = $this->grav['page'];
        $this->isDoc = $this->filterByTemplate($page);
        if (isset($page->header()->anchors) ) {
            $this->config->set('plugins.anchors', array_merge($defaults, $page->header()->anchors));
        }
    }
    function filterByTemplate($page)
    {
//        var_dump($this->config->get('plugins.anchors'));
        return in_array($page->template(), $this->config->get('plugins.anchors.templates'));
    }

    /**
     * if enabled on this page, load the JS + CSS and set the selectors.
     */
    public function onTwigSiteVariables()
    {
        if ($this->config->get('plugins.anchors.active') && $this->isDoc) {
           
            $selectors = explode(',', $this->config->get('plugins.anchors.selectors', 'h1,h2,h3,h4'));
            $container = $this->config->get('plugins.anchors.container', 'body').' ';
//            $fun = function($val, $s){return "$s $val";};
            $prefixed_array = preg_filter('/^/', $container, $selectors);
            $containedSelectors = implode(',', $prefixed_array);
            $visible = "visible: '{$this->config->get('plugins.anchors.visible', 'hover')}',";
            $placement = "placement: '{$this->config->get('plugins.anchors.placement', 'right')}',";
            $icon = $this->config->get('plugins.anchors.icon') ? "icon: '{$this->config->get('plugins.anchors.icon')}'," : '';
            $class = $this->config->get('plugins.anchors.class') ? "class: '{$this->config->get('plugins.anchors.class')}'," : '';
            $truncate = "truncate: {$this->config->get('plugins.anchors.truncate', 64)}";
            $this->grav['assets']->addJs('plugin://anchors/js/jquery-scrolltofixed-min.js');
            $this->grav['assets']->addJs('plugin://anchors/js/anchors_grav.js');
            $this->grav['assets']->addJs('plugin://anchors/js/anchor.min.js');
            
            $anchors_init = "$(document).ready(function() {
                                anchors.options = {
                                    $visible
                                    $placement
                                    $icon
                                    $class
                                    $truncate
                                };
                                anchors.add('$containedSelectors');
//                                $('#sidebar').scrollToFixed({zIndex: 1000});
                                $('#page-toc').scrollToFixed({zIndex: 500});
                                document.getElementById('toc').appendChild(generateTableOfContents(anchors.elements));
                           });";
                             

            $this->grav['assets']->addInlineJs($anchors_init);
        }
    }
}
