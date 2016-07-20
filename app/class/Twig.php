<?php
namespace EmailNewsletter;

class Twig {


    public function emailSubscribeForm(){
        return \Template::render('@emailnewsletter/subscribe-form');
    }//emailSubscribeForm



}//Twig
