<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 04.07.2019
 * Time: 17:14
 */

namespace App\Packages;


class Firebase
{
    public static function send($to, $message) {
        $fields = array(
            'to' => $to,
            'data' => $message,
//            'notification' => $message,
        );
        return self::sendPushNotification($fields);
    }

    public static function sendMultiple(array $registration_ids, array $message) {
        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $message,
            'notification' => $message,
        );
        if (count($registration_ids) > 0 ){
            return self::sendPushNotification($fields);
        }
    }

    private static function sendPushNotification($fields) {


        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=AAAAAvA-Iuw:APA91bHCdNpZ2Cy39wAxjEWFrBcxE1W6YWpbeTY7qlbjm5LIdejn3yF3GZL348pm3LQxHbpLfo7jQykJTkVD4mkkjNL8WTZNtYovt4SefE1VsZSzaFNoQu553xEe0wq40hNO41qMBZIN',
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        // echo "Result".$result;
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);

        return $result;
    }
}
