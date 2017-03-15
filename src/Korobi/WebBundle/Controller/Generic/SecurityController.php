<?php

namespace Korobi\WebBundle\Controller\Generic;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Util\Akio;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController {
    const CACHE_FILE = 'csp_cache.json';

    /**
     * @var Akio
     */
    private $akio;

    /**
     * @var LoggerInterface Logger we can use.
     */
    private $logger;

    /**
     * @var String Last hash reported
     */
    protected $lastHashReported;

    /**
     * @var int Every time an identical hash gets reported, this value is increased.
     */
    protected $lastHashIdenticalReportCount = 0;

    /**
     * @param Akio $akio
     * @param LoggerInterface $logger
     */
    public function __construct(Akio $akio, LoggerInterface $logger) {
        $this->akio = $akio;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reportCspAction(Request $request) {
        $payload = json_decode($request->getContent(), true);
        if ($payload === null) {
            $payload = [];
        }
        $uri = isset($payload['csp-report']['document-uri']) && $payload['csp-report']['document-uri'] != ''
            ? $this->stripChannelKey($payload['csp-report']['document-uri'])
            : '[uri]';

        $resource = isset($payload['csp-report']['blocked-uri']) && $payload['csp-report']['blocked-uri'] != ''
            ? $payload['csp-report']['blocked-uri']
            : '[resource]';
        $directive = $payload['csp-report']['violated-directive'];

        $this->logger->warning('CSP Warning', $payload);
        $ip = $request->getClientIp();
        $hash = hash_hmac('sha1', $ip .  $resource . $directive, 'bc604aedc9027a1f1880');

        if ($this->shouldReportCspAction($hash)) {
            $amount = (int) $this->lastHashIdenticalReportCount + 1;
            $text = ' ' . $amount . ($amount == 1 ? ' request' : ' requests') . ' to ' . $resource . ' on page ' . $uri ' blocked via ' . $ip . '.';

            $this->akio->message()
                ->red()
                ->text('[!! CSP !!]')
                ->aquaLight()
                ->text($text)
                ->send('csp', 'private');
        }

        return new JsonResponse('Thanks, browser.');
    }

    /**
     * @param Request $req
     * @return Response
     */
    public function showRedirectAction(Request $req) {
        $response = new Response($this->renderView('KorobiWebBundle:error:unexpected-redirect.html.twig', [
            'url' => $req->get('redirUrl'),
        ]), 403);
        return $response;
    }

    public function safelyReturnScriptSource(Request $req) {
        $resp = $this->render("KorobiWebBundle:partial:analytics.js.twig");
        $resp->headers->set("Content-Type", "application/javascript");
        return $resp;
    }

    /**
     * @param String $uri
     * @return String
     */
    protected function stripChannelKey($uri) {
        if (strpos($uri, '?key=') === false)
            return $uri;

        $repl = preg_replace('/\?key=.*$/', '',  $uri);

        if ($repl === null)
            return '[uri snipped]';
        return $repl;
    }

    /**
     * @param String $hash
     * @return bool Report message or not
     */
    protected function shouldReportCspAction($hash) {
        if ($this->lastHashReported === null) {
          $this->loadHashCache();
        }

        if ($hash === $this->lastHashReported) {
            $this->lastHashIdenticalReportCount++;
        } else {
            $this->lastHashReported = $hash;
            $this->lastHashIdenticalReportCount = 0;
        }

        $this->cacheHashData();
        return $this->lastHashIdenticalReportCount % 10 == 0;
    }

    /**
     * @return bool successfully reloaded cache or not
     */
    protected function loadHashCache() {
        $cachePath = $this->getCacheFile();
        if (!file_exists($cachePath) || !is_readable($cachePath)) return false;

        $content = file_get_contents($cachePath);
        if ($content === false) return false;

        $cache = unserialize($content);
        if ($cache === false) return false;

        $this->lastHashReported = $cache['hash'];
        $this->lastHashIdenticalReportCount = $cache['hash_count'];
        return true;
    }

    /**
     * @return bool successfully cached data or not
     */
    protected function cacheHashData() {
        if (!$this->lastHashReported) return false;
        $cachePath = $this->getCacheFile();

        $contents = serialize([
            'hash' => $this->lastHashReported,
            'hash_count' => $this->lastHashIdenticalReportCount,
        ]);

        $success = file_put_contents($cachePath, $contents);
        return $success !== false;
    }

    /**
     * @return String path to cache file
     */
    protected function getCacheFile() {
        return $this->get('kernel')->getCacheDir() . DIRECTORY_SEPARATOR . self::CACHE_FILE;
    }
}
