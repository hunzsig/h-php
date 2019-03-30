<?php
/**
 * @date: 2017/12/12
 */

namespace library;


class IOCode
{

    /* 公用信息 */
    //todo 以下方法所有字段必须一致，当前拥有三个字段 code response data
    //todo 即使某个字段无用，将其设为 null 也不应缺少字段，保持结构统一

    /**
     * @param $data
     * @param $response
     * @return array
     */
    public static function SUCCESS($data, $response)
    {
        return array(
            'code' => 200,
            'response' => $response,
            'data' => $data,
        );
    }

    /**
     * @param $data
     * @param $client
     * @param $response
     * @return array
     */
    public static function BROADCAST($data, $client, $response)
    {
        return array(
            'code' => 201,
            'response' => $response,
            'data' => $data,
            'client' => $client,
        );
    }

    /**
     * @param $data
     * @param $response
     * @return array
     */
    public static function GOON($data, $response)
    {
        return array(
            'code' => 202,
            'response' => $response,
            'data' => $data,
        );
    }

    /**
     * @param $response
     * @return array
     */
    public static function EXCEPTION($response)
    {
        return array(
            'code' => 400,
            'response' => $response,
            'data' => null
        );
    }

    /**
     * @param $response
     * @param null $data
     * @return array
     */
    public static function ERROR($response, $data = null)
    {
        return array(
            'code' => 401,
            'response' => $response,
            'data' => $data
        );
    }

    /**
     * @param $response
     * @return array
     */
    public static function NOT_FOUND($response)
    {
        return array(
            'code' => 404,
            'response' => $response,
            'data' => null
        );
    }

    /**
     * @param $response
     * @return array
     */
    public static function NOT_PERMISSION($response)
    {
        return array(
            'code' => 403,
            'response' => $response,
            'data' => null
        );
    }

}