<?php
include_once "api.php";
// Step 1: create a new list
$list_id = listCreate();
echo "List ".$list_id." created!<br>";

// Step 2: add my email to the list
$my_mail = "tempsteve@mail-apps.com";
if (listMemberCreate($my_mail, $list_id) === true) {
    echo $my_mail." added!<br>";
}

// Step 3: add another email addresses to the list
$email_list = array();
// Create some random email addresses
for ($i=0; $i < 10; $i++) {
    array_push($email_list, md5(mt_rand())."@abc.com");
}
foreach ($email_list as $email) {
    if (listMemberCreate($email, $list_id) === true) {
        echo $email." added!<br>";
    }
}

// Step 4: create a new campaign
$campaign_id = campaignCreate($list_id);
echo "Campaign ".$campaign_id." created!<br>";

// Step 5: edit campaign's content
campaignContentUpdate($campaign_id);

// Step 6: send a campaign email to all the members in the list
if (campaignSend($campaign_id) === true) {
    echo "Sent!<br>";
}
