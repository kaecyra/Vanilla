<?php if (!defined('APPLICATION')) exit();

/**
 * SteamAPI Plugin
 * 
 * This plugin grants access to the Steam Web API.
 * 
 * Changes: 
 *  
 * 
 * @author Tim Gunter <gunter.tim@gmail.com>
 * @copyright 2012 Tim Gunter
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL
 * @package Addons
 */

$PluginInfo['SteamAPI'] = array(
   'Name' => 'Steam API Provider',
   'Description' => "Provide API hooks and authentication for the Steam Web API.",
   'Version' => '1.0a',
   'RequiredApplications' => array('Vanilla' => '2.1a'),
   'MobileFriendly' => TRUE,
   'Author' => "Tim Gunter",
   'AuthorEmail' => 'tim@vanillaforums.com',
   'AuthorUrl' => 'http://vanillaforums.com'
);

class SteamAPIPlugin extends Gdn_Plugin {
   
   public function __construct() {
      parent::__construct();
   }
   
   /**
    * Hook into profile menu
    * 
    * @param Gdn_Controller $Sender
    * @return type 
    */
   public function ProfileController_AfterAddSideMenu_Handler($Sender) {
      if (!Gdn::Session()->CheckPermission('Garden.SignIn.Allow'))
         return;
   
      $SideMenu = $Sender->EventArguments['SideMenu'];
      $ViewingUserID = Gdn::Session()->UserID;
      $SideMenu->AddLink('Options', T('Steam Connect'), '/profile/steamapi', FALSE);
   }
   
   /**
    * Controller provider
    * 
    * @param Gdn_Controller $Sender 
    */
   public function ProfileController_SteamAPI_Create($Sender) {
      $Sender->Permission('Garden.SignIn.Allow');
      $this->Dispatch($Sender);
   }
   
   /**
    * 
    * @param Gdn_Controller $Sender 
    */
   public function Controller_Index($Sender) {
      $Sender->Title('Steam Connect');
      $Sender->AddCssFile($this->GetResource('design/steamapi.css', FALSE, FALSE));
      $this->ProfileIntegration($Sender);
      $Sender->_SetBreadcrumbs(T('Steam Connect'), '/profile/steamapi');
      
      // Check if we're already authed
      
      $this->Render('settings');
   }
   
   /**
    * OpenID Authentication Dispatcher
    * 
    * @param Gdn_Controller $Sender 
    */
   public function Controller_Connect($Sender) {
      $Sender->Title('Steam Connect');
      $Sender->AddCssFile($this->GetResource('design/steamapi.css', FALSE, FALSE));

      // Build OpenID URL
      $Sender->SetData('SteamConnect', SteamAPI::GetOpenIDURL(Url('profile/steamapi/auth', TRUE)));
      
      $this->Render('connect');
   }
   
   /**
    * OpenID Authentication receiver
    * 
    * http://dev.vanilla.tim/profile/steamapi/auth?
    *    openid.ns=http://specs.openid.net/auth/2.0
    *    openid.mode=id_res
    *    openid.op_endpoint=https://steamcommunity.com/openid/login
    *    openid.claimed_id=http://steamcommunity.com/openid/id/76561197960331709
    *    openid.identity=http://steamcommunity.com/openid/id/76561197960331709
    *    openid.return_to=http://dev.vanilla.tim/profile/steamapi/auth
    *    openid.response_nonce=2012-05-13T23:10:05ZHLqm4QcWca8+ho7OXyeO3yJiFZ0=
    *    openid.assoc_handle=1234567890
    *    openid.signed=signed,op_endpoint,claimed_id,identity,return_to,response_nonce,assoc_handle
    *    openid.sig=7YuanDTYuc81BCtVKVrqfrvyFAA=
    * 
    * @param Gdn_Controller $Sender 
    */
   public function Controller_Auth($Sender) {
      $Sender->Title('Steam Connect');
      $Sender->AddCssFile($this->GetResource('design/steamapi.css', FALSE, FALSE));
      $this->ProfileIntegration($Sender);
      $Sender->_SetBreadcrumbs(T('Steam Connect'), '/profile/steamapi');
      
      $ValidAuthentication = SteamAPI::ValidateOpenID(Gdn::Request()->Get());
      
      if ($ValidAuthentication) {
         //$CommunityID64 = ;
      }
      
      $this->Render('auth');
   }
   
   protected function ProfileIntegration($Sender) {
      $Args = $Sender->RequestArgs;
      if (!is_array($Args)) $Args = array();
      
      if (sizeof($Args) < 3)
         $Args = array_merge($Args, array_fill(0,3 - sizeof($Args),0));
      
      $Args = array_slice($Args, 1, 2);
      list($UserReference, $Username) = $Args;
      
      $Sender->GetUserInfo($UserReference, $Username);
   }
   
   public function Setup() {
      
   }
   
}