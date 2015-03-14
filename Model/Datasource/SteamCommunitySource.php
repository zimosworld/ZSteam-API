<?php
App::uses('DboSource', 'Model/Datasource');
App::uses('HttpSocket', 'Network/Http');

class SteamCommunitySource extends DboSource
{

    /**
     * A description of the datasource
     */
    public $description = 'Steam Community API for retrieving steam user profile data';

    /**
     * Datasource config
     *
     * @var array
     */
    public $config = array();

    /**
     * Create our HttpSocket and handle any config tweaks.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->Http = new HttpSocket();
    }

    /**
     * listSources() is for caching. You'll likely want to implement caching in
     * your own way with a custom datasource. So just ``return null``.
     *
     * @param null $data
     * @return null
     */
    public function listSources($data = null)
    {
        return null;
    }

    /**
     * Need this or CakePHP will complain that it can't find it.
     */
    public function connect()
    {

    }

    /**
     * Each API call will have a different schema so we will return the model schema.
     *
     * @param Model|string $model
     * @return array
     */
    public function describe($model)
    {
        return $model->_schema;
    }

    /**
     * Reads data from datasource (The Steam API)
     *
     * @param Model $model
     * @param array $queryData
     * @param null $recursive
     * @return array|mixed
     */
    public function read(Model $model, $queryData = array(), $recursive = null)
    {
        App::uses('Xml', 'Utility');

        /**
         * Set URI details, over-writing any set elsewhere
         */
        $queryData['url'] = array(
            'host' => $this->config['host'],
            'command' => $model->command,
            'query' => $queryData['conditions']['query']
        );

        /**
         * Set the return format to XML.
         */
        $queryData['conditions']['xml'] = 1;

        /**
         * Send the request
         */
        $xmlString = $this->Http->get(
            implode('/', $queryData['url']),
            $queryData['conditions']
        );

        /**
         * Now we decode and return the remote data.
         */
        try {
            $res = Xml::toArray(Xml::build($xmlString->body));
        } catch (XmlException $e) {
            throw new CakeException($e);
        }

        return array($model->alias => $res);
    }

}