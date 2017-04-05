<?php
/**
 * Created by PhpStorm.
 * User: emilianozublena
 * Date: 17/3/17
 * Time: 6:51 PM
 */

namespace Sheetsu;

final class Sheetsu
{
    const BASE_URL = 'https://sheetsu.com/apis/v1.0/';
    private $connection;
    private $sheetId;
    private $sheetUrl;

    public function __construct($config=array()){
        $this->connection = new Connection($config);
        $this->setSheetId($config['sheetId']);
        $this->sheetUrl = self::BASE_URL.$this->sheetId;
    }

    public function setSheetId($sheetId){
        $this->sheetId = $sheetId;
    }

    public function read($limit=0, $offset=0){
        $connectionConfig = [
            'method'    => 'get',
            'url'       => $this->sheetUrl,
            'limit'     => $limit,
            'offset'    => $offset
        ];

        return $this->_setConnectionConfigAndMakeCall($connectionConfig);
    }

    public function search(array $conditions, $limit=0, $offset=0){
        $connectionConfig = [
            'method'    => 'get',
            'url'       => $this->sheetUrl.'/search',
            'params'    => $conditions,
            'limit'     => $limit,
            'offset'    => $offset
        ];

        return $this->_setConnectionConfigAndMakeCall($connectionConfig);
    }

    public function create($insertData) {
        $connectionConfig = [
            'method'    => 'post',
            'url'       => $this->sheetUrl
        ];

        if($insertData instanceof Collection) {
            $connectionConfig['params'] = '{"rows":'.$insertData->_prepareCollectionToJson().'}';
        }else {
            $connectionConfig['params'] = ['rows' => $insertData];
        }

        return $this->_setConnectionConfigAndMakeCall($connectionConfig);
    }

    public function update($columnName, $value, $updateData, $forcePutMethod=false) {
        $connectionConfig = [
            'method'    => $forcePutMethod===true ? 'put' : 'patch',
            'url'       => $this->sheetUrl.'/'.$columnName.'/'.$value
        ];

        if($updateData instanceof Model) {
            $connectionConfig['params'] = $updateData->_prepareModelAsJson();
        }else {
            $connectionConfig['params'] = $updateData;
        }

        return $this->_setConnectionConfigAndMakeCall($connectionConfig);
    }

    public function delete($columnName, $value){
        $connectionConfig = [
            'method'        => 'delete',
            'url'           => $this->sheetUrl.'/'.$columnName.'/'.$value
        ];

        return $this->_setConnectionConfigAndMakeCall($connectionConfig);
    }

    private function _setConnectionConfigAndMakeCall(array $connectionConfig){
        $this->connection->setConfig($connectionConfig);
        return $this->connection->makeCall();
    }
}