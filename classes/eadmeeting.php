<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Video class.
 *
 * @package    mod_eadmeeting
 * @copyright  2019 Eduardo Kraus  {@link http://eadmeeting.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined ( 'MOODLE_INTERNAL' ) || die();

define ( "EADMEETING", true );

/**
 * Class eadmeeting.
 *
 * @copyright  2019 Eduardo Kraus  {@link http://eadmeeting.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class eadmeeting
{
    /**
     * Get token for redirect
     *
     * @param string $name
     * @param int    $cmid
     *
     * @return string
     * @throws dml_exception
     */
    public static function getplayerurl ( $name, $cmid, $isadmin )
    {
        $config = get_config ( 'eadmeeting' );
        $token  = self::gettoken ( $config, $name, $cmid, $isadmin );
        return "{$config->url}Embed/load/?token={$token}";
    }

    /**
     * Create room
     *
     * @param string $name
     * @param int    $cmid
     *
     * @throws dml_exception
     */
    public static function create ( $name, $cmid )
    {
        $config = get_config ( 'eadmeeting' );
        $token  = self::gettoken ( $config, $name, $cmid );
        self::load ( "{$config->url}api/Embed/create/?token={$token}" );
    }

    /**
     * Delete meeting
     *
     * @param string $name
     * @param int    $cmid
     *
     * @throws dml_exception
     */
    public static function delete ( $name, $cmid )
    {
        $config = get_config ( 'eadmeeting' );
        $token  = self::gettoken ( $config, $name, $cmid );
        self::load ( "{$config->url}api/Embed/delete/?token={$token}" );
    }

    private static function gettoken ( $config, $name, $cmid, $isadmin = false )
    {
        global $CFG, $USER, $PAGE;

        $userpicture       = new \user_picture( $USER );
        $userpicture->size = 1;
        $profileimageurl   = $userpicture->get_url ( $PAGE )->out ( false );

        $payload = array(
            "id"      => $cmid,
            "name"    => $name,
            "url"     => "moodle-{$cmid}",
            "referer" => "{$CFG->wwwroot}/mod/eadmeeting/view.php?id={$cmid}",

            "user_nome"    => fullname ( $USER ),
            "user_email"   => $USER->email,
            "user_avatar"  => $profileimageurl,
            "user_isadmin" => $isadmin ? 1 : 0,

            "expire" => time ()
        );

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $header  = base64_encode ( json_encode ( $header ) );
        $payload = base64_encode ( json_encode ( $payload ) );

        $signature = hash_hmac ( 'sha256', "{$header}.{$payload}", $config->token, true );
        $signature = base64_encode ( $signature );

        return $token = urlencode ( "{$header}.{$payload}.{$signature}" );
    }

    /**
     * Curl execution.
     *
     * @param string $url
     *
     * @return string
     */
    private static function load ( $url )
    {
        $ch = curl_init ();

        curl_setopt ( $ch, CURLOPT_URL, $url );

        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );

        $output = curl_exec ( $ch );
        curl_close ( $ch );

        return $output;
    }
}
