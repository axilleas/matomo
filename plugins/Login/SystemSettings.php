<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\Login;

use Piwik\Network\IP;
use Piwik\Settings\Setting;
use Piwik\Settings\FieldConfig;

/**
 * Defines Settings for Login.
 */
class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /** @var Setting */
    public $enableBruteForceDetection;

    /** @var Setting */
    public $whitelisteBruteForceIps;

    /** @var Setting */
    public $blacklistedBruteForceIps;

    /** @var Setting */
    public $maxFailedLoginsPerMinutes;

    /** @var Setting */
    public $loginAttemptsTimeRange;

    protected function init()
    {
        $this->enableBruteForceDetection = $this->createEnableBruteForceDetection();
        $this->maxFailedLoginsPerMinutes = $this->createMaxFailedLoginsPerMinutes();
        $this->loginAttemptsTimeRange = $this->createLoginAttemptsTimeRange();
        $this->blacklistedBruteForceIps = $this->createBlacklistedBruteForceIps();
        $this->whitelisteBruteForceIps = $this->createWhitelisteBruteForceIps();
    }

    private function createEnableBruteForceDetection()
    {
        return $this->makeSetting('enable_brute_force_detection', $default = true, FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = 'Enable Brute Force Detection';
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
        });
    }

    private function createWhitelisteBruteForceIps()
    {
        return $this->makeSetting('browsers', array(), FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = 'Never block any of the following IPs from logging in';
            $field->uiControl = FieldConfig::UI_CONTROL_TEXTAREA;
            $field->description = 'Enter one IP or one IP range per line';
        });
    }

    private function createBlacklistedBruteForceIps()
    {
        return $this->makeSetting('description', array(), FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $field->title = 'Never allow these IPs to log in';
            $field->uiControl = FieldConfig::UI_CONTROL_TEXTAREA;
            $field->description = 'Enter one IP or one IP range per line';
        });
    }

    private function createMaxFailedLoginsPerMinutes()
    {
        return $this->makeSetting('maxFailedLoginsPerMinutes', 25, FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = 'Number of allowed failed logins within the configured time range';
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = 'Enter one IP or one IP range per line';
        });
    }

    private function createLoginAttemptsTimeRange()
    {
        return $this->makeSetting('loginAttemptsTimeRange', 60, FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = 'Watch failed logins within this time range in minutes';
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = 'Enter a value in minutes';
        });
    }

    public function isWhitelistedIp($ipAddress)
    {
        $ip = IP::fromStringIP($ipAddress);

        $ips = $this->whitelisteBruteForceIps->getValue();
        if (empty($ips)) {
            return false;
        }
        return $ip->isInRanges($ips);
    }

    public function isBlacklistedIp($ipAddress)
    {
        $ip = IP::fromStringIP($ipAddress);

        $ips = $this->blacklistedBruteForceIps->getValue();
        if (empty($ips)) {
            return false;
        }

        return $ip->isInRanges($ips);
    }

}
