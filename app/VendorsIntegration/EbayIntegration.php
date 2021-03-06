<?php
namespace App\VendorsIntegration;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use App\VendorsIntegration\VendorsIntegrationInterface;
use App\VendorsIntegration\Exceptions\ItemNotFoundException;
use App\VendorsIntegration\Exceptions\VendorNotFoundException;
use App\VendorsIntegration\Responses\ItemResponses\EbayItemResponse;
use App\VendorsIntegration\Exceptions\VendorAuthenticationFailedException;

class EbayIntegration implements VendorsIntegrationInterface
{
    /**
     * Unique idenifier to the current integration in the database
     *
     * @var string
     */
    protected $key;

    /**
     * Ebay Integration config
     *
     * @var array
     */
    protected $config;

    /**
     * Http client
     *
     * @var mixed
     */
    protected $httpClient;

    /**
     * the bearer for the request to ebay api
     *
     * @var string
     */
    protected $bearer;

    /**
     * Ebay Integration constructor
     *
     * @param string     $key
     * @param HttpClient $client 
     */
    public function __construct($key, HttpClient $client)
    {
        $this->key = $key;
        $this->config = $this->getConfig();
        $this->httpClient = $client;

        $this->authRequest();
    }

    /**
     * Authentication request to ebay
     *
     * @return void
     */
    protected function authRequest()
    {
        try {
            $response = $this->httpClient->request('post', 'https://api.ebay.com/identity/v1/oauth2/token', [
                'auth' => [ $this->config['clientId'], $this->config['clientSecret'] ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://api.ebay.com/oauth/api_scope'
                ]
            ]);

            $this->bearer = json_decode($response->getBody()->getContents())->access_token;

        } catch (RequestException $e) {
            throw new VendorAuthenticationFailedException();
        }
    }

    /**
     * get the item data from ebay id
     *
     * @param  integer $itemId
     * @return EbayItemResponse
     */
    public function getItemById($itemId)
    {
        try {
            $response = $this->httpClient->request(
                'get', "https://api.ebay.com/buy/browse/v1/item/get_items_by_item_group?item_group_id={$itemId}", [
                    'headers' => [
                        'Authorization' => "Bearer {$this->bearer}",
                        'X-EBAY-C-ENDUSERCTX' =>
                            "affiliateCampaignId={$this->config['affId']},affiliateReferenceId=website"
                    ],
                ]
            );

            return new EbayItemResponse(json_decode($response->getBody()->getContents()));

        } catch (RequestException $e) {
            throw new ItemNotFoundException();
        }
    }

    /**
     * get the config of the vendor
     *
     * @param  integer $vendorKey
     * @return array
     */
    protected function getConfig()
    {
        if (!$config = config("vendors-integrations.{$this->key}")) {
            throw new VendorNotFoundException("the vendor key '{$this->key}' doesn't have config");
        }

        return $config;
    }

}
