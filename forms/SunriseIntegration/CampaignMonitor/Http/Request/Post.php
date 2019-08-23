<?php
namespace SunriseIntegration\CampaignMonitor\Http\Request;

use SunriseIntegration\CampaignMonitor\Http\Request;
use SunriseIntegration\CampaignMonitor\IRequest;

/**
 * Sends POST requests .
 */
class Post implements IRequest
{

	/**
	 * @param Request $request
	 *
	 * @return bool|mixed|string
	 */
    public function send(Request $request)
    {
        /**
         * PHP 5.6.0 changed the way you specify the peer name for SSL context options.
         * Using "CN_name" will still work, but it will raise deprecated errors.
         */
        $peer_key = version_compare(PHP_VERSION, '5.6.0', '<') ? 'CN_name' : 'peer_name';
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => $request->getBody(),
                // Force the peer to validate (not needed in 5.6.0+, but still works)
                'verify_peer' => true,
                $peer_key => 'www.domain.com',
            ),
        );
        $context = stream_context_create($options);
        return file_get_contents(self::SITE_URL, false, $context);
    }

	public function getLastRequest() {
		// TODO: Implement getLastRequest() method.
	}
}
