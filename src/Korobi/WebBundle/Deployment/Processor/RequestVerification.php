<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Verifies the request is authentic (i.e. from GitHub, or a super admin).
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class RequestVerification extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(DeploymentInfo $deploymentInfo) {
        $deploymentInfo->getRevision()->setDate(new \DateTime());

        $req = $deploymentInfo->getRequest();
        $signature = $this->getSignatureFromRequest($req);
        $isSuperUser = $this->isSuperUser($deploymentInfo->getAuthorisationChecker());
        $signatureVerified = $this->verifySignature($signature, $deploymentInfo->getHmacKey(), $req->getContent());
        $okayToProceed = $signatureVerified || $isSuperUser;

        $deploymentInfo->getRevision()->setManual($isSuperUser);

        if ($okayToProceed) {
            $this->logger->debug("Verified deployment request");
            return parent::handle($deploymentInfo);
        }
        $this->logger->debug("Rejecting unauthorised deployment request", ["signature" => $signatureVerified, "superuser" => $isSuperUser]);
        $deploymentInfo->addStatus(DeploymentStatus::UNAUTHORISED);
        return DeploymentStatus::UNAUTHORISED;
    }

    private function isSuperUser(AuthorizationChecker $authChecker) {
        return $authChecker->isGranted('ROLE_SUPER_ADMIN');
    }

    /**
     * Get the signature from the request, if available.
     *
     * @param Request $request
     * @return array|null|string
     */
    private function getSignatureFromRequest(Request $request) {
        $signature = $request->headers->get('X-Hub-Signature');
        if ($signature === null) {
            return null;
        }

        parse_str($signature, $output);
        $signature = array_key_exists('sha1', $output) ? $output['sha1'] : null;

        return $signature;
    }

    /**
     * Verify the signature with our secret.
     *
     * @param $signature
     * @param $secret
     * @param $data
     * @return bool
     */
    private function verifySignature($signature, $secret, $data) {
        /** @noinspection PhpUndefinedFunctionInspection */ // hash_equals (PHP 5 >= 5.6.0) - PhpStorm complains about an undefined function
        return $signature !== null ? hash_equals(hash_hmac('sha1', $data, $secret), $signature) : false;
    }
}
