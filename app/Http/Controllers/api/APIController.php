<?php
/**
 * Created by PhpStorm.
 * User: christian.klemp
 * Date: 7/20/2015
 * Time: 3:50 PM
 */

namespace App\Http\Controllers\api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Requests_Session;

error_reporting(E_ALL);
ini_set( 'display_errors','1');


class APIController extends Controller {
    /**
     * Handle an API Token and Key generation request
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function generateUserAPIToken(Request $request) {
        $username = $request->header('IMT-Projects-Username');
        if (!isset($username)) {
            $username = $request->input('username');
        }

        $authKey = $request->header('IMT-Projects-Auth-Key');
        if (!isset($authKey)) {
            $authKey = $request->input('projects-auth-key');
        }

        $newToken = $this->generateNewAPIToken();
        $secretKey = $this->generateNewSecretKey();
        $user = User::where('username', $username)->first();
        if (isset($user) && ($user->auth_key == $authKey)) {
            $user->api_token = $newToken.';'.$secretKey;
            $user->save();
            $request->session()->put('user', $user);

            return $this->apiResponse(array(
                'user' => $user->toArray(),
                'api' => array(
                    'token' => $newToken,
                    'key' => $secretKey
                )
            ), 200, 'OK');
        }

        return $this->apiResponse(array(
            'user' => null,
            'api' => array(
                'token' => null,
                'key' => null
            )
        ), 400, 'Bad Request');
    }

    /**
     * Static method to get a new API token and key.
     *
     * @return string
     */
    public static function generateUserAPITokenAndKey() {
        $controller = new static;
        $token = $controller->generateNewAPIToken();
        $key = $controller->generateNewSecretKey();

        return $token . ';' . $key;
    }

    /**
     * Return a standard API response that includes the status, message and payload for every API call
     *
     * @param $payload
     * @param int $status
     * @param $message
     * @param array $headers
     * @param int $options
     * @return JsonResponse
     */
    public function apiResponse($payload, $status = 200, $message = null, $headers = array(), $options = 0) {
        if (!isset($message)) {
            $message = $this->generateStatusMessage($status);
        }

        return new JsonResponse(array('status' => $status, 'message' => $message, 'payload' => $payload), $status, $headers, $options);
    }

    /**
     * Return a Semantic UI API response that includes the payload and it's status
     *
     * @param $payload
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return JsonResponse
     */
    public function semanticUiResponse($payload, $status = 200, $headers = array(), $options = 0) {
        return new JsonResponse($payload, $status, $headers, $options);
    }

    /**
     * Returns the appropriate HTTP Status Verb for the provided status code.
     *
     * @param $status
     *
     * @return string
     */
    protected function generateStatusMessage($status) {
        $message = 'Bad Request';
        switch($status) {
            case 200:
                $message = 'OK';
                break;
            case 201:
                $message = 'Created';
                break;
            case 202:
                $message = 'Accepted';
                break;
            case 203:
                $message = 'Non-Authoritative Information';
                break;
            case 204:
                $message = 'No Content';
                break;
            case 205:
                $message = 'Reset Content';
                break;
            case 206:
                $message = 'Partial Content';
                break;
            case 300:
                $message = 'Multiple Choices';
                break;
            case 301:
                $message = 'Moved Permanently';
                break;
            case 302:
                $message = 'Found';
                break;
            case 303:
                $message = 'See Other';
                break;
            case 304:
                $message = 'Not Modified';
                break;
            case 305:
                $message = 'Use Proxy';
                break;
            case 306:
                $message = '(Unused)';
                break;
            case 307:
                $message = 'Temporary Redirect';
                break;
            case 400:
                $message = 'Bad Request';
                break;
            case 401:
                $message = 'Unauthorized';
                break;
            case 402:
                $message = 'Payment Required';
                break;
            case 403:
                $message = 'Forbidden';
                break;
            case 404:
                $message = 'Not Found';
                break;
            case 405:
                $message = 'Method Not Allowed';
                break;
            case 406:
                $message = 'Not Acceptable';
                break;
            case 407:
                $message = 'Proxy Authentication Required';
                break;
            case 408:
                $message = 'Request Timeout';
                break;
            case 409:
                $message = 'Conflict';
                break;
            case 410:
                $message = 'Gone';
                break;
            case 411:
                $message = 'Length Required';
                break;
            case 412:
                $message = 'Precondition Failed';
                break;
            case 413:
                $message = 'Request Entity Too Large';
                break;
            case 414:
                $message = 'Request-URI Too Long';
                break;
            case 415:
                $message = 'Unsupported Media Type';
                break;
            case 416:
                $message = 'Requested Range Not Satisfiable';
                break;
            case 417:
                $message = 'Expectation Failed';
                break;
            case 500:
                $message = 'Internal Server Error';
                break;
            case 501:
                $message = 'Not Implemented';
                break;
            case 502:
                $message = 'Bad Gateway';
                break;
            case 503:
                $message = 'Service Unavailable';
                break;
            case 504:
                $message = 'Gateway Timeout';
                break;
            case 505:
                $message = 'HTTP Version Not Supported';
                break;
        }

        return $message;
    }

