<?php
define("SITE", "https://us20.api.mailchimp.com/3.0/");
define("API_KEY", "17df9eb12674bfc3fe815580a7244979-us20");

function list_create() {
    $data = array(
        "name" => "Tempsteve's List",
        "contact" => array(
           "company" => "Tempsteve",
           "address1" => "No. 87, Test Rd.",
           "city" => "Taipei",
           "state" => "TPE",
           "zip" => "100",
           "country" => "TW"
        ),
        "permission_reminder" => "This is a test.",
        "campaign_defaults" => array(
           "from_name" => "Steve Tsai",
           "from_email" => "tempsteve1024@gmail.com",
           "subject" => "",
           "language" => "en"
        ),
        "email_type_option" => true
    );
    $post_json = json_encode($data);
    $ch = curl_init(SITE."lists");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Authorization: apikey '.API_KEY
        )
    );
    $result = curl_exec($ch);
    curl_close($ch);
    $result_decode = json_decode($result, true);
    return $result_decode["id"];
}

function list_member_create($email, $list_id) {
    $data = array(
        'email_address' => $email,
        'status' => 'subscribed',
        'tags' => array('a tag')
    );
    $post_json = json_encode($data);
    $ch = curl_init(SITE."lists/".$list_id."/members");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Authorization: apikey '.API_KEY
        )
    );
    $result = curl_exec($ch);
    curl_close($ch);
    $result_decode = json_decode($result, true);
    if (isset($result_decode["email_address"])) {
        return true;
    } else {
        return false;
    }
}

function campaign_create($list_id) {
    $ch = curl_init(SITE."lists/".$list_id."/segments");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: apikey '.API_KEY
        )
    );
    $result = curl_exec($ch);
    curl_close($ch);
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
            'reply_to' => 'tempsteve1024@gmail.com',
            'from_name' => 'Steve Tsai'
        )
    );
    $post_json = json_encode($data);
    $ch = curl_init(SITE."campaigns");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Authorization: apikey '.API_KEY
        )
    );
    $result = curl_exec($ch);
    curl_close($ch);
    $result_decode = json_decode($result, true);
    return $result_decode["id"];
}

function campaign_content_update($campaign_id) {
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
    $ch = curl_init(SITE."campaigns/".$campaign_id."/content");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Authorization: apikey '.API_KEY
        )
    );
    curl_exec($ch);
    curl_close($ch);
}

function campaign_send($campaign_id) {
    $ch = curl_init(SITE."campaigns/".$campaign_id."/send-checklist");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: apikey '.API_KEY
        )
    );
    $result = curl_exec($ch);
    curl_close($ch);
    $result_decode = json_decode($result, true);

    if($result_decode["is_ready"] === true) {
        $ch = curl_init(SITE."campaigns/".$campaign_id."/actions/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: apikey '.API_KEY
            )
        );
        curl_exec($ch);
        curl_close($ch);

        return true;
    } else {
        echo $result."<br>";
        return false;
    }
}
?>
