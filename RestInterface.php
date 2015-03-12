<?php namespace Devtools;

interface RestInterface
{
    public function get($request = null);
    public function put($request = null);
    public function post($request = null);
    public function delete($request = null);

    function getCollection();
    function getMember();

    function putCollection();
    function putMember();

    function postCollection();
    function postMember();

    function deleteCollection();
    function deleteMember();
}
