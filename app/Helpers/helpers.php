<?php
    function urlApiHotel(){
        $url = "http://127.0.0.1:8001/api/";
        return $url;
    }
    function urlApiResto(){
        $url = "http://127.0.0.1:8002/api/";
        return $url;
    }
    function rupiah($rp)
    {
        return number_format($rp,0,',','.');
    }
    function dmyhi($date)
    {
        return date('d-m-Y H:i', strtotime($date));
    }