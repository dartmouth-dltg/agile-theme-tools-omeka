<?php

namespace AgileThemeTools\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Wraps the standard "serverUrl" view helper to inject the public-facing
 * NodePort (agile_theme_tools.public_port). Every force_canonical URL in
 * Omeka core (Omeka\View\Helper\Url::__invoke()) and IiifServer funnels
 * through serverUrl(), which builds scheme://host purely from $_SERVER, so
 * it never carries the NodePort inside the pod. Unlike Omeka's own "url"
 * helper override, ServerUrl::__invoke() doesn't depend on getView(), so
 * wrapping it here is safe.
 */
class PortAwareServerUrl extends AbstractHelper
{
    protected $serverUrlHelper;

    protected $publicPort;

    public function __construct($serverUrlHelper, $publicPort)
    {
        $this->serverUrlHelper = $serverUrlHelper;
        $this->publicPort = $publicPort;
    }

    public function __invoke($requestUri = null)
    {
        $url = ($this->serverUrlHelper)($requestUri);

        if ($this->publicPort
            && is_string($url)
            && !preg_match('#^https?://[^/]+:\d+#', $url)
        ) {
            $url = preg_replace('#^(https?://[^/:]+)#', '$1:' . $this->publicPort, $url, 1);
        }

        return $url;
    }
}
