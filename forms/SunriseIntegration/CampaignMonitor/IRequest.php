<?php
/**
 * This is a PHP library that handles calling reCAPTCHA.
 *
 * @copyright Copyright (c) 2015, Google Inc.
 * @link      http://www.google.com/recaptcha
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace SunriseIntegration\CampaignMonitor;

/**
 * Method used to send the request to the service.
 */
interface IRequest
{

	/**
	 * URL to which requests are sent.
	 * @const string
	 */
	const SITE_URL = 'https://api.createsend.com/api/v3.1';
	const METHOD_OPTIONS  = 'OPTIONS';
	const METHOD_GET      = 'GET';
	const METHOD_HEAD     = 'HEAD';
	const METHOD_POST     = 'POST';
	const METHOD_PUT      = 'PUT';
	const METHOD_DELETE   = 'DELETE';
	const METHOD_TRACE    = 'TRACE';
	const METHOD_CONNECT  = 'CONNECT';
	const METHOD_PATCH    = 'PATCH';
	const METHOD_PROPFIND = 'PROPFIND';

	public function getLastRequest();

	/**
	 * @param Http\Request $params
	 *
	 * @return mixed
	 */
    public function send(\SunriseIntegration\CampaignMonitor\Http\Request $params);
}
