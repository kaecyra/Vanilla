<?php

/**
 * SteamAPI Tools
 * 
 * This class grants access to the Steam Web API Toolset for conversions and 
 * other convenience functions.
 * 
 * @author Tim Gunter <gunter.tim@gmail.com>
 * @copyright 2012 Tim Gunter
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL
 * @package Addons
 */
class SteamAPITools {
   
   const STEAM_INDIVIDUAL_IDENTIFIER   = '0x0110000100000000';
   const STEAM_GROUP_IDENTIFIER        = '0x0170000000000000';
   
   public static function SteamIDtoCommunityID($SteamID64, $AccountType = 'individual') {
      $Identifier = self::GetIdentifierFromAccountType($AccountType);
      if (is_null($Identifier))
         return NULL;
      
      $IdentifierValue = hexdec($Identifier);
      $Matched = preg_match("/STEAM_(\d+):(\d+):(\d+)/", $SteamID64, $MatchedIDParts);
      if (!$Matched) return NULL;
      $Universe = $MatchedIDParts[1];
      $AccountServer = $MatchedIDParts[2];
      $AccountNumber = $MatchedIDParts[3];
      
      $DoubleID = bcmul($AccountNumber, 2, 0);
      $NoParityID = bcadd($DoubleID, $IdentifierValue);
      $CommunityID64 = bcadd($NoParityID, $AccountServer);
      
      return $CommunityID64;
   }
   
   public static function CommunityIDtoSteamID($CommunityID64, $AccountType = 'individual') {
      $Identifier = self::GetIdentifierFromAccountType($AccountType);
      if (is_null($Identifier))
         return NULL;
      
      $IdentifierValue = hexdec($Identifier);
      $DoubleParityID = bcsub($CommunityID64, $IdentifierValue);
      
      $AccountServer = bcmod($DoubleParityID, 2);
      $DoubleID = bcsub($DoubleParityID, $AccountServer);
      $SteamID = bcdiv($DoubleID, 2, 0);
      $SteamID64 = sprintf('STEAM_0:%d:%d', $AccountServer, $SteamID);
      
      return $SteamID64;
   }
   
   public static function GetIdentifierFromAccountType($AccountType) {
      $AccountConstant = "STEAM_".strtoupper($AccountType)."_IDENTIFIER";
      if (defined("self::{$AccountConstant}"))
         return constant("self::{$AccountConstant}");
      return NULL;
   }
   
}