<?php
/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @author      Jan Kozak <galvani78@gmail.com>
 */

namespace MauticPlugin\MauticWassengerBundle\Wassenger;

use Joomla\Http\Http;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PageBundle\Model\TrackableModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\SmsBundle\Api\AbstractSmsApi;
use Monolog\Logger;

class WassengerApi extends AbstractSmsApi
{

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
     * @param IntegrationHelper $integrationHelper
     * @param Logger            $logger
     * @param Http|null         $http
     */
    public function __construct(
        TrackableModel $pageTrackableModel,
        IntegrationHelper $integrationHelper,
        Logger $logger,
        Http $http = null
    ) {
        $this->logger            = $logger;
        $this->integrationHelper = $integrationHelper;
        $this->http              = $http;
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
            $data              = $integration->getDecryptedApiKeys();
            $input             = [];
            $input['reference'] = $contact->getId();
            $input['phone']   = $contact->getMobile();
            $input['message'] = $content;
            $headers           = [
                'token'        => $data['AUTH_TOKEN'],
                'content-type' => 'application/json',
            ];
            $response          = $this->http->post('https://api.wassenger.com/v1/messages', json_encode($input), $headers);
            $body = json_decode($response->body, true);
            print_r($body);
            if ($response->code=== 201) {
                return true;
            } elseif(isset($body['message'])) {
                return $body['message'];
            }
            return false;
        }
    }
}
