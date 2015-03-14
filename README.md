CakePHP ZSteam-API Plugin
===========================

## What is this?

This is a CakePHP 2 plugin that provides access to the SteamCommunity and SteamAPI as a datasource for use with your CakePHP application.

## Installation

1. Download and place into your plugin directory
2. Go to http://steamcommunity.com/dev/apikey and generate a Steam API key
3. Add the following to your __app/Config/bootstrap.php__ file
```php
CakePlugin::load('ZSteam-API');
```
4. Add the following to your __app/Config/database.php__ file, replacing the apiKey with your API Key from step 2

```php
public $steam = array(
    'datasource' => 'ZSteam-API.SteamApiSource',
    'host' => 'http://api.steampowered.com',
    'apiKey' => 'XXXXXXXXXXXXXXXX',
    'format' => 'json'
);

public $steamcomm = array(
    'datasource' => 'ZSteam-API.SteamCommunitySource',
    'host' => 'http://steamcommunity.com/',
    'format' => 'json'
);
```
## How to use

Using the Steam API documentation you can translate a request URL into a model for use by this plugin.

Steam API documentation can be found by going to: http://steamcommunity.com/dev

Using the below request URL as an example, you can take parts of the URL and assign them to the appropriate variables.

http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX&steamid=76561197960435530&relationship=friend

* 'api.steampowered.com' = Hostname is automatically passed from config
* 'ISteamUser' = $commandType
* 'GetFriendList' = $command 
* 'v0001' = $commandVersion
* 'key' =  Is automatically passed from the config
* 'steamid' and 'relationship' = Will need to be passed to the model as conditions

## Quick Example Models

Get user SteamID64 based on the profile id/profile name. This May be the only use for the SteamCommunitySource

```php
<?php
/**
 * GetSteamId model
 */
class GetSteamId extends AppModel
{
    public $useDbConfig = 'steamcomm';
    public $useTable = false;

    /**
     * Name of the API command.
     *
     * @var string
     */
    public $command = 'id';

    /**
     * Get Steam profile for steam account, this is usually called first
     * as we don't know the steamId64.
     *
     * @return array
     */
    public function getSteamId64()
    {

        $queryData['conditions']['query'] = 'userxyz';

        $searchResult = $this->find('all', $queryData);

        if ( isset($searchResult['GetSteamId']['response']['error']) ) {
            return array();
        }

        return $searchResult;
    }

}
```

Get the friends list for a steamid64 (Using a steam user's steamid64 key). This model uses the SteamApiSource.

```php
<?php
/**
 * GetFriendList model
 */
class GetFriendList extends AppModel
{
    public $useDbConfig = 'steam';
    public $useTable = false;

    /**
     * Name of the API command.
     *
     * @var string
     */
    protected $command = 'GetFriendList';

    /**
     * Steam API command category.
     *
     * @var string
     */
    protected $commandType = 'ISteamUser';

    /**
     * Version of the API command.
     *
     * @var string
     */
    protected $commandVersion = 'v0001';

    /**
     * Get List of Friends for SteamId64.
     *
     * @param $steamId64
     * @param string $relationship
     * @return array
     */
    public function getFriends($steamId64, $relationship = 'friend')
    {

        $queryData['conditions']['steamid'] = $steamId64;
        $queryData['conditions']['relationship'] = $relationship;

        return $this->find('all', $queryData);
    }

}
```