    /**
     * Returns an array with the exception details to be returns in an apiResponse error.
     *
     * @param \Exception $exception
     * @return array
     */
    public function exceptionAPIError(\Exception $exception) {
        return array(
            'exception' => array(
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            )
        );
    }

    /**
     * Generates a new API Token
     *
     * @return string
     */
    protected function generateNewAPIToken() {
        return bin2hex(openssl_random_pseudo_bytes(26));
    }

    /**
     * Generates a new API Secret Key
     *
     * @return string
     */
    protected function generateNewSecretKey() {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * Finds the requesting user by their api tokens or username and authKey.
     *
     * @param Request $request
     * @return array|User
     */
    protected function findRequestingUserByAPITokens(Request $request) {
        try {
            $apiToken = $request->header(trans('api.auth.headers.token')) != null ? $request->header(trans('api.auth.headers.token')) : $request->input(trans('api.auth.input.apiToken'));
            $apiKey = $request->header(trans('api.auth.headers.key')) != null ? $request->header(trans('api.auth.headers.key')) : $request->input(trans('api.auth.input.apiKey'));

            $return = User::where('api_token', $apiToken . ';' . $apiKey)->first();
        } catch (\Exception $ex) {
        }

        if (!isset($return)) {
            try {
                $username = $request->header(trans('api.auth.headers.username')) != null ? $request->header(trans('api.auth.headers.username')) : $request->input(trans('api.auth.input.username'));
                $password = $request->header(trans('api.auth.headers.authKey')) != null ? $request->header(trans('api.auth.headers.authKey')) : $request->input(trans('api.auth.input.authKey'));

                $return = User::where(['username' => $username, 'auth_key' => $password])->first();
            } catch (\Exception $ex2) {
                $return = ['message' => trans('api.preference.project.user-not-found'), 'exception' => $ex, 'exception_2' => $ex2];
            }
        }

        return $return;
    }

    /**
     * Convert a string or an array of strings to their proper types.
     *
     * @param $options
     * @return array|bool|int|string
     */
    public function convertInput($options) {
        if (is_array($options)) {
            foreach ($options as $key => $option) {
                $options[$key] = $this->convertInput($option);
            }
        } elseif (is_string($options)) {
            $options = $options === 'true' ? true : $options;
            $options = $options === 'false' ? false : $options;
            $options = intval($options) > 0 && !is_bool($options) ? intval($options) : $options;
        }

        return $options;
    }

    /**
     * Return the provided content with escaped HTML tags for safe storage and display.
     *
     * @param $content
     *
     * @return mixed
     */
    public function escapeHTMLInput($content) {
        return str_replace('<style', '&lt;style',
            str_replace('</style', '&lt;/style',
                str_replace('<script', '&lt;script',
                    str_replace('</script', '&lt;/script',
                        str_replace('<link', '&lt;link',
                            str_replace('</link', '&lt;/link',
                                str_replace('</body', '&lt;/body',
                                    str_replace('</html', '&lt;/html',
                                        str_replace('<meta', '&lt;meta',
                                            nl2br($content))))))))));
    }

    /**
     * Creates a new Requests Session for server to server calls.
     *
     * @param $url
     * @return Requests_Session
     */
    protected function createRequestsSession($url) {
        return new \Requests_Session($url);
    }

    /**
     * Call IMTOnline Model Data Endpoint and returns DB data.
     *
     * @param array $data
     * @return array|mixed
     */
    public function getModelDataFromIMTOnline($data = array()) {
        $token = array_key_exists('script_token', $data) ? $data['script_token'] : null;
        $system = array_key_exists('for_system', $data) ? $data['for_system'] : null;
        $protocal = 'https://';
        $subdomain = 'www';
        $domain = '.imtins.com';
        $api = "/api/v1/imt-projects/conversion/?script_token=$token&for_system=$system";
        $apiURL = '';

        $requestHeaders = array('Accept' => 'application/json');

        $requestingUser = array_key_exists('user', $data) ?  $data['user'] : null;

        if (app()->environment('local') || app()->environment('dev')) {
            $subdomain = 'www';
        }

        if (app()->environment('test')) {
            $subdomain = 'www';
        }

        // Determine Cookie Name -- authKey || authKeyLuigi
        $cookieName = 'authKey';
        if ($subdomain !== 'www') {
            $cookieName = 'authKeyLuigi';
        }

        if (isset($requestingUser) && $requestingUser !== null) {
            $requestData = array(
                $cookieName => $requestingUser->getAuthKey()
            );
        } else {
            $requestData = $data;
        }

        $apiURL = $protocal . $subdomain . $domain . $api;

        if (!empty($requestData)) {
            $session = $this->createRequestsSession($apiURL);
            $session->headers['Accept'] = 'application/json';
            $options = array();
            $authKey = array_key_exists($cookieName, $requestData) ? $requestData[$cookieName] : '';
            $data['origin'] = 'imt-projects';

            if ($authKey !== '') {
                $cookie = new Requests_Cookie($cookieName, $authKey, [
                    'path' => '/',
                    'domain' => '.imtins.com',
                    'secure' => true
                ]);

                $cookieJar = new \Requests_Cookie_Jar([$cookieName => $cookie]);
                $options['cookies'] = $cookieJar;
            }

            if ($system === 'ACTIVITY' || $system === 'ITEMS' || $system === 'APPROVED_PROJECTS') {
                $options['timeout'] = 25000;
            } elseif ($system === 'ITEM_ASSIGNEES') {
                $options['timeout'] = 30000;
            }

            $request = $session->get($apiURL, $requestHeaders, $options);
            $applications = json_decode($request->body, true);

            if (is_array($applications) && array_key_exists('data', $applications) && array_key_exists('errors', $applications['data']) && !empty($applications['data']['errors'])) {
                return $applications['data'];
            }

            return is_array($applications) && array_key_exists('data', $applications) && array_key_exists($system, $applications['data']) ? $applications['data'][$system] : [];
        } else {
            return [
                'error' => 'Invalid Request',
                'message' => 'Cookie Not set in request data'
            ];
        }
    }

    public function getAssociatedApplications($data = array()) {
        if (!empty($data)) {
            $protocal = 'https://';
            $subdomain = 'www';
            $domain = '.imtins.com';
            $payload = array_key_exists('items', $data) ? $data['items'] : '';
            $token = 'Fdst4$oh^jafja*nnn820ps31';
            $api = "/api/v1/imt-projects/conversion/related/";
            $url = '';
            $parameters = array();

            $requestHeaders = array('Accept' => 'application/json');

            if (app()->environment('local') || app()->environment('dev')) {
                $subdomain = 'www';
            }

            if (app()->environment('test')) {
                $subdomain = 'www';
            }

            // Determine Cookie Name -- authKey || authKeyLuigi
            $cookieName = 'authKey';
            if ($subdomain !== 'www') {
                $cookieName = 'authKeyLuigi';
            }

            if (isset($requestingUser) && $requestingUser !== null) {
                $requestData = array(
                    $cookieName => $requestingUser->getAuthKey()
                );
            } else {
                $requestData = $data;
            }

            $apiURL = $protocal . $subdomain . $domain . $api;

            $parameters['origin'] = 'IMT_PROJECTS';

            $parameters['script_token'] = 'Fdst4$oh^jafja*nnn820ps31';

            $parameters['items'] = $payload;

            if (!empty($requestData)) {
                $session = $this->createRequestsSession($apiURL);
                $session->headers['Accept'] = 'application/json';
                $options = array();
                $authKey = array_key_exists($cookieName, $requestData) ? $requestData[$cookieName] : '';
                $data['origin'] = 'imt-projects';

                $options['timeout'] = 15000;

                if ($authKey !== '') {
                    $cookie = new Requests_Cookie($cookieName, $authKey, [
                        'path' => '/',
                        'domain' => '.imtins.com',
                        'secure' => true
                    ]);

                    $cookieJar = new \Requests_Cookie_Jar([$cookieName => $cookie]);
                    $options['cookies'] = $cookieJar;
                }

                $request = $session->post('', $requestHeaders, $parameters, $options);
                $associatedItems = json_decode($request->body, true);

                if (is_array($associatedItems) && array_key_exists('data', $associatedItems) && array_key_exists('errors', $associatedItems['data']) && !empty($associatedItems['data']['errors'])) {
                    return $associatedItems['data'];
                }

                return is_array($associatedItems) && array_key_exists('data', $associatedItems) ? $associatedItems['data'] : [];
            } else {
                return [
                    'error' => 'Invalid Request',
                    'message' => 'Cookie Not set in request data'
                ];
            }
        }
    }
}