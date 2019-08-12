<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @author      Jan Kozak <galvani78@gmail.com>
 */

namespace MauticPlugin\MauticWassengerBundle\Wassenger;

use http\Client;
use http\Client\Request;
use http\Message\Body;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PageBundle\Model\TrackableModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\SmsBundle\Api\AbstractSmsApi;
use Monolog\Logger;

class WassengerApi extends AbstractSmsApi
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $originator;

    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var Http
     */
    private $http;

    /**
     * MessageBirdApi constructor.
     *
     * @param TrackableModel    $pageTrackableModel
     * @param PhoneNumberHelper $phoneNumberHelper
     * @param IntegrationHelper $integrationHelper
     * @param Logger            $logger
     */
    public function __construct(TrackableModel $pageTrackableModel, IntegrationHelper $integrationHelper, Logger $logger)
    {
        $this->logger = $logger;
        $this->integrationHelper = $integrationHelper;
        $this->client = new Client();
        parent::__construct($pageTrackableModel);
    }

    /**
     * @param Lead   $contact
     * @param string $content
     *
     * @return bool|mixed|string
     */
    public function sendSms(Lead $contact, $content)
    {
        if (!$contact->getMobile()) {
            return false;
        }

        $integration = $this->integrationHelper->getIntegrationObject('Wassenger');
        if ($integration && $integration->getIntegrationSettings()->getIsPublished()) {
            $data   = $integration->getDecryptedApiKeys();

            $request = new Request();
            $body = new Body();

            $body->append('{"phone":"'.$contact->getMobile().'","message":"'.$content.'"}');

            $request->setRequestUrl('https://api.wassenger.com/v1/messages');
            $request->setRequestMethod('POST');
            $request->setBody($body);

            $request->setHeaders(array(
                'token' => $data['AUTH_TOKEN'],
                'content-type' => 'application/json'
            ));

            $this->client->enqueue($request)->send();
            $response = $this->client->getResponse();

            if ($response->getBody()) {
                return true;
            }else{
                return false;
            }
        }
    }
}
