<?php
/**
  *
  * @author Matt Gross <contact@mattgross.net>
  * @link https://github.com/MatthewGross/PHP-Country-Block
  * @license MIT - http://mattgross.mit-license.org/
  *
  * A basic PHP IP Address blocker for certain countries provided by the user.
  * Uses IP Info DB <http://ipinfodb.com/>
  * Uses IP Info DB PHP Wrapper <http://github.com/beingtomgreen/IP-User-Location>
  *
  */

class countryBlock {

  // Store Country
  private $countries = null;
  
  // Store IPInfoDB API Key
  private $api_key = null;
  
  // Store Visitors IP Address
  private $ip_address = null;
  
  /**
    * __construct
    *
    * @param array $countries - All countries provided in an array
    * @param string $apiKey - Your API key
    * @param string $path_to_script - Directory path before the Uses IP Info DB PHP Wrapper
    *
    * @return true/false - true if ip has been blocked, false if ip has not been blocked and is permitted.
    * 
    */
  function __construct($countries, $api_key, $path_to_script = null)
  {
    // Include ipInfo.inc.php with or without path
    if($path_to_script)
    {
      include($path_to_script.'ipInfo.inc.php');
    }
    else
    {
      include('ipInfo.inc.php');
    }
    
    // new ipInfo class with api_key from parameters
    $ipInfo = new ipInfo($api_key);
  
    // Save Countries to $countries
    $this->country = $countries;
    
    // Save IPInfoDB API Key to $api_key
    $this->api_key = $api_key;
    
    // Bind Visitors IP Address to variable
    $ip_address = $ipInfo->getIPAddress();
    
    // Save IP Address ($ip_address) to $ip_address
    $this->ip_address = $ip_address;
    
    // Check if cookie exists
    if(cookieCheck())
    {
      // returned true... cookie does not exist
      foreach($countries as $country)
      {
        // return true or false
        $blockable = $this->countryCheck($c);
        // check
        if($blockable)
        {
          // block
          $this->setCookie();
          return true;
        }
        else
        {
          // don't block
          return false;
        }
      }
    }
    else
    {
      // don't block.. Already blocked...
      return true;
    }
  }
  
  /**
    * getCountry()
    *
    * Returns true or false based off if the country from the ip is in the $countries array
    *
    * @param string $country - country code of one country!
    *
    * @return true/false - true if same, false if not
    *
    */
  function countryCheck($country)
  {
    // Get IP Address from local variable
    $ip_address = $this->ip_address;
    
    // Get Country from ipInfo API
    $userCountry = $ipInfo->getCountry($ip_address);
    
    // Find Country Code!
    $countryCode = $userCountry['countryCode'];
    
    // Compare...
    if($country == $countryCode)
    {
      // Should block
      return true;
    }
    else
    {
      // Shouldn't block
      return false;
    }
  }
  
  /**
    * cookieCheck()
    *
    * Returns true or false depending if the cookie ('ip_not_allowed') is set or not
    *
    * @return true/false - true if not set, false if is set
    *
    */
  function cookieCheck()
  {
    // Get IP Address from local variable
    $ip_address = $this->ip_address;
    
    if(!isset($_COOKIE['ip_not_allowed']))) {
      return true;
    }
    else {
      return false;
    }
  }
  
  /**
    * cookieCheck()
    *
    * Sets cookie and returns true
    *
    * @return true
    *
    */
  function setCookie()
  {
    setcookie('ip_not_allowed', 'true');
    $_COOKIE['ip_not_allowed'] = 'true';
    return true;
  }

}
