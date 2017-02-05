<?php
namespace \DavidFricker\DataAbstracter;
/*
    MAKE THIS A SINGELTON OBJECT SO THAT WE DONT NEED TO USE UGLY DEPENDACY INJECTION
 */
// connection should be made in constructer
class ContentProvider implements InterfaceContentProvider {
    public function __construct() {
    	
    }
}