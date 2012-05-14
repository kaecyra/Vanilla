<?php

/**
 * SteamAPI
 * 
 * This class grants access to the Steam Web API.
 * 
 * @author Tim Gunter <gunter.tim@gmail.com>
 * @copyright 2012 Tim Gunter
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL
 * @package Addons
 */
class SteamAPI {
   
   const OPENID_PROVIDER = 'https://steamcommunity.com/openid/login';
   const PROFILE_URL = '';
   
   /**
    * ProxyRequest object for connecting to steam web API
    * @var ProxyRequest
    */
   protected $SteamAPI;
   
   /**
    * List of steam profiles, cached in memory
    * @var array
    */
   protected $SteamProfileCache;
   
   public function __construct() {
      $this->SteamAPI = new ProxyRequest(FALSE, array(
          'ConnectTimeout'       => 5,
          'Timeout'              => 60,
          'Redirects'            => TRUE,
          'SSLNoVerify'          => TRUE,
          'PreEncodePost'        => TRUE,
          'Cookies'              => FALSE
      ));
      
      $this->SteamProfilesCache = array();
   }
   
   public function GetProfile() {
      
   }
   
   public function CacheProfileInfo($Community64ID) {
      
   }
   
   public static function GetOpenIDURL($AuthenticationURL) {
      // Build OpenID request
      $OpenID = array(
         'openid.claimed_id'  => 'http://specs.openid.net/auth/2.0/identifier_select',
         'openid.identity'    => 'http://specs.openid.net/auth/2.0/identifier_select',
         'openid.mode'        => 'checkid_setup',
         'openid.ns'          => 'http://specs.openid.net/auth/2.0',
         'openid.realm'       => Url('/', TRUE),
         'openid.return_to'   => $AuthenticationURL
      );

      // Final OpenID URL
      return SteamAPI::OPENID_PROVIDER.'?'.http_build_query($OpenID);
   }
   
   public static function ValidateOpenID($Query) {
      
      $CheckOpenID = array(
         'openid.assoc_handle', 
         'openid.claimed_id', 
         'openid.identity', 
         'openid.ns',
         'openid.op_endpoint',
         'openid.response_nonce',
         'openid.return_to',
         'openid.sig',
         'openid.signed'
      );
      
      $ValidationQuery = array(
         'openid.mode'  => 'check_authentication'
      );
      foreach ($CheckOpenID as $CheckField)
         $ValidationQuery[$CheckField] = GetValue(str_replace('.', '_', $CheckField), $Query);

      // Loading up all of our previously collected values into an array and sending them off for verificiation
      $AuthCheckRequest = new ProxyRequest();
      $CheckAuthResult = $AuthCheckRequest->Request(array(
         'URL'       => SteamAPI::OPENID_PROVIDER,
         'Method'    => 'GET',
         'Cookies'   => FALSE
      ), $ValidationQuery);
      
      if (stristr($CheckAuthResult, 'is_valid:true') !== FALSE) return TRUE;
      return FALSE;
   }
   
}