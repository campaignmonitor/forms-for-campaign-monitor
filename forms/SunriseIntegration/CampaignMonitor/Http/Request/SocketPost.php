<?php
namespace SunriseIntegration\CampaignMonitor\Http\Request;

use SunriseIntegration\CampaignMonitor\Http\Request;
use SunriseIntegration\CampaignMonitor\IRequest;

/**
 * Sends a POST request, but makes use of fsockopen()
 * instead of get_file_contents(). This is to account for people who may be on
 * servers where allow_furl_open is disabled.
 */
class SocketPost implements IRequest
{
	const HOST = '';
    /**
     * @const string Bad request error
     */
    const BAD_REQUEST = '{"success": false, "error-codes": ["invalid-request"]}';

    /**
     * @const string Bad response error
     */
    const BAD_RESPONSE = '{"success": false, "error-codes": ["invalid-response"]}';

    /**
     * @var Socket
     */
    private $socket;


    public function __construct(Socket $socket = null)
    {
    	$this->socket = new Socket();
    }

	/**
	 * @param Request $request
	 *
	 * @return mixed|string
	 */
    public function send(Request $request)
    {
        $errno = 0;
        $errstr = '';

        if (false === $this->socket->fsockopen('ssl://' . self::HOST, 443, $errno, $errstr, 30)) {
            return self::BAD_REQUEST;
        }

        $content = $request->getBody();

        $request = "POST " . self::SITE_URL . " HTTP/1.1\r\n";
        $request .= "Host: " . self::HOST . "\r\n";
        $request .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $request .= "Content-length: " . strlen($content) . "\r\n";
        $request .= "Connection: close\r\n\r\n";
        $request .= $content . "\r\n\r\n";

        $this->socket->fwrite($request);
        $response = '';

        while (!$this->socket->feof()) {
            $response .= $this->socket->fgets(4096);
        }

        $this->socket->fclose();

        if (0 !== strpos($response, 'HTTP/1.1 200 OK')) {
            return self::BAD_RESPONSE;
        }

        $parts = preg_split("#\n\s*\n#Uis", $response);

        return $parts[1];
    }

	public function getLastRequest() {
		// TODO: Implement getLastRequest() method.
	}
}
