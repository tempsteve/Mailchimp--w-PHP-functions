<?php
define("API_KEY", "Your API KEY");
$server = explode("-", API_KEY);
define("SITE", "https://".$server[1].".api.mailchimp.com/3.0/");

function curl($method, $route, $postData) {
    $ch = curl_init(SITE.$route);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($method == "POST") {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: apikey '.API_KEY
            )
        );
    } elseif ($method == "PUT") {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: apikey '.API_KEY
            )
        );
    } elseif ($method == "GET") {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: apikey '.API_KEY));
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function listCreate() {
    $data = array(
        "name" => "Your List",
        "contact" => array(
           "company" => "YourCompany",
           "address1" => "No. 87, Test Rd.",
           "city" => "Taipei",
           "state" => "TPE",
           "zip" => "100",
           "country" => "TW"
        ),
        "permission_reminder" => "This is a test.",
        "campaign_defaults" => array(
           "from_name" => "Your Name",
           "from_email" => "YourEmail@mail.com",
           "subject" => "",
           "language" => "en"
        ),
        "email_type_option" => true
    );
    $post_json = json_encode($data);

    $result = curl("POST", "lists", $post_json);
    $result_decode = json_decode($result);

    if (isset($result_decode->{"id"})) {
        return $result_decode->{"id"};
    } else {
        return false;
    }
}

function listMemberCreate($email, $list_id) {
    $data = array(
        'email_address' => $email,
        'status' => 'subscribed',
        'tags' => array('a tag')
    );
    $post_json = json_encode($data);

    $result = curl("POST", "lists/".$list_id."/members", $post_json);
    $result_decode = json_decode($result);

    if (isset($result_decode->{"email_address"})) {
        return true;
    } else {
        return false;
    }
}

function campaignCreate($list_id) {
    $result = curl("GET", "lists/".$list_id."/segments", "");
    $result_decode = json_decode($result);
    $segment_id = $result_decode->{"segments"}[0]->{"id"};

    $data = array(
        'type' => 'regular',
        'recipients' => array(
            'list_id' => $list_id,
            'segment_opts' => array(
                'saved_segment_id' => $segment_id
            )
        ),
        'settings' => array(
            'subject_line' => 'The Mailchimp Test Campaign',
            'reply_to' => 'YourEmail@mail.com',
            'from_name' => 'Your Name'
        )
    );
    $post_json = json_encode($data);

    $result = curl("POST", "campaigns", $post_json);
    $result_decode = json_decode($result);

    if (isset($result_decode->{"id"})) {
        return $result_decode->{"id"};
    } else {
        return false;
    }
}

function campaignContentUpdate($campaign_id) {
    $content = "Lorem ipsum dolor sit amet, consectetur adipisicing elit,
        sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
         nisi ut aliquip ex ea commodo consequat.
         Duis aute irure dolor in reprehenderit in voluptate velit esse
         cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat
         cupidatat non proident, sunt in culpa qui officia deserunt
         mollit anim id est laborum.";
    $data = array('html' => $content);
    $post_json = json_encode($data);

    $result = curl("PUT", "campaigns/".$campaign_id."/content", $post_json);
}

function campaignSend($campaign_id) {
    $result = curl("GET", "campaigns/".$campaign_id."/send-checklist", "");
    $result_decode = json_decode($result);

    if ($result_decode->{"is_ready"} === true) {
        $result = curl("POST", "campaigns/".$campaign_id."/actions/send", "");
        return true;
    } else {
        echo $result."<br>";
        return false;
    }
}
?>
