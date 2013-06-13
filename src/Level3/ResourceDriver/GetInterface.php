<?php
namespace Level3\ResourceDriver;

interface GetInterface {
    public function getOne($id);
    public function get();
}